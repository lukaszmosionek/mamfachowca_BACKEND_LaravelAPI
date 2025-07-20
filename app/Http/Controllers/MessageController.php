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

class MessageController extends Controller
{

    use ApiResponse;

    public function fetchMessagedUsers(Request $request)
    {
        // Fetch all users that the authenticated user has chatted with
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
    public function index(User $user)
    {
        $authUser = Auth::user();
        $receiver = $user;

        $messages = Message::where(function ($query) use ($authUser, $receiver) {
            $query->where('sender_id', $authUser->id)
                  ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($authUser, $receiver) {
            $query->where('sender_id', $receiver->id)
                  ->where('receiver_id', $authUser->id);
        })
        ->orderBy('created_at')->get(['body']);

        return response()->json([
            'messages' => $messages,
            'receiver' => $receiver,
        ]);
    }

    // Send a new message to a user
    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $receiver = $user;

        $sender = Auth::user();

        $chat = Chat::firstOrCreate([
            'user1_id' => min($sender->id, $receiver->id),
            'user2_id' => max($sender->id, $receiver->id),
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $request->message,
        ]);

        $chat->last_message_at = now();
        $chat->save();

        // Notify the receiver about the new message
        $receiver->notify( new NewMessageNotification($sender) );
        broadcast(new MessageSent($message, $receiver))->toOthers();

        return $this->success($message, 'Message sent successfully', 201);

    }
}
