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

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'user_type'  => 'required|in:donor,recipient,admin',
            'password'   => 'nullable|string|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        $this->checkOwnership($user);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
