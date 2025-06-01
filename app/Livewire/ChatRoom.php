<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\Draft;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\CreateGroupModal;
use App\Livewire\NewMessageModal;
use App\Livewire\GroupManagement;

#[Title('پیام ها')]
class ChatRoom extends Component
{
    use WithFileUploads;

    public $selectedChat = null;
    public $message = '';
    public $messages = [];
    public $chats = [];
    public $users = [];
    public $activeChat;
    public $attachment;
    public $search = '';
    public $isNewMessageModalOpen = false;

    protected $listeners = [
        'chatSelected' => 'handleChatSelected',
        'messageReceived' => 'refreshMessages',
        'userSelected' => 'handleUserSelected',
        'newMessageModalOpened' => 'handleNewMessageModalOpened',
        'newMessageModalClosed' => 'handleNewMessageModalClosed',
        'group-updated' => 'refreshChats',
        'refresh-sidebar' => 'refreshChats',
        'refresh-chat' => 'refreshMessages',
        'group-deleted' => 'handleGroupDeleted'
    ];

    public function mount()
    {
        $this->loadChats();
        $this->loadUsers();
    }

    public function loadChats()
    {
        $this->chats = auth()->user()->chats()
            ->with(['users', 'lastMessage.user'])
            ->get()
            ->sortByDesc(function ($chat) {
                return $chat->lastMessage?->created_at ?? $chat->created_at;
            });
    }

    public function loadUsers()
    {
        // Get users that the current user has chatted with
        $this->users = User::whereHas('chats', function ($query) {
            $query->whereHas('users', function ($q) {
                $q->where('users.id', auth()->id());
            });
        })
            ->where('id', '!=', auth()->id())
            ->get();
    }

    public function handleUserSelected($userId)
    {
        // Check if a direct chat already exists between these users
        $existingChat = Chat::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', auth()->id());
        })
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
        })
            ->where('is_group', false)
            ->first();

        if ($existingChat) {
            $this->handleChatSelected($existingChat->id);
        } else {
            // Create a new chat
            $chat = Chat::create([
                'is_group' => false
            ]);

            // Attach both users to the chat
            $chat->users()->attach([auth()->id(), $userId]);

            $this->loadChats();
            $this->handleChatSelected($chat->id);
        }
    }

    public function handleGroupDeleted($groupId)
    {
        // If the deleted group was selected, clear the selection
        if ($this->selectedChat && $this->selectedChat->id === $groupId) {
            $this->selectedChat = null;
            $this->messages = [];
            $this->message = '';
        }

        // Refresh the chat list
        $this->loadChats();

        // Clear selected chat from localStorage
        $this->dispatch('saveSelectedChat', chatId: null);
    }

    public function handleChatSelected($chatId)
    {
        // Save current draft if exists
        if ($this->selectedChat && !empty($this->message)) {
            $this->saveDraft();
        }

        $chat = Chat::with(['users', 'messages.user'])->find($chatId);

        if (!$chat) {
            $this->selectedChat = null;
            $this->messages = [];
            $this->message = '';
            $this->loadChats();
            return;
        }

        $this->selectedChat = $chat;
        $this->messages = $this->selectedChat->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // Load draft if exists
        $draft = Draft::where('user_id', auth()->id())
            ->where('chat_id', $chatId)
            ->first();

        $this->message = $draft ? $draft->content : '';

        // Mark unread messages as read for both direct and group chats
        $this->selectedChat->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->get()
            ->each
            ->markAsRead();

        // Refresh the chat list to update unread counts
        $this->loadChats();

        // Save selected chat ID to localStorage
        $this->dispatch('saveSelectedChat', chatId: $chatId);
    }

    public function updatedMessage()
    {
        if ($this->selectedChat) {
            if (empty($this->message)) {
                // Delete draft when message is empty
                Draft::where('user_id', auth()->id())
                    ->where('chat_id', $this->selectedChat->id)
                    ->delete();
            } else {
                $this->dispatch('saveDraft')->self();
            }
        }
    }

    public function saveDraft()
    {
        if ($this->selectedChat && !empty($this->message)) {
            Draft::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'chat_id' => $this->selectedChat->id
                ],
                [
                    'content' => $this->message
                ]
            );
        }
    }

    public function sendMessage()
    {
        if (empty($this->message)) {
            return;
        }

        Message::create([
            'chat_id' => $this->selectedChat->id,
            'user_id' => auth()->id(),
            'content' => $this->message
        ]);

        // Delete draft after sending message
        Draft::where('user_id', auth()->id())
            ->where('chat_id', $this->selectedChat->id)
            ->delete();

        $this->message = '';
        $this->loadChats();
        $this->handleChatSelected($this->selectedChat->id);

        $this->dispatch('message-sent');
    }

    public function refreshMessages()
    {
        $this->selectedChat->refresh();
    }

    public function handleNewMessageModalOpened()
    {
        $this->isNewMessageModalOpen = true;
    }

    public function handleNewMessageModalClosed()
    {
        $this->isNewMessageModalOpen = false;
    }

    public function render()
    {
        return view('livewire.chat-room');
    }
}
