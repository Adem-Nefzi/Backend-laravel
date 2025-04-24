<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssociationController extends Controller
{
    /**
     * Display a listing of the associations (Admin Only).
     */
    public function index()
    {
        $this->authorizeAdmin();

        $associations = Association::all();
        return view('associations.index', compact('associations'));
    }

    /**
     * Show the form for creating a new association (Admin Only).
     */
    public function create()
    {
        $this->authorizeAdmin();

        return view('associations.create');
    }

    /**
     * Store a newly created association in storage (Admin Only).
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:associations',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'creation_date' => 'required|date',
            'logo_url'      => 'nullable|string',
        ]);

        Association::create($validated);

        return redirect()->route('associations.index')->with('success', 'Association created successfully');
    }

    /**
     * Show the form for editing the specified association (Admin Only).
     */
    public function edit(Association $association)
    {
        $this->authorizeAdmin();

        return view('associations.edit', compact('association'));
    }

    /**
     * Update the specified association in storage (Admin Only).
     */
    public function update(Request $request, Association $association)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:associations,email,' . $association->id,
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'creation_date' => 'required|date',
            'logo_url'      => 'nullable|string',
        ]);

        $association->update($validated);

        return redirect()->route('associations.index')->with('success', 'Association updated successfully');
    }

    /**
     * Remove the specified association from storage (Admin Only).
     */
    public function destroy(Association $association)
    {
        $this->authorizeAdmin();

        $association->delete();

        return redirect()->route('associations.index')->with('success', 'Association deleted successfully');
    }

    /**
     * Authorize only admin users.
     */
    private function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->user_type !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}
