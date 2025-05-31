<?php

namespace App\Livewire\Forms\Settings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class Banners extends Form
{
    #[Validate('nullable|file|mimes:png,jpg,jpeg,bmp', message: [
        'iranic_media.file' => 'فیلد بنر ایرانیک مدیا باید از نوع فایل باشد.',
        'iranic_media.mimes' => 'فرمت بنر ایرانیک مدیا باید از نوع png,jpg,jpeg,bmp باشد.',
    ])]
    public $iranic_media;

    #[Validate('nullable|file|mimes:png,jpg,jpeg,bmp', message: [
        'hottest.file' => 'فیلد بنر داغ ترین باید از نوع فایل باشد.',
        'hottest.mimes' => 'فرمت بنر داغ ترین باید از نوع png,jpg,jpeg,bmp باشد.',
    ])]
    public $hottest;

    #[Validate('nullable|file|mimes:png,jpg,jpeg,bmp', message: [
        'survey.file' => 'فیلد بنر پویش باید از نوع فایل باشد.',
        'survey.mimes' => 'فرمت بنر پویش باید از نوع png,jpg,jpeg,bmp باشد.',
    ])]
    public $survey;
}
