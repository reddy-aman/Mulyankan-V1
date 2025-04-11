<?php

namespace App\Http\Controllers\TA;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TA;
use App\Models\Course;

class TADashboardController extends Controller
{
    public function index()
    {
        $userEmail = Auth::user()->email;
        $courseNumbers = TA::where('email', $userEmail)->pluck('course_number');
        $Course = Course::whereIn('course_number', $courseNumbers)->get();
        
        return view('ta.dashboard',compact( 'Course'));
    }
    public function show($courseId)
    {
        // Retrieve the course based on the provided course ID.
        $lastOpenedCourse = Course::findOrFail($courseId);

        // Optionally load related data, e.g. assignments for this course.
        $assignments = Assignment::where('course_number', $lastOpenedCourse->course_number)->get();

        // Pass the necessary variables to the view.
        return view('instructor.show', compact('lastOpenedCourse', 'assignments'));
    }
    public function index2()
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
}
 