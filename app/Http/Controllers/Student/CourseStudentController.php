<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\TA;
use App\Mail\UserRegisteredMail;
use Validator;
use Illuminate\Support\Facades\Log;


    


class CourseStudentController extends Controller
{
    public function show($id)
    {
        // Fetch the course using the provided id.
        $course = Course::findOrFail($id);

        // Fetch assignments based on the course's course number.
        $assignments = Assignment::where('course_number', $course->course_number)->get();

        // Set the last opened course session variable.
        session(['last_opened_course' => $id]);

        // Return a view that has both course and assignments data.
        return view('student.show', compact('course', 'assignments'));
    }

    // show the members associated to the course
    public function roster(Request $request, $id)
    {
        // Fetch the course by ID
        $course = DB::table('courses')->where('id', $id)->first();

        // If course not found, throw a 404 error.
        if (!$course) {
            abort(404, 'Course not found');
        }

        // Removed all authentication/authorization checks,
        // so now anyone can access this roster page.

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

        // Load the view (here using a student-specific view)
        return view('student.course-roster', compact('course', 'allInstructors', 'students', 'ta', 'pri_instructor_email'));
    }
}
