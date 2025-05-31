<?php

namespace App\Livewire\Forms\FileManager;

use Livewire\Attributes\Validate;
use Livewire\Form;

class Create extends Form
{
    #[Validate('required|string|max:255', message: [
        'title.required' => 'فیلد عنوان الزامی است.',
        'title.string' => 'فیلد عنوان باید متن باشد.',
        'title.max' => 'فیلد عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    ])]
    public string $title;

    #[Validate('required|max:512000', message: [
        'file.required' => 'لطفاً یک فایل انتخاب کنید.',
        'file.file' => 'فایل انتخاب شده معتبر نیست.',
        'file.mimes' => 'فقط فایل‌های با پسوند MP4, AVI, MKV, MP3, WAV, JPEG, JPG, BMP, PNG مجاز هستند.',
        'file.max' => 'حجم فایل نباید بیشتر از ۵۰۰ مگابایت باشد.',
    ])]
    public $file;
}
