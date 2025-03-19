<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;  // Import the DB facade

class InstructorDashboardController extends Controller
{
    
    public function index()
    {
        // $terms = DB::table(table: 'attributes')->where('type', 'term')->pluck('value', 'id');
        // $years = DB::table('attributes')->where('type', 'year')->pluck('value', 'id');
        // $departments = DB::table('attributes')->where('type', 'department')->pluck('value', 'id');
        
        // $Course = DB::table(table: 'courses')->get();
        // //dd($Course);

        $role = auth()->user()->getRoleNames()->first(); // Get the first role

        

        return view('instructor.dashboard',compact('role'));
    }
}
