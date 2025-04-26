<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssociationController extends Controller
{
    public function index()
    {
        $associations = Association::all();
        return view('associations.index', compact('associations'));
    }

    public function create()
    {
        return view('associations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'creation_date' => 'required|date',
            'logo_url' => 'nullable|file|image|max:2048', // 2MB max
        ]);

        $association = new Association($request->except('logo_url'));

        if ($request->hasFile('logo_url')) {
            $path = $request->file('logo_url')->store('logos', 'public');
            $association->logo_url = $path;
        }

        $association->save();

        return redirect()->route('associations.index')
            ->with('success', 'Association created successfully.');
    }


    public function edit(Association $association)
    {
        return view('associations.edit', compact('association'));
    }

    public function update(Request $request, Association $association)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'creation_date' => 'required|date',
            'logo_url' => 'nullable|file|image|max:2048', // 2MB max
        ]);

        $association->fill($request->except('logo_url'));

        if ($request->hasFile('logo_url')) {
            // Optional: Delete the old logo if you want
            if ($association->logo_url) {
                Storage::disk('public')->delete($association->logo_url);
            }

            $path = $request->file('logo_url')->store('logos', 'public');
            $association->logo_url = $path;
        }

        $association->save();

        return redirect()->route('associations.index')
            ->with('success', 'Association updated successfully.');
    }


    public function destroy(Association $association)
    {
        $association->delete();
        return redirect()->route('associations.index')->with('success', 'Association deleted successfully.');
    }
}
