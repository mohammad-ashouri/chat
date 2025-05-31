<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('پیام ها')]
class ChatRoom extends Component
{
    use WithFileUploads;

    public $chats;
    public $activeChat;
    public $message = '';
    public $attachment;
    public $search = '';

    protected $listeners = ['messageReceived' => 'refreshMessages'];

    public function mount()
    {
        $this->loadChats();
        $this->activeChat = $this->chats->first();
    }

    public function loadChats()
    {
        $this->chats = Auth::user()->chats()
            ->with(['users', 'lastMessage'])
            ->when($this->search, function ($query) {
                $query->whereHas('users', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function selectChat($chatId)
    {
        $this->activeChat = Chat::with(['messages.user', 'users'])->find($chatId);
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required_without:attachment',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $message = new Message();
        $message->user_id = Auth::id();
        $message->chat_id = $this->activeChat->id;
        $message->content = $this->message;

        if ($this->attachment) {
            $path = $this->attachment->store('attachments', 'public');
            $message->attachment = $path;
            $message->attachment_type = $this->attachment->getMimeType();
        }

        $message->save();

        $this->activeChat->touch();
        $this->activeChat->refresh();
        $this->message = '';
        $this->attachment = null;

        $this->dispatch('messageSent');
    }

    public function refreshMessages()
    {
        $this->activeChat->refresh();
    }

    public function render()
    {
        return view('livewire.chat-room');
    }
}
