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

    public function create($courseNo)
    {
        $this->authorizeInstructor(); // Ensure only instructor

        $course = Course::where('course_no', $courseNo)->firstOrFail();
        return view('assignments.create', compact('course'));
    }

    public function store(Request $request, $courseNo)
    {
        $this->authorizeInstructor();

        $course = Course::where('course_no', $courseNo)->firstOrFail();
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'points'        => 'nullable|integer',
            'release_date'  => 'nullable|date',
            'due_date'      => 'nullable|date',
        ]);

        $data['course_id'] = $course->id;
        Assignment::create($data);

        return redirect()
            ->route('courses.assignments.index', $courseNo)
            ->with('success', 'Assignment created successfully.');
    }

    public function show($courseNo, $assignmentId)
    {
        $course = Course::where('course_no', $courseNo)->firstOrFail();
        $assignment = Assignment::where('course_id', $course->id)
                                ->where('id', $assignmentId)
                                ->firstOrFail();

        // Students and instructors can view
        return view('assignments.show', compact('course', 'assignment'));
    }

    public function edit($courseNo, $assignmentId)
    {
        $this->authorizeInstructor();

        $course = Course::where('course_no', $courseNo)->firstOrFail();
        $assignment = Assignment::where('course_id', $course->id)
                                ->where('id', $assignmentId)
                                ->firstOrFail();

        return view('assignments.edit', compact('course', 'assignment'));
    }

    public function update(Request $request, $courseNo, $assignmentId)
    {
        $this->authorizeInstructor();

        $course = Course::where('course_no', $courseNo)->firstOrFail();
        $assignment = Assignment::where('course_id', $course->id)
                                ->where('id', $assignmentId)
                                ->firstOrFail();

        // Example: toggling "status" (published/unpublished)
        $assignment->update([
            'status' => $request->has('status'),
        ]);

        return redirect()
            ->route('courses.assignments.index', $courseNo)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($courseNo, $assignmentId)
    {
        $this->authorizeInstructor();

        $course = Course::where('course_no', $courseNo)->firstOrFail();
        $assignment = Assignment::where('course_id', $course->id)
                                ->where('id', $assignmentId)
                                ->firstOrFail();

        $assignment->delete();

        return redirect()
            ->route('courses.assignments.index', $courseNo)
            ->with('success', 'Assignment deleted successfully.');
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
