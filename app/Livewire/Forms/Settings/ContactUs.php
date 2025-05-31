<?php

namespace App\Livewire\Forms\Settings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ContactUs extends Form
{
    #[Validate('required|string|max:255', message: [
        'hemayat.required' => 'فیلد حمایت از ایرانیک تی‌وی الزامی است.',
        'hemayat.string' => 'فیلد حمایت از ایرانیک تی‌وی باید متن باشد.',
        'hemayat.max' => 'فیلد حمایت از ایرانیک تی‌وی نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $hemayat;
    #[Validate('required|string|max:255', message: [
        'hamkari.required' => 'فیلد همکاری با ایرانیک تی‌وی الزامی است.',
        'hamkari.string' => 'فیلد همکاری با ایرانیک تی‌وی باید متن باشد.',
        'hamkari.max' => 'فیلد همکاری با ایرانیک تی‌وی نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $hamkari;
}
