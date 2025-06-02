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
use Illuminate\Support\Facades\Storage;

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
    public $file = null;
    public $draft = '';
    public $isUploading = false;
    public $isSending = false;

    protected $listeners = [
        'chatSelected' => 'loadChat',
        'messageReceived' => 'refreshMessages',
        'userSelected' => 'handleUserSelected',
        'newMessageModalOpened' => 'handleNewMessageModalOpened',
        'newMessageModalClosed' => 'handleNewMessageModalClosed',
        'group-updated' => 'refreshChats',
        'refresh-sidebar' => 'refreshChats',
        'refresh-chat' => 'refreshMessages',
        'refresh-messages' => 'refreshMessages',
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
        $this->users = User::where('id', '!=', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function loadChat($chatId)
    {
        $this->selectedChat = Chat::with(['messages.user', 'users'])
            ->findOrFail($chatId);
        $this->loadMessages();
        $this->loadDraft();
    }

    public function loadMessages()
    {
        if ($this->selectedChat) {
            $this->messages = $this->selectedChat->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
        }
    }

    public function loadDraft()
    {
        if ($this->selectedChat) {
            $draft = Message::where('chat_id', $this->selectedChat->id)
                ->where('user_id', auth()->id())
                ->where('is_draft', true)
                ->latest()
                ->first();

            if ($draft) {
                $this->message = $draft->content;
            }
        }
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

        // Dispatch update-group event for group management
        if ($chat->is_group) {
            $this->dispatch('update-group', chatId: $chatId);
        }
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
                $this->saveDraft();
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

    public function updatedFile()
    {
        if ($this->file) {
            $this->isUploading = true;
            $this->isSending = true;
        }
    }

    public function sendMessage()
    {
        if (!$this->selectedChat) return;
        if (empty($this->message) && !$this->file) return;

        try {
            $this->isSending = true;
            $filePath = null;
            $fileName = null;
            $fileType = null;
            $fileSize = null;

            if ($this->file) {
                $this->isUploading = true;
                $filePath = $this->file->store('chat-files');
                $fileName = $this->file->getClientOriginalName();
                $fileType = $this->file->getClientMimeType();
                $fileSize = $this->file->getSize();
                $this->isUploading = false;
            }

            $message = Message::create([
                'chat_id' => $this->selectedChat->id,
                'user_id' => auth()->id(),
                'content' => $this->message ?: $fileName,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'is_draft' => false
            ]);

            $this->message = '';
            $this->file = null;

            // Delete draft
            Draft::where('user_id', auth()->id())
                ->where('chat_id', $this->selectedChat->id)
                ->delete();

            $this->loadMessages();
            $this->isSending = false;
        } catch (\Exception $e) {
            $this->isSending = false;
            $this->isUploading = false;
            session()->flash('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    public function refreshMessages()
    {
        if ($this->selectedChat) {
            $this->selectedChat->refresh();
            $this->loadMessages();
        }
    }

    public function handleNewMessageModalOpened()
    {
        $this->isNewMessageModalOpen = true;
    }

    public function handleNewMessageModalClosed()
    {
        $this->isNewMessageModalOpen = false;
    }

    public function startChat($userId)
    {
        $chat = Chat::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })->first();

        if (!$chat) {
            $chat = Chat::create();
            $chat->users()->attach([auth()->id(), $userId]);
        }

        $this->loadChat($chat->id);
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.chat-room');
    }
}
