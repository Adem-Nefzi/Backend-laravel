<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => ['required', 'string', Rules\Password::defaults()], // ✅ Combined rule
            'user_type'  => 'required|in:donor,recipient,admin',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'user_type'  => $validated['user_type'],
            'phone'      => $validated['phone'] ?? null,
            'address'    => $validated['address'] ?? null,
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user // Return created user
        ], 201);
    }
}
