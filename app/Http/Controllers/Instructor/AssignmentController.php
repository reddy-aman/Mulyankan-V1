<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index($courseNo)
    {
        // Retrieve the course by course_no or similar field
        $course = Course::where('id', $courseNo)->firstOrFail();
        // $course = Course::where('course_no', $courseNo)->firstOrFail();

        // Fetch assignments for that course
        $assignments = Assignment::where('course_id', $course->course_number)->get();

        // Check role (example: an instructor can do everything, 
        // a student can only view). Adjust logic as needed.
        $user = auth()->user();
        $isInstructor = $user && $user->role === 'instructor';

        return view('assignments.index', compact('course', 'assignments', 'isInstructor'));
    }

    public function create($course_id)
    {
        // $this->authorizeInstructor(); // Ensure only instructor

        $course = Course::where('id', $course_id)->firstOrFail();
        return view('assignments.create', compact('course_id'));
    }

    /**
     * Simple helper to ensure only an instructor can proceed.
     */
    private function authorizeInstructor()
    {
        if (!auth()->check() || auth()->user()->role !== 'instructor') {
            abort(403, 'Unauthorized action.');
        }
    }
}
