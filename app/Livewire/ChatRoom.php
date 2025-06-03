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
use App\Livewire\ForwardMessageModal;

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
    public $files = [];
    public $newFile = null;
    public $draft = '';
    public $isUploading = false;
    public $isSending = false;
    public $error = null;
    public $selectedMessages = [];
    public $replyingTo = null;
    public $loading = false;

    protected $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',  // images
        'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm',   // videos
        'mp3', 'wav', 'ogg', 'm4a', 'aac',                  // audio
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',  // documents
        'zip', 'rar', '7z'                                  // archives
    ];

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
        'group-deleted' => 'handleGroupDeleted',
        'message-forwarded' => 'handleMessageForwarded',
        'clear-selected-messages' => 'clearSelectedMessages',
        'message-sent' => '$refresh',
        'forwardSelectedMessages' => 'forwardSelectedMessages',
    ];

    public function mount()
    {
        $this->loadChats();
        $this->loadUsers();

        // Check if there's a selected chat in localStorage
        if (request()->has('selectedChatId')) {
            $this->handleChatSelected(request()->get('selectedChatId'));
        }
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
                ->with(['user', 'originalSender'])
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

    public function selectChat($chatId)
    {
        $this->handleChatSelected($chatId);
    }

    public function handleChatSelected($chatId)
    {
        try {
            // Convert to integer if possible
            $chatId = is_numeric($chatId) ? (int)$chatId : $chatId;

            // If it's an array, try to get the id
            if (is_array($chatId)) {
                $chatId = $chatId['id'] ?? null;
            }

            // If we still don't have a valid ID, return
            if (!$chatId) {
                $this->selectedChat = null;
                $this->messages = collect();
                $this->dispatch('setChat', chat: null)->to('message-input');
                $this->dispatch('saveSelectedChat', chatId: null);
                return;
            }

            $chat = Chat::with(['users', 'messages.user', 'messages.replyTo.user', 'messages.originalSender'])
                ->find($chatId);

            if (!$chat) {
                $this->selectedChat = null;
                $this->messages = collect();
                $this->dispatch('setChat', chat: null)->to('message-input');
                $this->dispatch('saveSelectedChat', chatId: null);
                return;
            }

            $this->selectedChat = $chat;
            $this->messages = $chat->messages()->with(['user', 'replyTo.user', 'originalSender'])->get();
            $this->selectedMessages = [];

            $this->dispatch('setChat', chat: $chat)->to('message-input');
            $this->dispatch('saveSelectedChat', chatId: $chatId);
            $this->dispatch('messages-loaded');

            // Mark messages as read
            $chat->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->get()
                ->each
                ->markAsRead();

            // Refresh chats list
            $this->loadChats();

            // Scroll to bottom after a short delay to ensure DOM is updated
            $this->dispatch('scroll-to-bottom');
        } catch (\Exception $e) {
            \Log::error('Error in handleChatSelected: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'type' => gettype($chatId)
            ]);

            $this->selectedChat = null;
            $this->messages = collect();
            $this->dispatch('setChat', chat: null)->to('message-input');
            $this->dispatch('saveSelectedChat', chatId: null);
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

    public function updatedNewFile()
    {
        if ($this->newFile) {
            // Convert single file to array if needed
            $newFiles = is_array($this->newFile) ? $this->newFile : [$this->newFile];

            // Check if adding new files would exceed the limit
            if (count($this->files) + count($newFiles) > 15) {
                $this->showError('حداکثر 15 فایل می‌توانید آپلود کنید.');
                $this->newFile = null;
                return;
            }

            // Check file extensions
            foreach ($newFiles as $file) {
                $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                if (!in_array($extension, $this->allowedExtensions)) {
                    $this->showError('پسوند فایل ' . strtoupper($extension) . ' مجاز نیست.');
                    $this->newFile = null;
                    return;
                }
            }

            // Add new files to existing files
            foreach ($newFiles as $file) {
                $this->files[] = $file;
            }

            // Clear the newFile property
            $this->newFile = null;
        }
    }

    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files); // Re-index array

            // If no files left, reset upload status
            if (empty($this->files)) {
                $this->isUploading = false;
                $this->isSending = false;
            }
        }
    }

    public function sendMessage()
    {
        if (!$this->selectedChat) return;
        if (empty($this->message) && empty($this->files)) return;

        try {
            $this->isSending = true;
            $filePaths = [];
            $fileNames = [];
            $fileTypes = [];
            $fileSizes = [];

            if (!empty($this->files)) {
                $this->isUploading = true;
                foreach ($this->files as $file) {
                    $filePaths[] = $file->store('chat-files');
                    $fileNames[] = $file->getClientOriginalName();
                    $fileTypes[] = $file->getMimeType();
                    $fileSizes[] = $file->getSize();
                }
            }

            $message = Message::create([
                'chat_id' => $this->selectedChat->id,
                'user_id' => auth()->id(),
                'content' => $this->message,
                'file_path' => !empty($filePaths) ? json_encode($filePaths) : null,
                'file_name' => !empty($fileNames) ? json_encode($fileNames) : null,
                'file_type' => !empty($fileTypes) ? json_encode($fileTypes) : null,
                'file_size' => !empty($fileSizes) ? json_encode($fileSizes) : null,
                'reply_to_id' => $this->replyingTo ? $this->replyingTo->id : null,
            ]);

            // Clear the message, files and reply
            $this->message = '';
            $this->files = [];
            $this->replyingTo = null;
            $this->isUploading = false;
            $this->isSending = false;

            // Delete draft
            Draft::where('user_id', auth()->id())
                ->where('chat_id', $this->selectedChat->id)
                ->delete();

            // Refresh messages
            $this->loadMessages();
            $this->loadChats();

            // Dispatch event for real-time updates
            $this->dispatch('message-sent');

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ارسال پیام: ' . $e->getMessage());
            $this->isUploading = false;
            $this->isSending = false;
        }
    }

    public function refreshMessages()
    {
        if ($this->selectedChat) {
            $this->selectedChat->refresh();
            $this->loadMessages();
            $this->scrollToBottom();
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

        // Save selected chat ID to localStorage
        $this->dispatch('saveSelectedChat', chatId: $existingChat ? $existingChat->id : $chat->id);
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function handleMessageForwarded($chatId)
    {
        \Log::info('ChatRoom::handleMessageForwarded called', ['chatId' => $chatId]);

        // اگر چت فعلی همان چتی است که پیام به آن فوروارد شده، پیام‌ها را رفرش کن
        if ($this->selectedChat && $this->selectedChat->id === $chatId) {
            $this->refreshMessages();
        }

        // لیست چت‌ها را رفرش کن
        $this->loadChats();

        // پیام‌های انتخاب شده را پاک کن
        $this->selectedMessages = [];
    }

    public function openForwardModal($messageId)
    {
        \Log::info('ChatRoom::openForwardModal called', ['messageId' => $messageId]);
        $this->dispatch('openForwardModal', messageId: $messageId)->to('forward-message-modal');
    }

    public function forwardSelectedMessages()
    {
        if (count($this->selectedMessages) > 0) {
            $this->dispatch('openForwardModal', messageIds: $this->selectedMessages);
        }
    }

    public function clearSelectedMessages()
    {
        $this->selectedMessages = [];
        $this->dispatch('clearSelectedMessages')->to('message-input');
    }

    public function toggleMessageSelection($messageId)
    {
        if (in_array($messageId, $this->selectedMessages)) {
            $this->selectedMessages = array_diff($this->selectedMessages, [$messageId]);
        } else {
            $this->selectedMessages[] = $messageId;
        }
    }

    public function replyToMessage($messageId)
    {
        $message = Message::with('user')->find($messageId);
        if ($message) {
            $this->dispatch('setReplyingTo', message: $message)->to('message-input');
        }
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
    }

    public function scrollToMessage($messageId)
    {
        $this->dispatch('scroll-to-message', messageId: $messageId);
    }

    public function scrollToBottom()
    {
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.chat-room', [
            'forwardMessageModal' => new ForwardMessageModal()
        ]);
    }

    private function getFileTypeLabel($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

        if (in_array($extension, $imageExtensions)) {
            return 'دانلود عکس';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'دانلود فیلم';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'دانلود صوت';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'دانلود فایل';
        } else {
            return 'دانلود ' . strtoupper($extension);
        }
    }

    public function showError($message)
    {
        $this->error = $message;
        $this->dispatch('show-error');
    }

    public function dismissError()
    {
        $this->error = null;
    }
}
