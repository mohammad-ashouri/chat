<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Chat;

class CreateGroupModal extends Component
{
    public $showModal = false;
    public $groupName = '';
    public $selectedUsers = [];
    public $search = '';
    public $users = [];

    public function mount()
    {
        $this->users = User::where('id', '!=', auth()->id())->get();
    }

    public function updatedSearch()
    {
        $this->users = User::where('id', '!=', auth()->id())
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->get();
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function createGroup()
    {
        $this->validate([
            'groupName' => 'required|min:3',
            'selectedUsers' => 'required|array|min:1'
        ]);

        $chat = Chat::create([
            'name' => $this->groupName,
            'is_group' => true,
            'user_id' => auth()->id()
        ]);

        $chat->users()->attach(array_merge($this->selectedUsers, [auth()->id()]));

        $this->dispatch('chatSelected', chatId: $chat->id);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->groupName = '';
        $this->selectedUsers = [];
        $this->search = '';
        $this->dispatch('modalClosed');
    }

    public function render()
    {
        return view('livewire.create-group-modal');
    }
}
