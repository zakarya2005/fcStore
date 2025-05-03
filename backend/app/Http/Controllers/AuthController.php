<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'tel' => [
                'required',
                'string',
                'unique:users',
                'regex:/^(0)(5|6|7)[0-9]{8}$/'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one digit.',
            'tel.regex' => 'The phone number must be a valid Moroccan number (e.g., 0612345678).',
            'password.confirmed' => 'The passwords do not match.',
        ]);

        $user = User::create([
            'firstname' => $validated["firstname"],
            'lastname' => $validated["lastname"],
            'email' => $validated["email"],
            'tel' => $validated["tel"],
            'password' => Hash::make($validated["password"]),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request) 
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        Auth::login($user);

        return response()->json([
            'message' => "User logged in successfully",
            'user' => $user 
        ]);
    }

    public function logout() 
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => "No authenticated user",
            ], 401);
        }

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }
}