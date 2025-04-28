<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string' // For API token generation
        ]);

        // Check credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = $request->user();

        // For API requests
        if ($request->wantsJson()) {
            $token = $user->createToken($request->device_name ?? 'api_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'user_type' => $user->user_type // Include user type in response
            ]);
        }

        // For web requests
        $request->session()->regenerate();
        return response()->noContent();
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
