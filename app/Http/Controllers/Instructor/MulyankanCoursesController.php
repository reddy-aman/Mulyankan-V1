<?php
namespace App\Http\Controllers\Instructor; // Use only one namespace, depending on your directory structure

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredMail;
use App\Models\Course;
use App\Models\Roster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Import the DB facade

class MulyankanCoursesController extends Controller
{
    public function index()
    {
        $terms = DB::table(table: 'attributes')->where('type', 'term')->pluck('value', 'id');
        $years = DB::table('attributes')->where('type', 'year')->pluck('value', 'id');
        $departments = DB::table('attributes')->where('type', 'department')->pluck('value', 'id');

        $Course = DB::table(table: 'courses')->where('instructor_id', auth()->id())->get();

        // $aman = "Some value from showCourses";

        //dd($Course);

        $role = auth()->user()->getRoleNames()->first(); // Get the first role

        return view('instructor.create-courses', compact('role', 'terms', 'years', 'departments', 'Course'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_description' => 'required|string',
            //'course_number' => 'required|string|max:6'
        ]);

        // Check if entry_code checkbox is checked
        if ($request->has('entry_code') && $request->entry_code == 1) {
            // Generate entry_code when checkbox is checked
            $entryCode = Course::generateUniqueCode();
        } else {
            // Set entry_code to null if not checked (ensure it's allowed in DB schema)
            $entryCode = null; // or a default unique value (such as a placeholder string if required)
        }

        // Create the course

        $course = Course::create([
            'course_number' => $request->course_number,
            'entry_code' => $entryCode,
            'course_name' => $request->course_name,
            'course_description' => $request->course_description,
            'term' => $request->term,
            'year' => $request->year,
            'department' => $request->department,
            'instructor_id' => Auth::id(), // assuming instructor is the current authenticated user
        ]);

        return redirect()->route('instructor.create-courses')->with('success', 'Course created successfully !')->with('error', 'Some error occurs !');

    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_description' => 'required|string',
        ]);

        $course->update([
            'course_name' => $request->course_name,
            'course_description' => $request->course_description,
            'term' => $request->term,
            'year' => $request->year,
            'department' => $request->department,
        ]);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    // show courses for an instructor
    public function show($id)
    {
        $course = Course::where('id', $id)->firstOrFail();
        session(['last_opened_course' => $id]);
        return view('instructor.show', compact('course'));
    }

    // show the members associated to the course
    public function showRoster(Request $request, $id)
    {
        $course = DB::table('courses')->where('id', $id)->first();

        // Ensure course exists and the current user is authorized
        if (!$course || $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized Access');
        }

        // Get filtering parameters
        $filterRole = $request->input('role', 'all');

        // Retrieve the primary instructor from the courses table
        $instructors = DB::table('users')
            ->join('courses', 'users.id', '=', 'courses.instructor_id')
            ->where('courses.id', $id)
            ->select('users.name', 'users.email', 'users.id')
            ->get();

        $primaryInstructor = $instructors->first();
        $pri_instructor_email = $primaryInstructor ? $primaryInstructor->email : null;
        
        if ($filterRole !== 'all' && $filterRole !== 'instructors') {
            $instructors = collect();
        }
        $queryInstructors = Roster::instructors($id);
        $queryStudents = Roster::students($id);
        $queryTA = Roster::TAs($id);

        // Based on the filter role, determine which groups to retrieve
        if ($filterRole !== 'all') {
            if ($filterRole === 'instructors') {
                $instructors_oth = $queryInstructors->get();
                $students = collect();
                $ta = collect();
            } elseif ($filterRole === 'students') {
                $students = $queryStudents->get();
                $instructors_oth = collect();
                $ta = collect();
            } elseif ($filterRole === 'TA') {
                $ta = $queryTA->get();
                $students = collect();
                $instructors_oth = collect();
            }
        } else {
            $instructors_oth = $queryInstructors->get();
            $students = $queryStudents->get();
            $ta = $queryTA->get();
        }

        $allInstructors = collect($instructors)->merge($instructors_oth)->values();
        return view('instructor.course-roster', compact('course', 'allInstructors', 'students', 'ta', 'pri_instructor_email'));
    }


    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:roster',
            'role' => 'required|in:1,2,3',
            'sid' => 'nullable|string|max:255',
        ]);

        // Retrieve the last opened course ID from the session
        $course_id = session('last_opened_course');
        $role = $request->role;

        if (!$course_id) {
            return redirect()->back()->with('error', 'No course selected.');
        }

        $user = DB::table('users')->where('email', $request->email)->first();

        Roster::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => $user ? $user->id : null,
            'course_id' => $course_id,
            'role' => $role,
            'sid' => $request->sid,
        ]);

        $course = Course::where('id', $course_id)->firstOrFail();
        if ($request->has('notify_user')) {
            // Prepare email data
            $emailData = [
                'name' => $request->name,
                'course_number' => $course->course_number,
                'course_name' => $course->course_name,
                'role' => $role,
                'registered' => $user ? true : false,
            ];

            // Send email
            Mail::to($request->email)->send(new UserRegisteredMail($emailData));
        }

        return response()->json([
            'success' => true,
            'message' => 'User with email ' . $request->email . ' successfully added to the course!',
        ]);
    }

    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $course_id = session('last_opened_course');
        if (!$course_id) {
            return redirect()->back()->with('error', 'No course selected.');
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Get header row: name,email,sid,role
        $header = fgetcsv($handle);

        $successCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $data['role'] = [
                'student' => 1,
                'instructor' => 2,
                'ta' => 3,
            ][strtolower(trim($data['role']))] ?? $data['role'];

            $validator = \Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:roster,email',
                'role' => 'required|in:1,2,3',
                'sid' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row with email {$data['email']} failed: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $user = DB::table('users')->where('email', $data['email'])->first();

            Roster::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $user ? $user->id : null,
                'course_id' => $course_id,
                'role' => $data['role'],
                'sid' => $data['sid'],
            ]);

            $successCount++;
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "CSV upload complete. Successfully added {$successCount} users.",
            'redirect' => route('courses.roster', ['id' => session('last_opened_course')]),
            'errors' => $errors,
        ]);

    }

    public function rosterDownload($id)
    {
        $students = DB::table('roster')
            ->where('course_id', $id)
            ->where('role', 1)
            ->select('name', 'email', 'sid')
            ->get();

        $response = new StreamedResponse(function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Write CSV header
            fputcsv($handle, ['Name', 'Email', 'Student ID']);

            // Write student data
            foreach ($students as $student) {
                fputcsv($handle, [(string) $student->name, (string) $student->email, (string) $student->sid]);
            }

            fclose($handle);
        });

        // Set correct headers for CSV download
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="roster.csv"');

        return $response;
    }

    public function editUser(Request $request, $id)
    {
        $member = Roster::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:roster,email,' . $id . ',id',
            'sid' => 'nullable|string|max:255',
            'role' => 'required|integer|in:1,2,3',
        ]);

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'sid' => $request->sid,
            'role' => $request->role,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'member' => $member,
        ]);

    }

    public function deleteUser($id)
    {
        $user = Roster::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

}
