<?php

namespace App\Livewire\Catalogs;

use App\Models\File;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('مقادیر اولیه - اسلایدرها')]
class Sliders extends Component
{
    use WithPagination, WithFilePond;

    public ?int $id;

    #[Validate('string', message: 'فیلد عنوان از نوع متنی است')]
    #[Validate('required', message: 'لطفا عنوان را وارد کنید')]
    #[Validate('max:255', message: 'تعداد کاراکترهای عنوان بیشتر از 255 است.')]
    public string $title;

    #[Validate('string', message: 'فیلد سوتیتر از نوع متنی است')]
    #[Validate('required', message: 'لطفا سوتیتر را وارد کنید')]
    #[Validate('max:255', message: 'تعداد کاراکترهای سوتیتر بیشتر از 255 است.')]
    public string $subtitle;

    #[Validate('string', message: 'فیلد لینک از نوع متنی است')]
    #[Validate('required', message: 'لطفا لینک را وارد کنید')]
    #[Validate('max:500', message: 'تعداد کاراکترهای لینک بیشتر از 500 است.')]
    public string $link;

    #[Validate('string', message: 'فیلد آیکن از نوع متنی است')]
    #[Validate('in:بدون آیکن,صوت,فیلم', message: 'آیکن نامعتبر.')]
    public string $type = 'بدون آیکن';

    #[Validate('max:2048', message: 'حداکثر حجم فایل 2 مگابایت است.')]
    public $file;

    public bool $status = true;
    /**
     * File preview src variable
     * @var string
     */
    public string $file_preview;

    /**
     * Listeners
     * @var string[]
     */
    protected $listeners = ['resetForm'];

    /**
     * Reset form
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->dispatch('filepond-reset');
    }

    /**
     * Store slider
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت اسلایدرها')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->validate();

        $slider = new Slider();
        $slider->title = $this->title;
        $slider->subtitle = $this->subtitle;
        $slider->type = $this->type;
        $slider->link = $this->link;
        $slider->adder = auth()->user()->id;
        $slider->save();

        $extension = $this->file->getClientOriginalExtension();
        $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $extension;
        $file = $this->file->storeAs(
            "sliders/{$slider->id}",
            $fileName,
            'public'
        );
        $file_url = Storage::url($file);

        File::create([
            'type' => 'slider',
            'model' => Slider::class,
            'model_id' => $slider->id,
            'src' => $file_url,
            'adder' => auth()->user()->id
        ]);

        $this->reset();
        $this->dispatch('close-modal', 'create');
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('filepond-reset');
    }

    /**
     * Update slider
     * @return void
     */
    public function edit(): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت اسلایدرها')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->validate();

        $slider = Slider::find($this->id);
        $slider->title = $this->title;
        $slider->subtitle = $this->subtitle;
        $slider->type = $this->type;
        $slider->link = $this->link;
        $slider->status = $this->status;
        $slider->editor = auth()->user()->id;
        $slider->save();

        if ($this->file) {
            $extension = $this->file->getClientOriginalExtension();
            $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $extension;
            $file = $this->file->storeAs(
                "sliders/{$slider->id}",
                $fileName,
                'public'
            );
            $file_url = Storage::url($file);

            File::create([
                'type' => 'slider',
                'model' => Slider::class,
                'model_id' => $slider->id,
                'src' => $file_url,
                'adder' => auth()->user()->id
            ]);
        }

        $this->reset();
        $this->dispatch('close-modal', 'edit');
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('filepond-reset');
    }

    /**
     * Get data before opened edit modal
     * @param $id
     * @return void
     */
    #[On('get_data')]
    public function get_data($id): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت اسلایدرها')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $slider = Slider::with('file')->findOrFail($id);
        $this->id = $slider->id;
        $this->title = $slider->title;
        $this->subtitle = $slider->subtitle;
        $this->type = $slider->type;
        $this->link = $slider->link;
        $this->status = (int)$slider->status;
        $this->file_preview = $slider->file->src;
    }

    /**
     * Render the component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت اسلایدرها')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('livewire.catalogs.sliders', [
            'sliders' => Slider::orderByDesc('created_at')->paginate(50)
        ]);
    }
}
