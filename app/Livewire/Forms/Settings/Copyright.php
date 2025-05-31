<?php

namespace App\Livewire\Forms\Settings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class Copyright extends Form
{
    #[Validate('required|string|max:255', message: [
        'copyright.required' => 'فیلد کپی رایت الزامی است.',
        'copyright.string' => 'فیلد کپی رایت باید متن باشد.',
        'copyright.max' => 'فیلد کپی رایت نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $copyright;
}
