<?php

namespace App\Livewire\Forms\Post;

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

    #[Validate('required|string|max:256', message: [
        'short_title.required' => 'فیلد توضیح کوتاه الزامی است.',
        'short_title.string' => 'فیلد توضیح کوتاه باید متن باشد.',
        'short_title.max' => 'فیلد توضیح کوتاه نباید بیشتر از 256 کاراکتر باشد.',
    ])]
    public string $short_title;

    #[Validate('required|string', message: [
        'body.required' => 'فیلد متن اصلی الزامی است.',
        'body.string' => 'فیلد متن اصلی باید متن باشد.',
    ])]
    public string $body;

    #[Validate('required|integer|exists:post_types,id', message: [
        'post_type.required' => 'فیلد قالب رسانه الزامی است.',
        'post_type.integer' => 'فیلد قالب رسانه باید یک عدد صحیح باشد.',
        'post_type.exists' => 'قالب رسانه انتخاب‌شده معتبر نیست.',
    ])]
    public ?int $post_type;

    #[Validate('required|integer|exists:iranic_media_types,id', message: [
        'iranic_media_type.required' => 'فیلد دسته‌بندی ایرانیک مدیا الزامی است.',
        'iranic_media_type.integer' => 'فیلد دسته‌بندی ایرانیک مدیا باید یک عدد صحیح باشد.',
        'iranic_media_type.exists' => 'دسته‌بندی انتخاب‌شده معتبر نیست.',
    ])]
    public ?int $iranic_media_type;

    #[Validate('nullable|string')]
    public $related_posts;

    #[Validate('nullable|string', message: [
        'meta_description.string' => 'فیلد توضیحات متا باید متن باشد.',
    ])]
    public $meta_description;

    protected $rules = [
        'tags' => 'nullable|array|min:1',
        'tags.*' => 'string|max:255',
    ];

    protected $messages = [
        'tags.array' => 'فیلد تگ‌ها باید یک آرایه باشد.',
        'tags.min' => 'حداقل یک تگ باید انتخاب شود.',
        'tags.*.string' => 'هر تگ باید یک رشته باشد.',
        'tags.*.max' => 'هر تگ نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
    ];
    public $tags;

    #[Validate('required|integer|in:0,1,2', message: [
        'hottest.required' => 'فیلد وضعیت داغ ترین الزامی است.',
        'hottest.integer' => 'فیلد داغ ترین باید عددی باشد.',
    ])]
    public int $hottest = 0;

    #[Validate('required|boolean', message: [
        'special.required' => 'فیلد انتخاب به عنوان ویژه الزامی است.',
    ])]
    public int $special = 0;

    public $file;

    #[Validate('required|image|mimes:jpg,png,jpeg,bmp|max:5120', message: [
        'main_picture.required' => 'لطفاً یک تصویر شاخص انتخاب کنید.',
        'main_picture.file' => 'فایل انتخاب شده معتبر نیست.',
        'main_picture.mimes' => 'فقط فایل‌های با پسوند MP4, AVI, و MKV مجاز هستند.',
        'main_picture.max' => 'حجم فایل نباید بیشتر از 5 مگابایت باشد.',
    ])]
    public $main_picture;
}
