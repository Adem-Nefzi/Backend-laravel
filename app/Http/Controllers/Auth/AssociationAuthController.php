<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class AssociationAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:associations,email',
            'password' => ['required', 'string', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|in:Food,Clothes,Healthcare,Education,Home supplies',
            'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $association = Association::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'] ?? null,
            ]);

            if ($request->hasFile('logo_url')) {
                $this->handleLogoUpload($association, $request->file('logo_url'));
            }

            // Generate token for the association
            $token = $association->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Association registered successfully',
                'association' => $association->makeHidden(['password']),
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Association registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $association = Association::where('email', $request->email)->first();

        if (!$association || !Hash::check($request->password, $association->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $association->createToken('association_token')->plainTextToken;

        return response()->json([
            'association' => $association,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    private function handleLogoUpload(Association $association, $file, $deleteOld = false)
    {
        if ($deleteOld && $association->logo_url) {
            $this->deleteLogoIfExists($association);
        }

        $path = $file->store('public/associations/logos');
        $association->update(['logo_url' => Storage::url($path)]);
    }


    private function deleteLogoIfExists(Association $association)
    {
        if ($association->logo_url) {
            $oldPath = str_replace('/storage', 'public', $association->logo_url);
            Storage::delete($oldPath);
        }
    }
}
