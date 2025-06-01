<?php

namespace App\Livewire;

use Livewire\Component;

class AdvancedThemeSettingsModal extends Component
{
    public $showModal = false;
    public $theme = 'system';
    public $accentColor = 'blue';
    public $fontSize = 'medium';
    public $fontFamily = 'vazir';
    public $compactMode = false;
    public $animations = true;
    public $roundedCorners = true;
    public $shadows = true;

    protected $listeners = ['openAdvancedThemeSettings' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function save()
    {
        // Save theme settings to user preferences
        // You can implement the actual saving logic here
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.advanced-theme-settings-modal');
    }
} 