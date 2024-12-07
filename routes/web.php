<?php

use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\TA\TADashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Instructor\MulyankanCoursesController;
use App\Http\Controllers\Auth\NewPasswordController;

// Welcome page (public route)
Route::get('/', function () {
    return view('welcome');
});

// Group routes for authenticated users

Route::group(['prefix'=>'mulyankan','middleware'=>'auth'],function(){

    // Role-specific dashboard redirection

    Route::get('/student/dashboard',[StudentDashboardController::class,'index'])->middleware('role:Student')->name('student.dashboard');
    Route::get('/instructor/dashboard',[InstructorDashboardController::class,'index'])->middleware('role:Instructor')->name('instructor.dashboard');
    Route::get('/ta/dashboard',[TADashboardController::class,'index'])->middleware('role:TA')->name('ta.dashboard');

    // Course routes 

    Route::get('/view/courses', [MulyankanCoursesController::class, 'index'])->middleware('role:Student|Instructor')->name('instructor.create-courses');
    Route::post('/courses/{course}/enroll', [MulyankanCoursesController::class, 'enroll'])->middleware('role:Student|Instructor')->name('courses.enroll');
    Route::post('/add/courses', [MulyankanCoursesController::class, 'store'])->middleware('role:Student|Instructor')->name('courses.store');
    Route::get('{course}/edit', [MulyankanCoursesController::class, 'edit'])->middleware('role:Student|Instructor')->name('edit');
    Route::put('{course}', [MulyankanCoursesController::class, 'update'])->middleware('role:Student|Instructor')->name('update');

    // Profile routes for authenticated users
    
    // Route::get('/profile', [ProfileController::class, 'edit'])->middleware('role:Instructor')->name('profile.edit');
    // Route::patch('profile/change/password', [ProfileController::class, 'update'])->middleware('role:Instructor')->name('profile.update');
    // Route::delete('/profile', action: [ProfileController::class, 'destroy'])->middleware('role:Instructor')->name('profile.destroy');
    // Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('role:Instructor')->name('password.store');
   
    // Dashboard route for all authenticated users (handled by controller based on roles)
    // Route::get('/dashboard', function () {
    //     return (new AuthenticatedSessionController())->redirectBasedOnRole(auth()->user()); // Pass authenticated user here
    // })->name('dashboard');


});




// Require authentication routes (login, register, etc.)
require __DIR__.'/auth.php';
