<?php

namespace App\Livewire;

use Livewire\Component;

class AdvancedLanguageSettingsModal extends Component
{
    public $showModal = false;

    protected $listeners = ['openAdvancedLanguageSettings' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }
}
