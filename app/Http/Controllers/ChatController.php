<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewNotification;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    use ApiResponse;

    public function index(Request $request)
    {
        $authId = auth()->id(); // or however you get the auth user ID
        $users = [];

        $messages = Message::where('sender_id', $authId)->orWhere('receiver_id', $authId)
                        ->orderBy('created_at', 'DESC')
                        ->get(['sender_id', 'receiver_id']);

        foreach ($messages as $message) {
                if ($message->sender_id !== $authId) {
                    $users[] = $message->sender_id;
                } elseif ($message->receiver_id !== $authId) {
                    $users[] = $message->receiver_id;
                }
        }

        $users = array_unique($users);

        $users = User::whereIn('id', $users)->get(['id', 'name', 'email']);

        return $this->success($users, 'Users fetched successfully');
    }

    // Get all messages between the authenticated user and the target user
    public function show($receiverId)
    {
        $authId = Auth::id();
        $receiver = User::find($receiverId);

        if (!$receiver) {
            return $this->error('Chat user not found', 404);
        }

        $messages = Message::where(function ($query) use ($authId, $receiver) {
            $query->where('sender_id', $authId)
                  ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($authId, $receiver) {
            $query->where('sender_id', $receiver->id)
                  ->where('receiver_id', $authId);
        })
        ->orderBy('created_at')->get();

        return response()->json([
            'messages' => $messages,
            'receiver' => $receiver,
        ]);
    }

    // Send a new message to a user
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $receiver = User::find($request->receiver_id);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'body' => $request->message,
        ]);

        // Notify the receiver about the new message
        $receiver->notify( new NewMessageNotification() );
        broadcast(new MessageSent($message, $receiver->id))->toOthers();

        return response()->json($message, 201);

    }
}
