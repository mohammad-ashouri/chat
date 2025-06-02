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
        \Log::info('ForwardMessageModal::mount called');
        $this->loadChats();
    }

    public function openModal($messageId = null)
    {
        \Log::info('ForwardMessageModal::openModal called', ['messageId' => $messageId]);

        if ($messageId) {
            $this->messageIds = [$messageId];
        }

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
        \Log::info('ForwardMessageModal::forwardMessage called', ['chatId' => $chatId, 'messageIds' => $this->messageIds]);

        if (empty($this->messageIds)) {
            \Log::error('No message IDs provided for forwarding');
            return;
        }

        $chat = Chat::find($chatId);
        if (!$chat) {
            \Log::error('Chat not found', ['chatId' => $chatId]);
            return;
        }

        $forwardedCount = 0;
        foreach ($this->messageIds as $messageId) {
            $originalMessage = Message::find($messageId);
            if (!$originalMessage) {
                \Log::error('Original message not found', ['messageId' => $messageId]);
                continue;
            }

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

            $forwardedCount++;
        }

        \Log::info('Messages forwarded successfully', ['count' => $forwardedCount]);

        $this->showModal = false;
        $this->messageIds = [];
        $this->search = '';
        $this->selectedChat = null;

        // Dispatch events for UI updates
        $this->dispatch('message-forwarded', chatId: $chatId);
        $this->dispatch('refresh-messages')->to('chat-room');
        $this->dispatch('clear-selected-messages')->to('chat-room');

        \Log::info('ForwardMessageModal::forwardMessage completed', ['chatId' => $chatId]);
    }

    public function render()
    {
        return view('livewire.forward-message-modal');
    }
}
