<?php

namespace App\Livewire;

use Livewire\Component;

class AdvancedNotificationSettingsModal extends Component
{
    public $showModal = false;
    public $notificationSound = 'default';
    public $notificationDuration = 'medium';
    public $notificationPosition = 'top-right';
    public $showNotificationPreview = true;
    public $groupNotifications = true;
    public $desktopNotifications = true;
    public $emailNotifications = false;

    protected $listeners = ['openAdvancedNotificationSettings' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function save()
    {
        // Save notification settings to user preferences
        // You can implement the actual saving logic here
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.advanced-notification-settings-modal');
    }
} 