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

    protected $listeners = [
        'chatSelected' => 'handleChatSelected',
        'messageReceived' => 'refreshMessages'
    ];

    public function mount()
    {
        $this->loadChats();
        $this->loadUsers();
    }

    public function loadChats()
    {
        $this->chats = Chat::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })->with(['users', 'messages' => function ($query) {
            $query->latest();
        }])->get();
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

    public function handleChatSelected($chatId)
    {
        $this->selectedChat = Chat::with(['users', 'messages.user'])->find($chatId);
        $this->messages = $this->selectedChat->messages()->with('user')->orderBy('created_at', 'asc')->get();
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

    public function render()
    {
        return view('livewire.chat-room');
    }
}
