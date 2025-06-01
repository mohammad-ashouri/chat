<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class GroupManagement extends Component
{
    use WithFileUploads;

    public $group;
    public $groupName;
    public $isAdmin = false;
    public $search = '';
    public $selectedUsers = [];
    public $availableUsers = [];
    public $showModal = false;
    public $showDeleteModal = false;
    public $showSuccessModal = false;

    protected $rules = [
        'groupName' => 'required|min:3|max:255',
        'group.is_public' => 'boolean',
    ];

    public function mount(Chat $group)
    {
        $this->group = $group;
        $this->groupName = $group->name;
        $this->isAdmin = $group->users->contains(auth()->id());
        $this->loadAvailableUsers();
    }

    public function loadAvailableUsers()
    {
        $this->availableUsers = User::where('id', '!=', auth()->id())
            ->whereNotIn('id', $this->group->users->pluck('id'))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->get();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->dispatch('open-modal', 'group-management');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->dispatch('close-modal', 'group-management');
    }

    public function updatedSearch()
    {
        $this->loadAvailableUsers();
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function addSelectedMembers()
    {
        if (!empty($this->selectedUsers)) {
            $this->group->users()->attach($this->selectedUsers);
            $this->selectedUsers = [];
            $this->search = '';
            $this->loadAvailableUsers();
            $this->dispatch('member-added');
        }
    }

    public function removeMember($userId)
    {
        if ($this->isAdmin) {
            $this->group->users()->detach($userId);
            $this->loadAvailableUsers();
            $this->dispatch('member-removed');
        }
    }

    public function saveChanges()
    {
        $this->validate();

        $this->group->name = $this->groupName;
        $this->group->save();

        $this->showModal = false;
        $this->showSuccessModal = true;

        $this->dispatch('close-modal', 'group-management');
        $this->dispatch('group-updated');
        $this->dispatch('refresh-sidebar');
        $this->dispatch('refresh-chat');
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->dispatch('close-modal', 'success-modal');
        $this->dispatch('refresh-sidebar');
        $this->dispatch('refresh-chat');
    }

    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
        $this->dispatch('open-modal', 'delete-group-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->dispatch('close-modal', 'delete-group-modal');
    }

    public function deleteGroup()
    {
        if (!$this->isAdmin) {
            return;
        }

        // Delete group profile image if exists
        if ($this->group->profile_photo_path) {
            Storage::disk('public')->delete($this->group->profile_photo_path);
        }

        // Store the group ID before deletion
        $groupId = $this->group->id;

        // Delete the group
        $this->group->delete();

        // Close the modals
        $this->showModal = false;
        $this->showDeleteModal = false;

        // Dispatch events for UI updates
        $this->dispatch('group-deleted', groupId: $groupId)->to('chat-room');
        $this->dispatch('refresh-sidebar')->to('chat-room');
        $this->dispatch('refresh-chat')->to('chat-room');

        // Redirect to chat page without refresh
        return redirect()->route('chat', [], false);
    }

    public function render()
    {
        $this->loadAvailableUsers();
        return view('livewire.group-management', [
            'availableUsers' => $this->availableUsers
        ]);
    }
}
