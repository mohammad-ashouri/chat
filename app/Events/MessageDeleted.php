<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $chatId;

    public function __construct($messageId, $chatId)
    {
        $this->messageId = $messageId;
        $this->chatId = $chatId;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->chatId);
    }

    public function broadcastAs()
    {
        return 'message.deleted';
    }
} 