<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $userEmail = Auth::user()->email;
        $courseNumbers = Student::where('email', $userEmail)->pluck('course_id');
        $Course = Course::whereIn('id', $courseNumbers)->get();
        return view('student.dashboard',compact( 'Course'));
    }
}
 