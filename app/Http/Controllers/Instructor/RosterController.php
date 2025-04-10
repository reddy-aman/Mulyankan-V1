<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Course;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\TA;
use App\Mail\UserRegisteredMail;
use Validator;
use Illuminate\Support\Facades\Log;

class RosterController extends Controller
{
    // show the members associated to the course
    public function showRoster(Request $request, $id)
    {
        $course = DB::table('courses')->where('id', $id)->first();

        if (!$course || $course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized Access');
        }

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

        $instructors_oth = collect();
        $students = collect();
        $ta = collect();

        if ($filterRole !== 'all') {
            if ($filterRole === 'instructors') {
                $instructors_oth = Instructor::where('course_number', $course->course_number)->get();

            } elseif ($filterRole === 'students') {
                $students = Student::where('course_number', $course->course_number)->get();

            } elseif ($filterRole === 'TA') {
                $ta = Ta::where('course_number', $course->course_number)->get();

            }
        } else {
            $instructors_oth = Instructor::where('course_number', $course->course_number)->get();
            $students = Student::where('course_number', $course->course_number)->get();
            $ta = Ta::where('course_number', $course->course_number)->get();
        }

        $allInstructors = collect($instructors)->merge($instructors_oth)->values();
        return view('instructor.course-roster', compact('course', 'allInstructors', 'students', 'ta', 'pri_instructor_email'));
    }


    public function addUser(Request $request)
    {
        // Get the current course from session
        $course_id = session('last_opened_course');
        if (!$course_id) {
            return redirect()->back()->with('error', 'No course selected.');
        }
        $course = Course::findOrFail($course_id);
    
        // Validate the request.
        // Note: We check for duplicate email only within the same course.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($course, $request) {
                    // Depending on the requested role, check in the appropriate table
                    if ($request->role === "1") { // Student
                        if (Student::where('email', $value)
                                ->where('course_number', $course->course_number)
                                ->exists()) {
                            $fail('The email is already registered for this course.');
                        }
                    } elseif ($request->role === "2") { // Instructor
                        if (Instructor::where('email', $value)
                                ->where('course_number', $course->course_number)
                                ->exists()) {
                            $fail('The email is already registered for this course.');
                        }
                    } elseif ($request->role === "3") { // TA
                        if (Ta::where('email', $value)
                                ->where('course_number', $course->course_number)
                                ->exists()) {
                            $fail('The email is already registered for this course.');
                        }
                    }
                }
            ],
            'role' => 'required|in:1,2,3',
            'sid'  => 'nullable|string|max:255',
        ]);
    
        $role = $request->role;
    
        // Find the user in the users table, if it exists.
        $user = DB::table('users')->where('email', $request->email)->first();
    
        // Data to be inserted. This includes course_number so that the same email
        // can be registered in different courses.
        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'user_id'       => $user ? $user->id : null,
            'course_number' => $course->course_number,
        ];
    
        // Create the appropriate record based on the role.
        if ($role === "2") {
            Instructor::create($data);
        } elseif ($role === "1") {
            $data['sid'] = $request->sid;
            Student::create($data);
        } elseif ($role === "3") {
            Ta::create($data);
        }
    
        // Optionally notify the user if requested.
        if ($request->has('notify_user')) {
            $emailData = [
                'name'          => $request->name,
                'course_number' => $course->course_number,
                'course_name'   => $course->course_name,
                'role'          => $role,
                'registered'    => $user ? true : false,
            ];
    
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
        $course = Course::where('id', $course_id)->firstOrFail();

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Get header row: name,email,sid,role
        $rawHeader = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $rawHeader);

        $successCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $data['role'] = [
                'student' => 1,
                'instructor' => 2,
                'ta' => 3,
            ][strtolower(trim($data['role']))] ?? $data['role'];

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    function ($value, $fail) use ($course) {
                        if (
                            Student::where('email', $value)->where('course_number', '!=', $course->course_number)->exists() ||
                            Instructor::where('email', $value)->where('course_number', '!=', $course->course_number)->exists() ||
                            Ta::where('email', $value)->where('course_number', '!=', $course->course_number)->exists()
                        ) {
                            $fail("The email {$value} is already registered.");
                        }
                    }
                ],
                'role' => 'required|in:1,2,3',
                'sid' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row with email {$data['email']} failed: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $user = DB::table('users')->where('email', $data['email'])->first();

            $recordData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $user ? $user->id : null,
                'course_number' => $course->course_number,
            ];

            if ($data['role'] == 1) {
                $recordData['sid'] = $data['sid'];
                Student::create($recordData);
            } elseif ($data['role'] == 2) {
                Instructor::create($recordData);
            } elseif ($data['role'] == 3) {
                Ta::create($recordData);
            }

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
        $course = Course::findOrFail($id);
        $students = Student::where('course_number', $course->course_number)
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
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $course->course_number . ' roster.csv"');

        return $response;
    }

    public function editUser(Request $request, $email)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'sid'       => 'nullable|string|max:255',
            'role'      => 'required|integer|in:1,2,3',
            'old_email' => 'required|email|max:255',
        ]);

        $newRole   = $request->role;  
        $oldEmail  = $request->old_email; 
        $newEmail  = $request->email;

        if ($newEmail !== $oldEmail) {
            $emailExists = Student::where('email', $newEmail)->exists() ||
                           Instructor::where('email', $newEmail)->exists() ||
                           Ta::where('email', $newEmail)->exists();
            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'The new email is already taken by another user.'
                ], 422);
            }
        }

        $record = Student::where('email', $oldEmail)->first();
        $currentRole = null;
        if ($record) {
            $currentRole = 1;
        } else {
            $record = Instructor::where('email', $oldEmail)->first();
            if ($record) {
                $currentRole = 2;
            } else {
                $record = Ta::where('email', $oldEmail)->first();
                if ($record) {
                    $currentRole = 3;
                }
            }
        }
    
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $course_id = session('last_opened_course');
        if (!$course_id) {
            return redirect()->back()->with('error', 'No course selected.');
        }
        $course = Course::where('id', $course_id)->firstOrFail();
        
        $data = [
            'name'  => $request->name,
            'email' => $newEmail,
            'course_number' => $course->course_number,
            'updated_at' => now(),
        ];
        // For students, include sid
        if ($newRole == 1) {
            $data['sid'] = $request->sid;
        }
    
        // If the role remains the same, update in place
        if ($newRole == $currentRole) {
            $record->update($data);
            $updatedRecord = $record;
        } else {
            // If role changed, remove the record from the old table and create it in the new table
            $record->delete();
            if ($newRole == 1) {
                $updatedRecord = Student::create($data);
            } elseif ($newRole == 2) {
                $updatedRecord = Instructor::create($data);
            } elseif ($newRole == 3) {
                $updatedRecord = Ta::create($data);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'member' => $updatedRecord,
        ]);

    }

    public function deleteUser($email)
    {
        $record = Student::where('email', $email)->first();
    
        if (!$record) {
            $record = Instructor::where('email', $email)->first();
        }
        
        if (!$record) {
            $record = Ta::where('email', $email)->first();
        }
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }
}
