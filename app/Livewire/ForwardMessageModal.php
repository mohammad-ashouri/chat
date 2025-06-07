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
        // Get all users except the current user
        $users = \App\Models\User::where('id', '!=', auth()->id())
            ->when($this->search, function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
            })
            ->get()
            ->map(function ($user) {
                return (object)[
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_group' => false,
                    'other_user' => $user
                ];
            });

        // Get all groups
        $groups = \App\Models\Chat::where('is_group', true)
            ->when($this->search, function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
            })
            ->get()
            ->map(function ($group) {
                return (object)[
                    'id' => $group->id,
                    'name' => $group->name,
                    'is_group' => true
                ];
            });

        // Combine users and groups
        $this->chats = $users->concat($groups);
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

        // Check if the chatId is a user ID (direct message) or a group ID
        $isDirectMessage = \App\Models\User::where('id', $chatId)->exists();

        if ($isDirectMessage) {
            // Find or create a direct chat with this user
            $chat = \App\Models\Chat::whereHas('users', function ($query) use ($chatId) {
                $query->where('users.id', auth()->id());
            })
                ->whereHas('users', function ($query) use ($chatId) {
                    $query->where('users.id', $chatId);
                })
                ->where('is_group', false)
                ->first();

            if (!$chat) {
                // Create a new direct chat
                $chat = \App\Models\Chat::create([
                    'is_group' => false
                ]);
                $chat->users()->attach([auth()->id(), $chatId]);
            }
        } else {
            // Get the group chat
            $chat = \App\Models\Chat::find($chatId);
            if (!$chat) {
                \Log::error('Chat not found', ['chatId' => $chatId]);
                return;
            }
        }

        $forwardedCount = 0;
        foreach ($this->messageIds as $messageId) {
            $originalMessage = \App\Models\Message::find($messageId);
            if (!$originalMessage) {
                \Log::error('Original message not found', ['messageId' => $messageId]);
                continue;
            }

            // Create the forwarded message
            \App\Models\Message::create([
                'chat_id' => $chat->id,
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
        $this->dispatch('message-forwarded', chatId: $chat->id);
        $this->dispatch('refresh-messages')->to('chat-room');
        $this->dispatch('clear-selected-messages')->to('chat-room');

        \Log::info('ForwardMessageModal::forwardMessage completed', ['chatId' => $chat->id]);
    }

    public function render()
    {
        return view('livewire.forward-message-modal');
    }
}
