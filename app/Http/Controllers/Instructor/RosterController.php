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
use App\Mail\UserUpdatedMail;
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
                $instructors_oth = Instructor::where('course_id', $course->id)->get();

            } elseif ($filterRole === 'students') {
                $students = Student::where('course_id', $course->id)->get();

            } elseif ($filterRole === 'TA') {
                $ta = Ta::where('course_id', $course->id)->get();

            }
        } else {
            $instructors_oth = Instructor::where('course_id', $course->id)->get();
            $students = Student::where('course_id', $course->id)->get();
            $ta = Ta::where('course_id', $course->id)->get();
        }

        $allInstructors = collect($instructors)->merge($instructors_oth)->values();
        return view('instructor.course-roster', compact('course', 'allInstructors', 'students', 'ta', 'pri_instructor_email'));
    }

    private function validateUniqueCourseUser($email, $role, $course)
    {
        $courseId = $course->id;

        $existsInStudent = Student::where('course_id', $courseId)
                                  ->where('email', $email)
                                  ->exists();
    
        $existsInInstructor = Instructor::where('course_id', $courseId)
                                        ->where('email', $email)
                                        ->exists();
    
        $existsInTA = Ta::where('course_id', $courseId)
                        ->where('email', $email)
                        ->exists();
    
        return $existsInStudent || $existsInInstructor || $existsInTA;
    }
    
    public function addUser(Request $request)
    {
        $course_id = session('last_opened_course');
        if (!$course_id) {
            return redirect()->back()->with('error', 'No course selected.');
        }
        $course = Course::findOrFail($course_id);
    

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($course, $request) {
                    if ($this->validateUniqueCourseUser($value, $request->role, $course)) {
                        $fail('The email is already registered for this course.');
                    }
                }
            ],
            'role' => 'required|in:1,2,3',
            'sid'  => 'nullable|string|max:255',
        ]);
    
        $role = $request->role;
    
        $user = DB::table('users')->where('email', $request->email)->first();
    
        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'user_id'       => $user ? $user->id : null,
            'course_id'  => $course->id,
        ];
    
        if ($role === "1") {
            $data['sid'] = $request->sid;
            $model=Student::create($data);
    
        } elseif ($role === "2") {
            $model=Instructor::create($data);
    
        } elseif ($role === "3") {
            $model=Ta::create($data);
        }
    
        // Optionally notify the user if requested.
        if ($request->has('notify_user')) {
            $emailData = [
                'name'          => $request->name,
                'course_number' => $course->course_number,
                'course_name'   => $course->course_name,
                'registered'    => $user ? true : false,
            ];
    
            Mail::to($request->email)->send(new UserRegisteredMail($emailData));
            $model->update(['email_notified' => true]);
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

        $header = ['name', 'email', 'sid', 'role'];

        $firstRow = fgetcsv($handle);
        if ($firstRow === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'CSV appears to be empty.');
        }
    
        if (strtolower(trim($firstRow[0])) === 'name') {
        } else {
            rewind($handle);
        }

        $successCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $data['role'] = [
                'student' => 1,
                'instructor' => 2,
                'ta' => 3,
            ][strtolower(trim($data['role']))] ?? $data['role'];

            $validator = Validator::make($data, [
                'name'  => 'required|string|max:255',
                'email' => 'required|email',
                'role'  => 'required|in:1,2,3',
                'sid'   => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                continue;
            }

            if ($this->validateUniqueCourseUser($data['email'], $data['role'], $course)) {
                continue;
            }

            $user = DB::table('users')->where('email', $data['email'])->first();

            $recordData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $user ? $user->id : null,
                'course_id' => $course->id,
            ];

            if ($data['role'] === 1) {
                // student
                $recordData['sid'] = $data['sid'];
                $model=Student::create($recordData);
    
            } elseif ($data['role'] === 2) {
                // instructor
                $model=Instructor::create($recordData);
    
            } else {
                // ta
                $model=Ta::create($recordData);
            }
            $successCount++;

            if ($request->has('notify_user')) {
                $emailData = [
                    'name'          => $recordData['name'],
                    'course_number' => $course->course_number,
                    'course_name'   => $course->course_name,
                    'registered'    => $user ? true : false,
                ];
        
                Mail::to($recordData['email'])->send(new UserRegisteredMail($emailData));
                $model->update(['email_notified' => true]);
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "CSV upload complete. Successfully added {$successCount} users.",
            'redirect' => route('courses.roster', ['id' => session('last_opened_course')]),
        ]);
    }

    public function rosterDownload($id)
    {
        $course = Course::findOrFail($id);
        $students = Student::where('course_id', $course->id)
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
            'course_id' => $course->id,
            'email_notified' => true,
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

        $roleNameMap = [1 => 'Student', 2 => 'Instructor', 3 => 'TA'];

        $emailData = [
            'name'          => $request->name,
            'email'         => $newEmail,
            'course_number' => $course->course_number,
            'role_name'     => $roleNameMap[$newRole],
            'sid'           => $request->sid,
        ];
        
        Mail::to($newEmail)->send(new UserUpdatedMail($emailData));

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

    public function sendEnrollmentNotification($id)
    {
        $course = Course::findOrFail($id);
    
        $students = Student::where('course_id', $course->id)
                           ->where('email_notified', false)
                           ->get();
    
        $instructors = Instructor::where('course_id', $course->id)
                                 ->where('email_notified', false)
                                 ->get();
    
        $tas = Ta::where('course_id', $course->id)
                 ->where('email_notified', false)
                 ->get();
    
        $usersToNotify = $students->merge($instructors)->merge($tas);

        if ($usersToNotify->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No users found to notify.',
            ]);
        }

        foreach ($usersToNotify as $user) {
            // Prepare email data
            $user_email = DB::table('users')->where('email', $user->email)->first();

            $emailData = [
                'name' => $user->name,
                'course_number' => $course->course_number,
                'course_name' => $course->course_name,
                'registered'    => $user_email ? true : false,
            ];
    
            // Send the notification email
            Mail::to($user->email)->send(new UserRegisteredMail($emailData));

            $user->update(['email_notified' => true]);

        }
    
        return response()->json([
            'success' => true,
            'message' => 'Enrollment notifications sent successfully to users.',
        ]);
    }
    
}
