<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssociationController extends Controller
{
    use AuthorizesRequests;

    protected function isAdmin()
    {
        return Auth::user() && Auth::user()->user_type === 'admin';
    }

    protected function checkOwnership(Association $association)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function apiIndex()
    {
        $associations = Association::all();
        return response()->json($associations);
    }

    public function apiShow(Association $association)
    {
        return response()->json($association);
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can create associations.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:associations,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $association = new Association();
            $association->name = $validated['name'];
            $association->email = $validated['email'];
            $association->password = bcrypt($validated['password']);
            $association->phone = $validated['phone'] ?? null;
            $association->address = $validated['address'] ?? null;
            $association->description = $validated['description'] ?? null;

            if ($request->hasFile('logo_url')) {
                $path = $request->file('logo_url')->store('public/associations/logos');
                $association->logo_url = Storage::url($path);
            }

            $association->save();

            return response()->json([
                'message' => 'Association created successfully',
                'association' => $association
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create association',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiUpdate(Request $request, Association $association)
    {
        $this->checkOwnership($association);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:associations,email,' . $association->id,
            'password' => 'sometimes|string|min:8',
            'phone' => 'nullable|sometimes|string|max:20',
            'address' => 'nullable|sometimes|string|max:255',
            'description' => 'nullable|sometimes|string',
            'logo_url' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            if (isset($validated['name'])) {
                $association->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $association->email = $validated['email'];
            }
            if (isset($validated['password'])) {
                $association->password = bcrypt($validated['password']);
            }
            if (isset($validated['phone'])) {
                $association->phone = $validated['phone'];
            }
            if (isset($validated['address'])) {
                $association->address = $validated['address'];
            }
            if (isset($validated['description'])) {
                $association->description = $validated['description'];
            }

            if ($request->hasFile('logo_url')) {
                // Delete old logo if exists
                if ($association->logo_url) {
                    $oldPath = str_replace('/storage', 'public', $association->logo_url);
                    Storage::delete($oldPath);
                }

                $path = $request->file('logo_url')->store('public/associations/logos');
                $association->logo_url = Storage::url($path);
            }

            $association->save();

            return response()->json([
                'message' => 'Association updated successfully',
                'association' => $association
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update association',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiDestroy(Request $request, Association $association)
    {
        $this->checkOwnership($association);

        try {
            // Delete logo if exists
            if ($association->logo_url) {
                $path = str_replace('/storage', 'public', $association->logo_url);
                Storage::delete($path);
            }

            // Delete the association
            $association->delete();

            return response()->json([
                'message' => 'Association deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete association',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
