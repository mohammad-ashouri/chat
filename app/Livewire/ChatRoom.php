<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

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
        'newMessageModalClosed' => 'handleNewMessageModalClosed'
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
            $query->where('users.id', auth()->id())
                ->orWhere('users.id', $userId);
        })
            ->where('is_group', false)
            ->whereDoesntHave('users', function ($query) {
                $query->where('users.id', '!=', auth()->id());
            })
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

    public function handleChatSelected($chatId)
    {
        $this->selectedChat = Chat::with(['users', 'messages.user'])->find($chatId);
        $this->messages = $this->selectedChat->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // Mark unread messages as read
        if (!$this->selectedChat->isGroup()) {
            $this->selectedChat->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->get()
                ->each
                ->markAsRead();
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

        $this->message = '';
        $this->loadChats();
        $this->handleChatSelected($this->selectedChat->id);
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
