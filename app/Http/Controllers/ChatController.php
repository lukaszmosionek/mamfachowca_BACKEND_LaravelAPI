<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Get all messages between the authenticated user and the target user
    public function index(User $user)
    {
        $authId = Auth::id();

        $messages = Message::where(function ($query) use ($authId, $user) {
            $query->where('sender_id', $authId)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($authId, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $authId);
        })
        ->orderBy('created_at')->get();

        return response()->json([
            'messages' => $messages,
            'receiver' => $user,
        ]);
    }

    // Send a new message to a user
    public function store(Request $request, $receiver_id)
    {
        $receiver = User::findOrFail($receiver_id);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'body' => $request->message,
        ]);

        broadcast(new MessageSent($message, $receiver->id))->toOthers();

        return response()->json($message, 201);
    }
}
