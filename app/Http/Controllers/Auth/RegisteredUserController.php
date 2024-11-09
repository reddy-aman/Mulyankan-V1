<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the registration form inputs, including the role
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'], // Ensure role is valid and exists in the database
        ]);

        // Create the new user with the provided details
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the selected role to the new user
        $user->assignRole($request->role);

        // Fire the Registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect based on the assigned role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Custom function to handle redirection after successful registration.
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
        // Redirect user based on their assigned role
        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard'); // Ensure this route exists
        } elseif ($user->hasRole('Instructor')) {
            return redirect()->route('instructor.dashboard'); // Ensure this route exists
        } elseif ($user->hasRole('TA')) {
            return redirect()->route('ta.dashboard'); // Ensure this route exists
        }

        // Default redirect if no matching role
        return redirect('/home');
    }
}
