<?php

namespace App\Livewire\User;

use Livewire\Attributes\Title;
use Livewire\Component;

class Profile extends Component
{
    #[Title('پروفایل')]
    public function render()
    {
        return view('livewire.user.profile');
    }
}
