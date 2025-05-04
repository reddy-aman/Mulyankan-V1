<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\CourseStudentController;
use App\Http\Controllers\TA\TADashboardController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Instructor\MulyankanCoursesController;
use App\Http\Controllers\Instructor\RosterController;
use App\Http\Controllers\Instructor\AssignmentController;

// Welcome page (public route)
Route::get('/', function () {
    return view('welcome');
});

// Group routes for authenticated users

Route::group(['prefix' => 'mulyankan', 'middleware' => 'auth'], function () {

    // Role-specific dashboard redirection

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->middleware('role:Student')->name('student.dashboard');
    // Route::get('/instructor/dashboard', [InstructorDashboardController::class, 'index'])->middleware('role:Instructor')->name('instructor.dashboard');
    Route::get('/ta/dashboard', [TADashboardController::class, 'index'])->middleware('role:TA')->name('ta.dashboard');


    // Students route
    Route::get('/student/{id}', [CourseStudentController::class, 'show'])->middleware(['role:Student'])->name('courses_student.show');//route for showcourse
    Route::get('/courses_student/{id}/roster', [CourseStudentController::class, 'roster'])->middleware(['role:Student'])->name('courses_student.roster');//route for roster.

    // TA routes
    Route::get('/view/courses', [TADashboardController::class, 'show'])->middleware('role:Instructor|TA')->name('ta.create-courses');


    // Course routes 

    Route::get('/view/courses', [MulyankanCoursesController::class, 'index'])->middleware('role:Instructor|TA')->name('instructor.create-courses');
    Route::post('/courses/{course}/enroll', [MulyankanCoursesController::class, 'enroll'])->middleware('role:Student|Instructor|TA')->name('courses.enroll');
    Route::post('/add/courses', [MulyankanCoursesController::class, 'store'])->middleware('role:Instructor|TA')->name('courses.store');
    Route::get('{course}/edit', [MulyankanCoursesController::class, 'edit'])->middleware('role:Instructor|TA')->name('edit');
    Route::put('{course}', [MulyankanCoursesController::class, 'update'])->middleware('role:Instructor|TA')->name('update');
    Route::get('/courses/{id}', [MulyankanCoursesController::class, 'show'])->middleware('role:Instructor|TA')->name('courses.show');
    Route::get('/courses/{id}/settings', [MulyankanCoursesController::class, 'settings'])->middleware('role:Instructor|TA')->name('courses.settings');

    // Roster routes

    Route::get('/courses/{id}/roster', [RosterController::class, 'showRoster'])->middleware('role:Instructor|TA')->name('courses.roster');
    Route::post('/courses/add-user', [RosterController::class, 'addUser'])->middleware('role:Instructor|TA')->name('courses.addUser');
    Route::post('/courses/upload-csv', [RosterController::class, 'uploadCSV'])->middleware('role:Instructor|TA')->name('courses.uploadCSV');
    Route::get('/courses/rosterDownload/{id}', [RosterController::class, 'rosterDownload'])->middleware('role:Instructor|TA')->name('courses.rosterDownload');
    Route::post('/courses/editUser{email}', [RosterController::class, 'editUser'])->middleware('role:Instructor|TA')->name('courses.editUser');
    Route::delete('/courses/deleteUser/{email}', [RosterController::class, 'deleteUser'])->middleware('role:Instructor|TA')->name('courses.deleteUser');
    Route::post('/courses/sendEnrollmentNotification/{id}', [RosterController::class, 'sendEnrollmentNotification'])->middleware('role:Instructor|TA')->name('courses.sendEnrollmentNotification');

    // Assignment routes

    Route::get('/courses/{id}/assignments', [AssignmentController::class, 'index'])->middleware('role:Instructor|TA')->name('assignments.index');
    Route::get('/assignments/{id}/create', [AssignmentController::class, 'create'])->middleware('role:Instructor|TA')->name('assignments.create');
    Route::get('/assignments/examQuiz', function () {return view('assignments.examQuiz');})->middleware('role:Instructor|TA')->name('assignments.examQuiz');
    Route::get('/assignments/homework', function () {return view('assignments.homework');})->middleware('role:Instructor|TA')->name('assignments.homework');
    Route::get('/assignments/bubble', function () {return view('assignments.bubble');})->middleware('role:Instructor|TA')->name('assignments.bubble');
    Route::get('/assignments/programming', function () {return view('assignments.programming');})->middleware('role:Instructor|TA')->name('assignments.programming');
    Route::get('/assignments/online', function () {return view('assignments.online');})->middleware('role:Instructor|TA')->name('assignments.online');
    Route::post('/assignments/store-template', [AssignmentController::class, 'storeTemplate'])->middleware('role:Instructor|TA')->name('assignments.storeTemplate');
    Route::get('/assignments/{assignment}/annotate-template', [AssignmentController::class, 'annotateTemplate'])->middleware('role:Instructor|TA')->name('assignments.annotateTemplate');
    Route::post('/assignments/save-annotation', [AssignmentController::class, 'saveAnnotation'])->middleware('role:Instructor|TA')->name('assignments.saveAnnotation');
    Route::get('assignments/{assignment}/submit', [AssignmentController::class, 'uploadForm'])->middleware('role:Instructor|TA')->name('assignments.uploadForm');
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'upload'])->middleware('role:Instructor|TA')->name('assignments.upload');
    Route::get('/assignments/{assignment}/edit',[AssignmentController::class, 'edit'])->middleware('role:Instructor')->name('assignments.edit');
    Route::put('/assignments/{assignment}',[AssignmentController::class, 'update'])->middleware('role:Instructor')->name('assignments.update');
    Route::delete('/assignments/{assignment}',[AssignmentController::class, 'deleteAssignment'])->middleware('role:Instructor')->name('assignments.deleteAssignment');

    Route::get('/profile', [ProfileController::class, 'edit'])->middleware('role:Instructor')->name('profile.edit');
    Route::patch('profile/change/password', [ProfileController::class, 'update'])->middleware('role:Instructor')->name('profile.update');
    Route::delete('/profile', action: [ProfileController::class, 'destroy'])->middleware('role:Instructor')->name('profile.destroy');

    Route::put('/profile/password', [PasswordController::class, 'update'])
    ->middleware('auth')
    ->name('password.update');


    Route::get('/courses/{id}/settings', [MulyankanCoursesController::class, 'settings'])->middleware('role:Instructor|TA')->name('courses.settings');
    Route::put('/courses/{id}/settings', [MulyankanCoursesController::class, 'update'])->middleware('role:Instructor|TA')->name('courses.updateSettings');
    Route::delete('/courses/{id}/settings', [MulyankanCoursesController::class, 'updateFromDelete'])->middleware('role:Instructor|TA')->name('courses.updateFromDelete');
    Route::delete('/courses/{id}', [MulyankanCoursesController::class, 'destroy'])->middleware('role:Instructor|TA')->name('courses.destroy');
});




// Require authentication routes (login, register, etc.)
require __DIR__ . '/auth.php';
