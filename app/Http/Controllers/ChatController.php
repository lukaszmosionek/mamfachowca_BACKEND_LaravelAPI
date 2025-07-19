<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    use ApiResponse;

    public function index(Request $request)
    {
        // Fetch all users that the authenticated user has chatted with
        // This assumes that the authenticated user is the one who is logged in
        $chatUserIds = Chat::where('user1_id', auth()->id())
            ->orWhere('user2_id', auth()->id())
            ->latest('last_message_at')
            ->get()
            ->map(function ($chat) {
                return $chat->user1_id === auth()->id() ? $chat->user2_id : $chat->user1_id;
            })
            ->unique()
            ->values();

        $usersYouChattedWith = User::whereIn('id', $chatUserIds)->get(['id', 'name', 'email'])
            ->sortBy(fn($user) => array_search( $user->id, $chatUserIds->toArray() ))
            ->values();

        return $this->success($usersYouChattedWith, 'Users fetched successfully');
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
        ->orderBy('created_at')->get(['body']);

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

        $sender_id = Auth::id();

        $chat = Chat::firstOrCreate([
            'user1_id' => min($sender_id, $receiver->id),
            'user2_id' => max($sender_id, $receiver->id),
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver->id,
            'body' => $request->message,
        ]);

        $chat->last_message_at = now();
        $chat->save();

        // Notify the receiver about the new message
        $receiver->notify( new NewMessageNotification() );
        broadcast(new MessageSent($message, $receiver->id))->toOthers();

        return response()->json($message, 201);

    }
}
