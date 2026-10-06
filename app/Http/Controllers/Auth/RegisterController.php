<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'confirmed', Rule::unique('users', 'email')],
        ]);

        User::firstOrCreate(
            ['email' => strtolower(trim($validated['email']))],
            [
                'name' => Str::before(strtolower(trim($validated['email'])), '@') ?: 'User',
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make(Str::random(32)),
            ]
        );

        return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
    }
}
