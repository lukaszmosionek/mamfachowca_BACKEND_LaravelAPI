<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\MessageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
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

    public function fetchMessagedUsers(Request $request): JsonResponse
    {
        $usersYouChattedWith = $this->chatRepository->getMessagedUsers(auth()->id());

        return $this->success( compact('usersYouChattedWith'),
            'Users fetched successfully'
        );
    }

    public function index(User $user): JsonResponse
    {
        $messages = $this->messageRepository->getConversation(authUserId: Auth::id(), receiverId: $user->id);

        return $this->success([
                'messages' => $messages,
                'receiver' => new UserResource($user)
            ], 'Messages fetched successfully'
        );
    }

    // Send a new message to a user
    public function store(StoreMessageRequest $request, User $user, MessageService $messageService): JsonResponse
    {
        $message = $messageService->sendMessage(sender: Auth::user(), receiver: $user, messageBody: $request->message);
        return $this->success( compact('message'), 'Message sent successfully', 201);
    }
}
