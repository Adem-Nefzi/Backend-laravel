<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'association_id' => 'required|exists:associations,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $donor = User::where('id', $request->user_id)
            ->where('user_type', 'donor')
            ->first();

        if (!$donor) {
            return response()->json(['error' => 'Invalid donor user_id'], 422);
        }

        $offer = Offer::create([
            'association_id' => $request->association_id,
            'user_id' => $donor->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Offer created successfully',
            'offer' => $offer
        ], 201);
    }

    // Get all offers for a specific association
    public function getAssociationOffers(Request $request, $associationId)
    {
        // Validate that the association exists
        $association = Association::find($associationId);
        if (!$association) {
            return response()->json(['error' => 'Association not found'], 404);
        }

        // Get all offers for this association
        $offers = Offer::where('association_id', $associationId)
            ->with('user') // Optional: include donor information
            ->get();

        return response()->json([
            'offers' => $offers
        ], 200);
    }

    //Update the status of an offer
    public function updateOfferStatus(Request $request, $offerId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'association_id' => 'required|exists:associations,id'
        ]);

        // Find the offer and verify it belongs to the requesting association
        $offer = Offer::where('id', $offerId)
            ->where('association_id', $request->association_id)
            ->first();

        if (!$offer) {
            return response()->json(['error' => 'Offer not found or not authorized'], 404);
        }

        // Only allow status change if current status is pending
        if ($offer->status !== 'pending') {
            return response()->json(['error' => 'Offer status can only be changed from pending'], 422);
        }

        $offer->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Offer status updated successfully',
            'offer' => $offer
        ], 200);
    }


    // Get all offers made by the authenticated donor
    public function getDonorOffers(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Verify the user is a donor
        if ($user->user_type !== 'donor') {
            return response()->json(['error' => 'Only donors can view their offers'], 403);
        }

        // Get all offers made by this donor with association information
        $offers = Offer::where('user_id', $user->id)
            ->with('association') // Include association information
            ->orderBy('created_at', 'desc') // Show most recent first
            ->get();

        return response()->json([
            'offers' => $offers
        ], 200);
    }
}
