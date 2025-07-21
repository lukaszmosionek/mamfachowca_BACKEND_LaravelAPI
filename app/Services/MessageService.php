<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;

class MessageService
{
    public function sendMessage(User $sender, User $receiver, string $messageBody): Message
    {
        $chat = $this->getOrCreateChat($sender, $receiver);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $messageBody,
        ]);

        $chat->update(['last_message_at' => now()]);

        $this->notifyReceiver($receiver, $sender, $message);

        return $message;
    }

    protected function getOrCreateChat(User $sender, User $receiver): Chat
    {
        return Chat::firstOrCreate([
            'user1_id' => min($sender->id, $receiver->id),
            'user2_id' => max($sender->id, $receiver->id),
        ]);
    }

    protected function notifyReceiver(User $receiver, User $sender, Message $message): void
    {
        $receiver->notify(new NewMessageNotification($sender));
        broadcast(new MessageSent($message, $receiver))->toOthers();
    }
}
