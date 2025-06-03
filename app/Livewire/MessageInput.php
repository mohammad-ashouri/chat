<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class MessageInput extends Component
{
    use WithFileUploads;

    public $message = '';
    public $files = [];
    public $isUploading = false;
    public $error = null;
    public $replyingTo = null;
    public $selectedMessages = [];
    public Chat $chat;

    protected $listeners = [
        'setChat' => 'setChat',
        'setReplyingTo' => 'setReplyingTo',
        'setSelectedMessages' => 'setSelectedMessages',
        'clearSelectedMessages' => 'clearSelectedMessages'
    ];

    public function mount($chat = null)
    {
        $this->chat = $chat;
    }

    public function setChat($chat)
    {
        $this->chat = Chat::findOrFail($chat['id']);
    }

    public function setReplyingTo($message)
    {
        if (is_array($message)) {
            $this->replyingTo = Message::with('user')->find($message['id']);
        } else {
            $this->replyingTo = $message;
        }
    }

    public function setSelectedMessages($messages)
    {
        $this->selectedMessages = $messages;
    }

    public function clearSelectedMessages()
    {
        $this->selectedMessages = [];
    }

    public function updatedNewFile()
    {
        $this->isUploading = true;
        $this->validate([
            'newFile.*' => 'max:10240', // 10MB Max
        ]);

        foreach ($this->newFile as $file) {
            $this->files[] = $file;
        }

        $this->isUploading = false;
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function sendMessage()
    {
        if (empty($this->message) && empty($this->files)) {
            return;
        }

        if (!$this->chat) {
            $this->error = 'لطفا یک چت را انتخاب کنید';
            return;
        }

        $message = Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => auth()->id(),
            'content' => $this->message,
            'reply_to_id' => $this->replyingTo ? $this->replyingTo->id : null,
        ]);

        if (!empty($this->files)) {
            $filePaths = [];
            $fileNames = [];
            foreach ($this->files as $file) {
                $path = $file->store('chat-files');
                $filePaths[] = $path;
                $fileNames[] = $file->getClientOriginalName();
            }
            $message->update([
                'file_path' => json_encode($filePaths),
                'file_name' => json_encode($fileNames),
            ]);
        }

        $this->message = '';
        $this->files = [];
        $this->replyingTo = null;
        $this->selectedMessages = [];
        $this->error = null;

        $this->dispatch('message-sent')->to('chat-room');
        $this->dispatch('refresh-chat')->to('chat-room');
        $this->dispatch('focus-message-input');
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
    }

    public function dismissError()
    {
        $this->error = null;
    }

    public function getFileTypeLabel($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $documentExtensions = ['doc', 'docx', 'pdf', 'txt', 'rtf'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a'];

        if (in_array($extension, $imageExtensions)) {
            return 'تصویر';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'سند';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'ویدیو';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'صوت';
        } else {
            return 'فایل';
        }
    }

    public function render()
    {
        return view('livewire.message-input');
    }
}
