<?php

namespace App\Http\Controllers\TA;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TA;
use App\Models\Course;
use App\Models\Assignment;

class TADashboardController extends Controller
{
    public function index()
    {
        $userEmail = Auth::user()->email;
        $courseNumbers = TA::where('email', $userEmail)->pluck('course_id');
        $Course = Course::whereIn('id', $courseNumbers)->get();
        
        return view('ta.dashboard',compact('Course'));
    }
    public function show($id)
    {
        $course = Course::where('id', $id)->firstOrFail();
        $assignments = Assignment::where('course_number', $course->id)->get();
        session(['last_opened_course' => $id]);
        return view('ta.show', compact('course', 'assignments'));
    }
}
 