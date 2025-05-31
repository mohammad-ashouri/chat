<?php

namespace App\Livewire\Forms\Settings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class AboutUs extends Form
{
    #[Validate('required|string|max:255', message: [
        'raahbari.required' => 'فیلد راهبری ایرانیک تی‌وی الزامی است.',
        'raahbari.string' => 'فیلد راهبری ایرانیک تی‌وی باید متن باشد.',
        'raahbari.max' => 'فیلد راهبری ایرانیک تی‌وی نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $raahbari;
    #[Validate('required|string|max:255', message: [
        'resalat.required' => 'فیلد رسالت ایرانیک تی‌وی الزامی است.',
        'resalat.string' => 'فیلد رسالت ایرانیک تی‌وی باید متن باشد.',
        'resalat.max' => 'فیلد رسالت ایرانیک تی‌وی نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $resalat;
    #[Validate('required|string|max:255', message: [
        'ghavanin.required' => 'فیلد قوانین و خط‌مشی‌ها الزامی است.',
        'ghavanin.string' => 'فیلد قوانین و خط‌مشی‌ها باید متن باشد.',
        'ghavanin.max' => 'فیلد قوانین و خط‌مشی‌ها نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $ghavanin;
    #[Validate('required|string|max:255', message: [
        'masooliat.required' => 'فیلد قوانین و خط‌مشی‌ها الزامی است.',
        'masooliat.string' => 'فیلد قوانین و خط‌مشی‌ها باید متن باشد.',
        'masooliat.max' => 'فیلد قوانین و خط‌مشی‌ها نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $masooliat;
}
