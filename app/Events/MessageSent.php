<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;
    public $receiver;

    public function __construct(string $message, User $receiver)
    {
        $this->message = $message;
        $this->receiver = $receiver;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-chat.' . $this->receiver->id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
