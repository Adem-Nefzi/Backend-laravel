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
        if (!$this->isAdmin() && Auth::id() !== $association->user_id) {
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

    public function apiStore(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can create associations.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string',
        ]);

        $association = new Association($request->except('logo_url'));
        $association->user_id = Auth::id();

        if ($request->hasFile('logo_url')) {
            $path = $request->file('logo_url')->store('logos', 'public');
            $association->logo_url = $path;
        }

        $association->save();
        return response()->json(['message' => 'Association created successfully.', 'association' => $association], 201);
    }

    public function apiUpdate(Request $request, Association $association)
    {
        $this->checkOwnership($association);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string',
        ]);

        $association->fill($request->except('logo_url'));

        if ($request->hasFile('logo_url')) {
            if ($association->logo_url) {
                Storage::disk('public')->delete($association->logo_url);
            }
            $path = $request->file('logo_url')->store('logos', 'public');
            $association->logo_url = $path;
        }

        $association->save();
        return response()->json(['message' => 'Association updated successfully.', 'association' => $association]);
    }



    public function apiDestroy(Request $request)
    {
        // Get the currently authenticated association
        $association = $request->user();

        // Verify this is actually an association
        if (!$association instanceof \App\Models\Association) {
            abort(403, 'Only authenticated associations can delete themselves');
        }

        try {
            // Delete the logo if exists
            if ($association->logo_url) {
                Storage::disk('public')->delete($association->logo_url);
            }

            // Delete all tokens
            $association->tokens()->delete();

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
