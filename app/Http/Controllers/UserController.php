<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    protected function isAdmin()
    {
        return Auth::user() && Auth::user()->user_type === 'admin';
    }

    protected function checkOwnership(User $user)
    {
        if (!$this->isAdmin() && Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can view all users.');
        }
        $users = User::all();
        return response()->json($users);
    }


    public function show($user)
    {
        return response()->json([
            'user' => $user->makeHidden(['password', 'remember_token']),
        ]);
    }


    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can create users.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'user_type'  => 'required|in:donor,recipient,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }



    public function update(Request $request, User $user)
    {
        $this->checkOwnership($user);

        $rules = [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|sometimes|string|max:20',
            'address' => 'nullable|sometimes|string',
            'password' => 'nullable|sometimes|string|min:8|confirmed',
        ];

        // Only validate user_type if present AND user is admin
        if ($request->has('user_type') && $this->isAdmin()) {
            $rules['user_type'] = 'required|in:donor,recipient,admin';
        }

        $validated = $request->validate($rules);

        // Remove null values (keep existing data)
        $validated = array_filter($validated, fn($value) => !is_null($value));

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json(['message' => 'User updated successfully']);
    }


    public function destroySelf(Request $request)
    {
        try {
            // Get authenticated user FIRST
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Revoke tokens THEN delete
            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }



    public function destroy(User $user)
    {
        $this->checkOwnership($user);
        $user->delete();

        return response()->json([
            'message' => 'User account deleted successfully'
        ]);
    }


    public function restore($userId)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can restore accounts');
        }

        $user = User::withTrashed()->findOrFail($userId);

        // Only restore the user, not relationships
        $user->restore();

        // If you need to restore pivot relationships, do it separately
        // $user->associations()->update(['deleted_at' => null]);

        return response()->json([
            'message' => 'Account restored successfully',
            'user' => $user->fresh() // Get refreshed user data
        ]);
    }

    public function deletedUsers()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can view deleted accounts.');
        }

        $deletedUsers = User::onlyTrashed()->get();

        if ($deletedUsers->isEmpty()) {
            return response()->json([
                'message' => 'No deleted users found',
                'users' => []
            ]);
        }

        return response()->json($deletedUsers);
    }
}
