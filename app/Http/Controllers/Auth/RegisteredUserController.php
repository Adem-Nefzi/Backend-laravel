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
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type'  => ['required', 'in:donor,recipient,admin'], // make sure user_type is provided
            'phone'      => ['nullable', 'string', 'max:20'],
            'address'    => ['nullable', 'string'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'user_type'  => $request->user_type,
            'phone'      => $request->phone,
            'address'    => $request->address,
        ]);

        return response()->json(['message' => 'User registered successfully!'], 201);
    }
}
