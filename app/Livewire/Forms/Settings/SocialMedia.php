<?php

namespace App\Livewire\Forms\Settings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class SocialMedia extends Form
{
    #[Validate('nullable|string', message: [
        'eitaa.string' => 'فیلد ایتا باید متن باشد.',
    ])]
    public string $eitaa;

    #[Validate('nullable|string', message: [
        'telegram.string' => 'فیلد تلگرام باید متن باشد.',
    ])]
    public string $telegram;

    #[Validate('nullable|string', message: [
        'x.string' => 'فیلد x باید متن باشد.',
    ])]
    public string $x;

    #[Validate('nullable|string', message: [
        'instagram.string' => 'فیلد اینستاگرام باید متن باشد.',
    ])]
    public string $instagram;

    #[Validate('nullable|string', message: [
        'youtube.string' => 'فیلد یوتیوب باید متن باشد.',
    ])]
    public string $youtube;

    #[Validate('nullable|string', message: [
        'tiktok.string' => 'فیلد تیک تاک باید متن باشد.',
    ])]
    public string $tiktok;

    #[Validate('nullable|string', message: [
        'rubika.string' => 'فیلد روبیکا باید متن باشد.',
    ])]
    public string $rubika;

    #[Validate('nullable|string', message: [
        'bale.string' => 'فیلد بله باید متن باشد.',
    ])]
    public string $bale;

    #[Validate('nullable|string', message: [
        'soroush.string' => 'فیلد سروش باید متن باشد.',
    ])]
    public string $soroush;

}
