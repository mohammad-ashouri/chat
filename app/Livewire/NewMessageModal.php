<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class NewMessageModal extends Component
{
    public $showModal = false;
    public $selectedUser = null;
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
            ->get();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = $userId;
        $this->dispatch('userSelected', userId: $userId);
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.new-message-modal');
    }
} 