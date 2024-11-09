<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollController extends Controller
{
    public function enroll(Request $request)
    {
        $request->validate([
            'entry_code' => 'required|string|exists:courses,entry_code',
        ]);

        // Find the course using entry code
        $course = Course::where('entry_code', $request->entry_code)->first();
        
        // Enroll the student (if course exists)
        if ($course) {
            $user = Auth::user(); // Get the current logged-in student
            $user->courses()->attach($course->id); // Attach the course to the student's courses
            return redirect()->back()->with('success', 'Successfully enrolled in the course!');
        }

        return redirect()->back()->with('error', 'Invalid entry code.');
    }
}
