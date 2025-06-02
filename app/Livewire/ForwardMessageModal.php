<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat;
use App\Models\Message;

class ForwardMessageModal extends Component
{
    public $showModal = false;
    public $messageId = null;
    public $search = '';
    public $chats = [];
    public $selectedChat = null;

    protected $listeners = [
        'openForwardModal' => 'openModal'
    ];

    public function mount()
    {
        $this->loadChats();
    }

    public function loadChats()
    {
        $this->chats = auth()->user()->chats()
            ->with(['users', 'lastMessage'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function openModal($messageId)
    {
        $this->messageId = $messageId;
        $this->showModal = true;
        $this->loadChats();
    }

    public function updatedSearch()
    {
        $this->loadChats();
    }

    public function forwardMessage($chatId)
    {
        if (!$this->messageId) return;

        $originalMessage = Message::find($this->messageId);
        if (!$originalMessage) return;

        $chat = Chat::find($chatId);
        if (!$chat) return;

        // Create the forwarded message
        $forwardedMessage = Message::create([
            'chat_id' => $chatId,
            'user_id' => auth()->id(),
            'content' => $originalMessage->content,
            'file_path' => $originalMessage->file_path,
            'file_name' => $originalMessage->file_name,
            'file_type' => $originalMessage->file_type,
            'file_size' => $originalMessage->file_size,
            'original_message_id' => $originalMessage->id,
            'original_sender_id' => $originalMessage->user_id
        ]);

        $this->showModal = false;
        $this->messageId = null;
        $this->search = '';
        $this->selectedChat = null;

        // Dispatch events for UI updates
        $this->dispatch('message-forwarded', chatId: $chatId);
        $this->dispatch('refresh-messages')->to('chat-room');
    }

    public function render()
    {
        return view('livewire.forward-message-modal');
    }
}
