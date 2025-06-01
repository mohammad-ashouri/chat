<?php

namespace App\Livewire;

use Livewire\Component;

class AdvancedSecuritySettingsModal extends Component
{
    public $showModal = false;
    public $twoFactorMethod = 'sms';
    public $loginAttempts = '5';
    public $lockoutDuration = '30';
    public $rememberDevice = true;
    public $loginNotifications = true;
    public $deviceManagement = true;
    public $sessionTimeout = true;

    protected $listeners = ['openAdvancedSecuritySettings' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function save()
    {
        // Save security settings to user preferences
        // You can implement the actual saving logic here
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.advanced-security-settings-modal');
    }
} 