<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password.regex' => 'Password must contain at least one uppercase, one lowercase letter, and one digit.',
            'tel.regex' => 'The phone number must be a valid Moroccan number (e.g., 0612345678).',
            'password.confirmed' => 'The passwords do not match.',
        ]);

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'tel' => $validated['tel'],
            'password' => Hash::make($validated['password']),
        ]);

        Cart::create([
            'user_id' => $user->id,
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

        // Create personal access token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Store token in secure HttpOnly cookie
        return response()->json([
            'message' => 'Login successful',
        ])->cookie(
            'token',      // Cookie name
            $token,       // Value (the token)
            60 * 24,      // Expiration (in minutes)
            '/',          // Path
            null,         // Domain (null = current domain)
            false,        // Secure: false = allow HTTP
            false,        // HttpOnly: false = JavaScript can access it
            false,        // Raw
            null          // SameSite: null = no restriction
        );
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'User logged out'
        ])->cookie('token', '', -1);
    }
}
