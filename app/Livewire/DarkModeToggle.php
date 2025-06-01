<?php

namespace App\Livewire;

use Livewire\Component;

class DarkModeToggle extends Component
{
    public $darkMode = true;

    public function mount()
    {
        $this->darkMode = session('darkMode', true);
        if ($this->darkMode) {
            $this->enableDarkMode();
        } else {
            $this->disableDarkMode();
        }
    }

    private function enableDarkMode()
    {
        $this->dispatch('dark-mode-enabled');
    }

    private function disableDarkMode()
    {
        $this->dispatch('dark-mode-disabled');
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['darkMode' => $this->darkMode]);

        if ($this->darkMode) {
            $this->enableDarkMode();
        } else {
            $this->disableDarkMode();
        }
    }

    public function render()
    {
        return view('livewire.dark-mode-toggle');
    }
}
