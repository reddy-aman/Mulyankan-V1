<?php

use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\TA\TADashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Instructor\MulyankanCoursesController;
use App\Http\Controllers\Instructor\RosterController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Instructor\AssignmentController;

// Welcome page (public route)
Route::get('/', function () {
    return view('welcome');
});

// Group routes for authenticated users

Route::group(['prefix' => 'mulyankan', 'middleware' => 'auth'], function () {

    // Role-specific dashboard redirection

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->middleware('role:Student')->name('student.dashboard');
    Route::get('/instructor/dashboard', [InstructorDashboardController::class, 'index'])->middleware('role:Instructor')->name('instructor.dashboard');
    Route::get('/ta/dashboard', [TADashboardController::class, 'index'])->middleware('role:TA')->name('ta.dashboard');

    // Course routes 

    Route::get('/view/courses', [MulyankanCoursesController::class, 'index'])->middleware('role:Student|Instructor')->name('instructor.create-courses');
    Route::post('/courses/{course}/enroll', [MulyankanCoursesController::class, 'enroll'])->middleware('role:Student|Instructor')->name('courses.enroll');
    Route::post('/add/courses', [MulyankanCoursesController::class, 'store'])->middleware('role:Student|Instructor')->name('courses.store');
    Route::get('{course}/edit', [MulyankanCoursesController::class, 'edit'])->middleware('role:Student|Instructor')->name('edit');
    Route::put('{course}', [MulyankanCoursesController::class, 'update'])->middleware('role:Student|Instructor')->name('update');
    Route::get('/courses/{id}', [MulyankanCoursesController::class, 'show'])->middleware('role:Student|Instructor')->name('courses.show');

    // Roster routes

    Route::get('/courses/{id}/roster', [RosterController::class, 'showRoster'])->middleware('role:Student|Instructor')->name('courses.roster');
    Route::post('/courses/add-user', [RosterController::class, 'addUser'])->middleware('role:Student|Instructor')->name('courses.addUser');
    Route::post('/courses/upload-csv', [RosterController::class, 'uploadCSV'])->middleware('role:Student|Instructor')->name('courses.uploadCSV');
    Route::get('/courses/rosterDownload/{id}', [RosterController::class, 'rosterDownload'])->middleware('role:Student|Instructor')->name('courses.rosterDownload');
    Route::post('/courses/editUser{email}', [RosterController::class, 'editUser'])->middleware('role:Student|Instructor')->name('courses.editUser');
    Route::delete('/courses/deleteUser/{email}', [RosterController::class, 'deleteUser'])->middleware('role:Student|Instructor')->name('courses.deleteUser');

    // Assignment routes

    Route::get('/courses/{id}/assignments', [AssignmentController::class, 'index'])->middleware('role:Instructor')->name('assignments.index');
    Route::get('/assignments/{id}/create', [AssignmentController::class, 'create'])->middleware('role:Instructor')->name('assignments.create');
    Route::get('/assignments/examQuiz', function () {return view('assignments.examQuiz');})->middleware('role:Instructor')->name('assignments.examQuiz');
    Route::get('/assignments/homework', function () {return view('assignments.homework');})->middleware('role:Instructor')->name('assignments.homework');
    Route::get('/assignments/bubble', function () {return view('assignments.bubble');})->middleware('role:Instructor')->name('assignments.bubble');
    Route::get('/assignments/programming', function () {return view('assignments.programming');})->middleware('role:Instructor')->name('assignments.programming');
    Route::get('/assignments/online', function () {return view('assignments.online');})->middleware('role:Instructor')->name('assignments.online');
    Route::get('/assignments/upload-template', [AssignmentController::class, 'showUploadForm'])->name('assignments.showUploadForm');
    Route::post('/assignments/store-template', [AssignmentController::class, 'storeTemplate'])->name('assignments.storeTemplate');
    Route::get('/assignments/annotate-template', [AssignmentController::class, 'annotateTemplate'])->name('assignments.annotateTemplate');
    Route::post('/assignments/save-annotation', [AssignmentController::class, 'saveAnnotation'])->name('assignments.saveAnnotation');
    Route::post('/assignments/split-submission', [AssignmentController::class, 'splitSubmission'])->name('assignments.splitSubmission');
    Route::post('/assignments/save-annotation', [AssignmentController::class, 'saveAnnotation'])->name('assignments.saveAnnotation');

    // Route::prefix('/courses/{courseNo}/assignments')->group(function () {
    //     Route::get('/', [AssignmentController::class, 'index'])->name('courses.assignments.index');
    //     Route::get('/create', [AssignmentController::class, 'create'])->name('courses.assignments.create');
    //     Route::post('/', [AssignmentController::class, 'store'])->name('courses.assignments.store');
    //     Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('courses.assignments.show');
    //     Route::get('/{assignment}/edit', [AssignmentController::class, 'edit'])->name('courses.assignments.edit');
    //     Route::put('/{assignment}', [AssignmentController::class, 'update'])->name('courses.assignments.update');
    //     Route::delete('/{assignment}', [AssignmentController::class, 'destroy'])->name('courses.assignments.destroy');
    // });
    // Profile routes for authenticated users
    // wait
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
require __DIR__ . '/auth.php';
