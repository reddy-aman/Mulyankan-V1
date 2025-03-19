<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the login input and selected role
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|in:Student,Instructor,TA', // Validate role selection
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the selected role matches the user's role
            if ($user->hasRole('Student') && $request->role !== 'Student') {
                Auth::logout(); // Logout the user
                return back()->withErrors(['role' => 'You are a Student, not an Instructor or TA. Please select the correct role.']);
            } elseif ($user->hasRole('Instructor') && $request->role !== 'Instructor') {
                Auth::logout(); // Logout the user
                return back()->withErrors(['role' => 'You are an Instructor, not a Student or TA. Please select the correct role.']);
            } elseif ($user->hasRole('TA') && $request->role !== 'TA') {
                Auth::logout(); // Logout the user
                return back()->withErrors(['role' => 'You are a TA, not a Student or Instructor. Please select the correct role.']);
            }

            // If the selected role matches the user's role, redirect to the corresponding dashboard
            return $this->redirectBasedOnRole($user);
        }

        // If authentication fails, redirect back with errors
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Redirect the user based on their assigned role.
     */
    public function redirectBasedOnRole($user)
    {
        // If user is a Student, redirect to the student dashboard
        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard'); // Ensure you have a route named 'student.dashboard'
        } 
        // If user is an Instructor, redirect to the instructor dashboard
        elseif ($user->hasRole('Instructor')) {
            return redirect()->route('instructor.dashboard'); // Ensure you have a route named 'instructor.dashboard'
        } 
        // If user is a TA, redirect to the TA dashboard
        elseif ($user->hasRole('TA')) {
            return redirect()->route('ta.dashboard'); // Ensure you have a route named 'ta.dashboard'
        }

        // Default redirect if no matching role
        return redirect('/home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
