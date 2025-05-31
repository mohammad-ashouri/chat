<?php

namespace App\Livewire\Post;

use App\Models\Catalogs\IranicMediaType;
use App\Models\Catalogs\PostType;
use App\Models\File;
use App\Models\Post;
use Carbon\Carbon;
use getID3;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('ایرانیک مدیا - پست جدید')]
class Create extends Component
{
    use WithFilePond;

    /**
     * Make form for create post
     * @var \App\Livewire\Forms\Post\Create
     */
    public \App\Livewire\Forms\Post\Create $form;

    /**
     * Condition variable for upload file box
     * @var int
     */
    public int $fileShow = 0;

    protected $listeners = ['tagsUpdated'];

    /**
     * Update tags from tagify
     * @param $tags
     * @return void
     */
    public function tagsUpdated($tags): void
    {
        $formTags = [];
        foreach ($tags as $tag) {
            if (isset($tag['value'])) {
                $formTags[] = $tag['value'];
            }
        }
        $this->form->tags = $formTags;
    }

    /**
     * Store post
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مدیریت ایرانیک مدیا | پست جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->form->validate();
        if ($this->form->post_type == 2) {
            $this->form->validate([
                'file' => 'required|file|mimes:mp3,wav|max:102400', // 100MB
            ], [
                'file.required' => 'لطفاً یک فایل صوتی انتخاب کنید.',
                'file.file' => 'فایل انتخاب شده معتبر نیست.',
                'file.mimes' => 'فقط فایل‌های با پسوند MP3 و WAV مجاز هستند.',
                'file.max' => 'حجم فایل نباید بیشتر از ۱۰۰ مگابایت باشد.',
            ]);
        } elseif ($this->form->post_type == 1) {
            $this->form->validate([
                'file' => 'required|file|mimes:mp4,avi,mkv|max:512000', // 500MB
            ], [
                'file.required' => 'لطفاً یک فایل ویدیویی انتخاب کنید.',
                'file.file' => 'فایل انتخاب شده معتبر نیست.',
                'file.mimes' => 'فقط فایل‌های با پسوند MP4, AVI, و MKV مجاز هستند.',
                'file.max' => 'حجم فایل نباید بیشتر از ۵۰۰ مگابایت باشد.',
            ]);
        } elseif ($this->form->post_type == 3) {
            $this->form->file = null;
        }

        $this->form->validate([
            'main_picture' => 'required|image|mimes:jpg,png,jpeg,bmp|max:5120', // 5MB
        ], [
            'main_picture.required' => 'لطفاً یک عکس انتخاب کنید.',
            'main_picture.file' => 'فایل انتخاب شده معتبر نیست.',
            'main_picture.mimes' => 'فقط فایل‌های با پسوند MP4, AVI, و MKV مجاز هستند.',
            'main_picture.max' => 'حجم فایل نباید بیشتر از 5 مگابایت باشد.',
        ]);

        $post = new Post();
        $post->title = $this->form->title;
        $post->short_title = $this->form->short_title;
        $post->body = $this->form->body;
        $post->post_type = $this->form->post_type;
        $post->iranic_media_type = $this->form->iranic_media_type;
        $post->meta_description = $this->form->meta_description;

        switch ($this->form->hottest) {
            case 0:
                $post->hottest = false;
                $post->main_hottest = false;
                break;
            case 1:
                $post->hottest = true;
                $post->main_hottest = false;
                break;
            case 2:
                $post->hottest = true;
                $post->main_hottest = true;
                break;
        }
        $post->special = $this->form->special;

        $post->adder = auth()->user()->id;
        $post->save();

        $extension = $this->form->main_picture->getClientOriginalExtension();
        $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $extension;
        $main_image = $this->form->main_picture->storeAs(
            "posts/{$post->id}",
            $fileName,
            'public'
        );
        $main_image_url = Storage::url($main_image);

        File::create([
            'type' => 'main_picture',
            'model' => 'App\Models\Post',
            'model_id' => $post->id,
            'src' => $main_image_url,
            'adder' => auth()->user()->id
        ]);

        if (!empty($this->form->tags)) {
            $post->syncTags($this->form->tags);
        }

        if (!empty($this->form->file)) {
            $extension = $this->form->file->getClientOriginalExtension();
            $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $extension;
            $file = $this->form->file->storeAs("posts/{$post->id}", $fileName, 'public');
            $file_url = Storage::url($file);

            //Audio
            if ($this->form->post_type == 2) {
                $getID3 = new getID3();
                $filePath = $this->form->file->getPathname();

                $fileInfo = $getID3->analyze($filePath);

                if (isset($fileInfo['playtime_seconds'])) {
                    $duration = round($fileInfo['playtime_seconds'], 2);
                    \Log::info('Duration from playtime_seconds: ' . $duration);
                } elseif (isset($fileInfo['audio']['playtime_seconds'])) {
                    $duration = round($fileInfo['audio']['playtime_seconds'], 2);
                    \Log::info('Duration from audio.playtime_seconds: ' . $duration);
                } else {
                    \Log::info('No duration found in file info');
                    $duration = 0;
                }
                File::create([
                    'type' => 'main_audio',
                    'model' => 'App\Models\Post',
                    'model_id' => $post->id,
                    'duration' => $duration,
                    'src' => $file_url,
                    'adder' => auth()->user()->id
                ]);
            } //Video
            elseif ($this->form->post_type == 1) {
                File::create([
                    'type' => 'main_video',
                    'model' => 'App\Models\Post',
                    'model_id' => $post->id,
                    'src' => $file_url,
                    'adder' => auth()->user()->id
                ]);
            }
        }


        session()->flash('success', 'پست با موفقیت ایجاد شد.');
        $this->redirect(route('posts.index'));
    }

    /**
     * Check post type for show upload file box
     * @return void
     */
    public function checkPostType(): void
    {
        if ($this->form->post_type == 2) {
            $this->fileShow = 1;
        } elseif ($this->form->post_type == 1) {
            $this->fileShow = 2;
        } else {
            $this->fileShow = 0;
        }

        // Dispatch event to reinitialize TinyMCE
        $this->dispatch('tinymce-reinit');
    }

    /**
     * Render the component
     * @return Factory|Application|View|\Illuminate\View\View
     */
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت ایرانیک مدیا | پست جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('livewire.post.create', [
            'post_types' => PostType::orderBy('name')->get()->pluck('name', 'id')->toArray(),
            'iranic_media_types' => IranicMediaType::whereIsActive(1)->orderBy('name')->get()->pluck('name', 'id')->toArray(),
        ]);
    }
}
