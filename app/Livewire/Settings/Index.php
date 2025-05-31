<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('تنظیمات سایت')]
class Index extends Component
{
    public function render()
    {
        if (!auth()->user()->can('تنظیمات سایت')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('livewire.settings.index');
    }
}
