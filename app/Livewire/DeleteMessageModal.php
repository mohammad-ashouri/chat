<?php

namespace App\Livewire;

use App\Events\MessageDeleted;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class DeleteMessageModal extends Component
{
    public $showModal = false;
    public $messageId = null;
    public $message = null;
    public $deleteForEveryone = false;

    protected $listeners = ['openDeleteModal'];

    public function openDeleteModal($messageId)
    {
        Log::info('DeleteMessageModal::openDeleteModal called', ['messageId' => $messageId]);
        $this->messageId = $messageId;
        $this->message = Message::with(['user', 'chat', 'originalSender'])->find($messageId);
        $this->showModal = true;
    }

    public function deleteMessage()
    {
        Log::info('DeleteMessageModal::deleteMessage called', [
            'messageId' => $this->messageId,
            'deleteForEveryone' => $this->deleteForEveryone
        ]);

        if (!$this->message) {
            Log::error('Message not found', ['messageId' => $this->messageId]);
            return;
        }

        $isOwnMessage = $this->message->user_id === auth()->id();

        if ($isOwnMessage && $this->deleteForEveryone) {
            // Delete for everyone using softDelete
            $this->message->delete();
            broadcast(new MessageDeleted($this->message->id, $this->message->chat_id))->toOthers();
            Log::info('Message soft deleted for everyone', ['messageId' => $this->message->id]);
        } else {
            // Delete only for current user
            $this->message->markAsDeletedForUser(auth()->id());
            Log::info('Message marked as deleted for current user', ['messageId' => $this->message->id]);
        }

        $this->closeModal();
        $this->dispatch('refresh-messages')->to('chat-room');
    }

    public function closeModal()
    {
        Log::info('DeleteMessageModal::closeModal called');
        $this->showModal = false;
        $this->messageId = null;
        $this->message = null;
        $this->deleteForEveryone = false;
    }

    public function render()
    {
        return view('livewire.delete-message-modal');
    }
}
