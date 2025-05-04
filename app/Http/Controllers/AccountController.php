<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function edit()
    {
        // return current user data as JSON
        return response()->json(Auth::user());
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Account updated.',
            'user'    => $user,
        ]);
    }
}
