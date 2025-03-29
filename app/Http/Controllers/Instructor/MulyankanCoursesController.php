<?php
namespace App\Http\Controllers\Instructor; 

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    #This is added to link to the course setting page
    public function settings($id)
    {
        // Fetch the course and ensure it belongs to the current instructor
        $course = Course::where('id', $id)
                        ->where('instructor_id', Auth::id())
                        ->firstOrFail();
    
        return view('instructor.course_setting', compact('course'));
    }
}
