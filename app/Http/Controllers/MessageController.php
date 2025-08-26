<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Services\MessageService;
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

        return $this->success( compact('usersYouChattedWith'), 'Users fetched successfully');
    }

    // Get all messages between the authenticated user and the target user
    public function index(User $user)
    {
        $authUser = Auth::user();
        $receiver = $user;

        $messages = Message::select(['sender_id','receiver_id','body'])->where(function ($query) use ($authUser, $receiver) {
            $query->where('sender_id', $authUser->id)
                  ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($authUser, $receiver) {
            $query->where('sender_id', $receiver->id)
                  ->where('receiver_id', $authUser->id);
        })
        ->orderBy('created_at')->get();

        return $this->success( compact('messages', 'receiver'), 'Messages fetch successfully');
    }

    // Send a new message to a user
    public function store(Request $request, User $user, MessageService $messageService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $receiver = $user;
        $sender = Auth::user();

        $message = $messageService->sendMessage($sender, $receiver, $request->message);

        return $this->success( compact('message'), 'Message sent successfully', 201);
    }
}
