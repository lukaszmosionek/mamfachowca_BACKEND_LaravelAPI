<?php

use App\Events\TestMessageSent;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId || $user->canChatWith($receiverId);
});



