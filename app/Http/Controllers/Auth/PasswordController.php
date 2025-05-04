<?php

// app/Http/Controllers/PasswordController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        
        // Validate input
        Log::info('Password update hit');
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if the current password matches
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        // Update the password
        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        
        Log::info('Saving new password hash: ', [Hash::make($request->new_password)]);

        return redirect()->back()->with('status', 'Password changed successfully!');
    }
}
