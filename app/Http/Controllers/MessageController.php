<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    use ApiResponse;

    protected ChatRepositoryInterface $chatRepository;
    protected MessageRepositoryInterface $messageRepository;

    public function __construct(ChatRepositoryInterface $chatRepository, MessageRepositoryInterface $messageRepository)
    {
        $this->chatRepository = $chatRepository;
        $this->messageRepository = $messageRepository;
    }

    public function fetchMessagedUsers(Request $request)
    {
        $usersYouChattedWith = $this->chatRepository->getMessagedUsers(auth()->id());

        return $this->success( compact('usersYouChattedWith'),
            'Users fetched successfully'
        );
    }

    public function index(User $user)
    {
        $messages = $this->messageRepository->getConversation(authUserId: Auth::id(), receiverId: $user->id);

        return $this->success([
                'messages' => $messages,
                'receiver' => $user
            ], 'Messages fetched successfully'
        );
    }

    // Send a new message to a user
    public function store(StoreMessageRequest $request, User $user, MessageService $messageService)
    {
        $message = $messageService->sendMessage(sender: Auth::user(), receiver: $user, messageBody: $request->message);
        return $this->success( compact('message'), 'Message sent successfully', 201);
    }
}
