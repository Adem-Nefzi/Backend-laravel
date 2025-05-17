<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Get conversation between user and association
    public function getConversation(Request $request, $associationId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $association = Association::find($associationId);
        if (!$association) {
            return response()->json(['error' => 'Association not found'], 404);
        }

        $messages = Message::where(function ($query) use ($user, $associationId) {
            $query->where('sender_id', $user->id)
                ->where('sender_type', get_class($user))
                ->where('receiver_id', $associationId);
        })->orWhere(function ($query) use ($user, $associationId) {
            $query->where('receiver_id', $user->id)
                ->where('sender_id', $associationId);
        })->orderBy('sent_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'association' => $association->only(['id', 'name']),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type
            ]
        ]);
    }

    // Send a message to association
    public function sendToAssociation(Request $request, $associationId)
    {
        $request->validate([
            'message_content' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $association = Association::find($associationId);
        if (!$association) {
            return response()->json(['error' => 'Association not found'], 404);
        }
        $message = Message::create([
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'receiver_id' => $association->id,
            'receiver_type' => get_class($association),
            'message_content' => $request->message_content,
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => $message,
            'status' => 'Message sent successfully'
        ], 201);
    }

    // Association sends message to user
    public function sendToUser(Request $request, $userId)
    {
        $request->validate([
            'message_content' => 'required|string|max:1000',
        ]);

        $association = Auth::guard('association')->user();
        if (!$association) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $message = Message::create([
            'sender_id' => $association->id,
            'sender_type' => get_class($association),
            'receiver_id' => $user->id,
            'receiver_type' => get_class($user),
            'message_content' => $request->message_content,
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => $message,
            'status' => 'Message sent successfully'
        ], 201);
    }

    // Mark messages as read
    public function markAsRead(Request $request, $senderId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Message::where('receiver_id', $user->id)
            ->where('sender_id', $senderId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'Messages marked as read']);
    }


    public function getAssociationReceivedMessages(Request $request)
    {
        // Get the authenticated association
        $association = Auth::guard('association')->user();

        if (!$association) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all messages where association is the receiver
        $messages = Message::where('receiver_id', $association->id)
            ->with('sender') // Eager load sender info
            ->orderBy('sent_at', 'desc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'total_unread' => $messages->whereNull('read_at')->count()
        ]);
    }
}
