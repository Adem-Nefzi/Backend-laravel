<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user credentials are correct
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials',
            ], 401);
        }

        // Authentication passed, create a token
        $user = Auth::user();
        $token = $request->user()->createToken($user->email . '_Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
        // Ensure the user is authenticated before attempting to logout
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();  // Invalidate the token
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}
