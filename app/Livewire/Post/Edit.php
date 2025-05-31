<?php

namespace App\Livewire\Post;

use App\Models\Catalogs\IranicMediaType;
use App\Models\Catalogs\PostType;
use App\Models\File;
use App\Models\Post;
use Carbon\Carbon;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use getID3;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('ایرانیک مدیا - ویرایش پست')]
class Edit extends Component
{
    use WithFilePond;

    #[Locked]
    public int $post_id;

    public Post $post;

    /**
     * Make form for edit post
     * @var \App\Livewire\Forms\Post\Edit
     */
    public \App\Livewire\Forms\Post\Edit $form;

    /**
     * Condition variable for upload file box
     * @var int
     */
    public int $fileShow = 0;

    protected $listeners = ['tagsUpdated' => 'tagsUpdatedEdit'];

    public function tagsUpdatedEdit($tags): void
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
        if (!auth()->user()->can('مدیریت ایرانیک مدیا | ویرایش پست')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->form->validate();
        if ($this->form->post_type == 2) {
            $this->form->validate([
                'file' => 'nullable|file|mimes:mp3,wav|max:102400', // 100MB
            ], [
                'file.file' => 'فایل انتخاب شده معتبر نیست.',
                'file.mimes' => 'فقط فایل‌های با پسوند MP3 و WAV مجاز هستند.',
                'file.max' => 'حجم فایل نباید بیشتر از ۱۰۰ مگابایت باشد.',
            ]);
        } elseif ($this->form->post_type == 1) {
            $this->form->validate([
                'file' => 'nullable|file|mimes:mp4,avi,mkv|max:512000', // 500MB
            ], [
                'file.file' => 'فایل انتخاب شده معتبر نیست.',
                'file.mimes' => 'فقط فایل‌های با پسوند MP4, AVI, و MKV مجاز هستند.',
                'file.max' => 'حجم فایل نباید بیشتر از ۵۰۰ مگابایت باشد.',
            ]);
        } elseif ($this->form->post_type == 3) {
            $this->form->file = null;
        }

        $post = $this->post;
        $post->title = $this->form->title;
        $post->short_title = $this->form->short_title;
        $post->body = $this->form->body;
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

        $post->editor = auth()->user()->id;
        $post->save();

        if (!empty($this->form->main_picture)) {
            File::where('model_id', $post->id)->where('model', 'App\Models\Post')->where('type', 'main_picture')->delete();
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
        }
        if (!empty($this->form->tags)) {
            $post->syncTags($this->form->tags);
        }

        if (!empty($this->form->file)) {
            $extension = $this->form->file->getClientOriginalExtension();
            $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $extension;
            $file = $this->form->file->storeAs("posts/{$post->id}", $fileName, 'public');
            $file_url = Storage::url($file);

            $fullVideoPath = Storage::path($file);
            $thumbnailName = 'thumb_' . uniqid() . '.jpg';
            $thumbnailPath = 'public/thumbnails/' . $thumbnailName;
            $fullThumbnailPath = storage_path('app/' . $thumbnailPath);

            if (!file_exists(dirname($fullThumbnailPath))) {
                mkdir(dirname($fullThumbnailPath), 0755, true);
            }

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => '/usr/bin/ffmpeg', // مسیر کامل ffmpeg
                'ffprobe.binaries' => '/usr/bin/ffprobe', // مسیر کامل ffprobe
                'timeout' => 3600,
                'ffmpeg.threads' => 12,
            ]);

            $video = $ffmpeg->open($fullVideoPath);

            dd($video);
            $video->frame(TimeCode::fromSeconds(1))
                ->save($fullThumbnailPath);


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

                File::where('model_id', $post->id)->where('model', 'App\Models\Post')->where('type', 'main_audio')->delete();
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
                File::where('model_id', $post->id)->where('model', 'App\Models\Post')->where('type', 'main_video')->delete();
                File::create([
                    'type' => 'main_video',
                    'model' => 'App\Models\Post',
                    'model_id' => $post->id,
                    'src' => $file_url,
                    'adder' => auth()->user()->id
                ]);
            }
        }
        session()->flash('success', 'پست با موفقیت ویرایش شد.');
        $this->redirect(route('posts.index'));
    }

    /**
     * Check post type for show upload file box
     * @return void
     */
    public function checkPostType(): void
    {
        if ($this->form->post_type == 2) {
            $this->fileShow = 2;
        } else if ($this->form->post_type == 1) {
            $this->fileShow = 1;
        } else {
            $this->fileShow = 0;
        }
        $this->dispatch('filepond-reset-form.file');
    }

    /**
     * Mount edit post component
     * @param $id
     * @return void
     */
    public function mount($id): void
    {
        if (!auth()->user()->can('مدیریت ایرانیک مدیا | ویرایش پست')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        //Take id from url
        $this->post_id = $id;

        //Find the post
        $this->post = Post::with('mainImage')->whereId($this->post_id)->firstOrFail();

        //Fix edit form values
        $this->form->title = $this->post->title;
        $this->form->short_title = $this->post->short_title;
        $this->form->post_type = $this->post->post_type;
        $this->form->iranic_media_type = $this->post->iranic_media_type;
        $this->form->body = $this->post->body;
        $this->form->meta_description = $this->post->meta_description;
        $this->form->tags = $this->post->tags->pluck('name')->toArray();

        if ($this->post->hottest and $this->post->main_hottest) {
            $this->form->hottest = 2;
        } elseif ($this->post->hottest and !$this->post->main_hottest) {
            $this->form->hottest = 1;
        } else {
            $this->form->hottest = 0;
        }
        $this->form->special = $this->post->special;
    }

    /**
     * Render edit post component
     * @return Factory|Application|View|\Illuminate\View\View
     */
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.post.edit', [
            'post_types' => PostType::orderBy('name')->get()->pluck('name', 'id')->toArray(),
            'iranic_media_types' => IranicMediaType::whereIsActive(1)->orderBy('name')->get()->pluck('name', 'id')->toArray(),
        ]);
    }
}
