<?php

namespace App\Livewire\Forms\Survey;

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

    public $survey_formats;

    #[Validate('required|string|max:1024', message: [
        'short_title.required' => 'فیلد توضیح کوتاه الزامی است.',
        'short_title.string' => 'فیلد توضیح کوتاه باید متن باشد.',
        'short_title.max' => 'فیلد توضیح کوتاه نباید بیشتر از ۱۰۲۴ کاراکتر باشد.',
    ])]
    public string $short_title;

    #[Validate('required|string', message: [
        'body.required' => 'فیلد متن اصلی الزامی است.',
        'body.string' => 'فیلد متن اصلی باید متن باشد.',
    ])]
    public string $body;

    #[Validate('nullable|string', message: [
        'meta_description.string' => 'فیلد توضیحات متا باید متن باشد.',
    ])]
    public $meta_description;

    #[Validate('required|string', message: [
        'help.required' => 'فیلد راهنما الزامی است.',
        'help.string' => 'فیلد راهنما باید متن باشد.',
    ])]
    public string $help;

    #[Validate('required|string', message: [
        'roles.required' => 'فیلد قوانین الزامی است.',
        'roles.string' => 'فیلد قوانین باید متن باشد.',
    ])]
    public string $roles;

    #[Validate('required|string', message: [
        'faq.required' => 'فیلد سوالات متداول الزامی است.',
        'faq.string' => 'فیلد سوالات متداول باید متن باشد.',
    ])]
    public string $faq;
    public $tags;
    #[Validate('required|file|mimes:mp4,avi,mkv,jpg,png,jpeg,bmp', message: [
        'main_file.file' => 'فیلد فایل شاخص باید از نوع فایل باشد.',
        'main_file.mimes' => 'فرمت فایل شاخص باید از نوع mp4,avi,mkv,jpg,png,jpeg,bmp باشد.',
    ])]
    public $main_file;
    protected $rules = [
        'survey_formats' => 'required|array|min:1',
        'survey_formats.*' => 'string|max:255',
        'tags' => 'nullable|array|min:1',
        'tags.*' => 'string|max:255',
    ];
    protected $messages = [
        'tags.array' => 'فیلد تگ‌ها باید یک آرایه باشد.',
        'tags.min' => 'حداقل یک تگ باید انتخاب شود.',
        'tags.*.string' => 'هر تگ باید یک رشته باشد.',
        'tags.*.max' => 'هر تگ نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
    ];

}
