<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Recipient_Offers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipientRequest extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'association_id' => 'required|exists:associations,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $recipient = User::where('id', $request->user_id)
            ->where('user_type', 'recipient')
            ->first();

        if (!$recipient) {
            return response()->json(['error' => 'Invalid recipient user_id'], 422);
        }

        $offer = Recipient_Offers::create([
            'association_id' => $request->association_id,
            'user_id' => $recipient->id,
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
        $offers = Recipient_Offers::where('association_id', $associationId)
            ->with('user') // Optional: include recipient information
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
        $offer = Recipient_Offers::where('id', $offerId)
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
}
