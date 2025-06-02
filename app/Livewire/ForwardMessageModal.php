<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat;
use App\Models\Message;

class ForwardMessageModal extends Component
{
    public $showModal = false;
    public $messageIds = [];
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

    public function openModal($messageIds = null)
    {
        $this->messageIds = is_array($messageIds) ? $messageIds : [$messageIds];
        $this->showModal = true;
        $this->loadChats();
    }

    public function loadChats()
    {
        $this->chats = auth()->user()->chats()
            ->with(['users', 'lastMessage'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('users', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%')
                                ->where('users.id', '!=', auth()->id());
                        });
                });
            })
            ->get()
            ->map(function ($chat) {
                if (!$chat->is_group) {
                    $otherUser = $chat->users()
                        ->where('users.id', '!=', auth()->id())
                        ->first();
                    $chat->other_user = $otherUser;
                }
                return $chat;
            });
    }

    public function updatedSearch()
    {
        $this->loadChats();
    }

    public function forwardMessage($chatId)
    {
        if (empty($this->messageIds)) return;

        $chat = Chat::find($chatId);
        if (!$chat) return;

        foreach ($this->messageIds as $messageId) {
            $originalMessage = Message::find($messageId);
            if (!$originalMessage) continue;

            // Create the forwarded message
            Message::create([
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
        }

        $this->showModal = false;
        $this->messageIds = [];
        $this->search = '';
        $this->selectedChat = null;

        // Dispatch events for UI updates
        $this->dispatch('message-forwarded', chatId: $chatId);
        $this->dispatch('refresh-messages')->to('chat-room');
        $this->dispatch('clear-selected-messages')->to('chat-room');
    }

    public function render()
    {
        return view('livewire.forward-message-modal');
    }
}
