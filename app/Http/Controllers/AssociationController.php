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

    // GET /api/associations (Admin only)
    public function index()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can view all associations.');
        }

        return response()->json([
            'associations' => Association::withTrashed()->get()
        ]);
    }

    // GET /api/associations/{id} (Owner or Admin)
    public function show(Association $association)
    {
        try {
            $this->checkOwnership($association);

            return response()->json([
                'association' => $association->makeHidden(['password', 'remember_token'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/associations (Admin only)
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admins can create associations.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:associations,email',
            'password' => 'required|string|min:8|confirmed',
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
                'user_id' => Auth::id()
            ]);

            if ($request->hasFile('logo_url')) {
                $this->handleLogoUpload($association, $request->file('logo_url'));
            }

            return response()->json([
                'message' => 'Association created successfully',
                'association' => $association->makeHidden(['password'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Association creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PUT/PATCH /api/associations/{id} (Owner or Admin)
    public function update(Request $request, Association $association)
    {
        // Verify the association belongs to the requesting user or the user is an admin
        try {
            $this->checkOwnership($association);

            $rules = [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:associations,email,' . $association->id,
                'password' => 'nullable|sometimes|string|min:8|confirmed',
                'phone' => 'nullable|sometimes|string|max:20',
                'address' => 'nullable|sometimes|string|max:255',
                'description' => 'nullable|sometimes|string',
                'category' => 'nullable|sometimes|string|in:Food,Clothes,Healthcare,Education,Home supplies',
                'logo_url' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];

            $validated = $request->validate($rules);

            // Filter out null values to keep existing data
            $validated = array_filter($validated, function ($value) {
                return !is_null($value);
            });

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            if ($request->hasFile('logo_url')) {
                $this->handleLogoUpload($association, $request->file('logo_url'), true);
            }

            $association->update($validated);

            return response()->json([
                'message' => 'Association updated successfully',
                'association' => $association->fresh()->makeHidden(['password', 'remember_token'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/associations/{id} (Owner or Admin)
    public function destroy(Association $association)
    {
        $this->checkOwnership($association);

        try {
            $this->deleteLogoIfExists($association);
            $association->delete();

            return response()->json([
                'message' => 'Association deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/associations/self (Self-delete)
    public function destroySelf(Request $request)
    {
        try {
            $association = $request->user()->association;

            if (!$association) {
                return response()->json(['error' => 'No associated organization found'], 404);
            }

            $this->deleteLogoIfExists($association);
            $association->tokens()->delete();
            $association->delete();

            return response()->json(['message' => 'Your organization account was deleted']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Self-deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET /api/associations/deleted (Admin only)
    public function deletedAssociations()
    {
        // Remove ALL checks - middleware handles auth
        $deleted = Association::onlyTrashed()->get();

        if ($deleted->isEmpty()) {
            return response()->json([
                'message' => 'No deleted associations found',
                'data' => []
            ], 200); // Still return 200 with empty array
        }

        return response()->json($deleted);
    }

    // POST /api/associations/{id}/restore (Admin only)
    public function restore($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Admin access required');
        }

        $association = Association::withTrashed()->findOrFail($id);
        $association->restore();

        return response()->json([
            'message' => 'Association restored',
            'association' => $association->fresh()
        ]);
    }

    // POST /api/associations/{id}/force-delete (Admin only)
    public function forceDelete($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Admin access required');
        }

        $association = Association::withTrashed()->findOrFail($id);
        $this->deleteLogoIfExists($association);
        $association->forceDelete();

        return response()->json(['message' => 'Association permanently deleted']);
    }

    // PRIVATE HELPER METHODS
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
