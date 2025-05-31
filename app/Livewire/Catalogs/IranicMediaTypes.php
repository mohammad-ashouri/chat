<?php

namespace App\Livewire\Catalogs;

use App\Models\Catalogs\IranicMediaType;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('مقادیر اولیه - دسته بندی های ایرانیک مدیا')]
class IranicMediaTypes extends Component
{
    use WithPagination;

    #[Validate('string')]
    #[Validate('required', message: 'لطفا نام دسته بندی را وارد کنید')]
    #[Validate('unique:iranic_media_types,name', message: 'نام دسته بندی تکراری می باشد')]
    public string $name;

    #[Validate('int')]
    #[Validate('required')]
    #[Validate('exists:iranic_media_types,id')]
    public int $id;

    #[Validate('boolean')]
    public bool $is_active;

    protected $listeners = [
        'resetCatalogName'
    ];

    /**
     * Reset catalog name before new modal opened
     * @return void
     */
    public function resetCatalogName(): void
    {
        $this->reset('name');
    }

    /**
     * Get data before opened edit modal
     * @param $id
     * @return void
     */
    #[On('get_data')]
    public function get_data($id): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت دسته بندی های ایرانیک مدیا')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalog = IranicMediaType::findOrFail($id);
        $this->id = $catalog->id;
        $this->name = $catalog->name;
        $this->is_active = $catalog->is_active;
    }

    /**
     * Store catalog
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت دسته بندی های ایرانیک مدیا')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        if (auth()->user() === null) {
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $this->validate();
        IranicMediaType::create([
            'name' => $this->name,
            'adder' => auth()->id(),
        ]);

        $this->reset('name');
        $this->dispatch('close-modal', 'create');
        $this->dispatch('show-notification', 'success-notification');
    }

    public function update()
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت دسته بندی های ایرانیک مدیا')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalog = IranicMediaType::findOrFail($this->id);

        $rules = [
            'name' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];

        if ($this->name !== $catalog->name) {
            $rules['name'][] = 'unique:iranic_media_types,name';
        }

        $this->validate($rules);

        $catalog->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
            'editor' => auth()->user()->id,
        ]);

        $this->dispatch('close-modal', 'edit');
        $this->dispatch('show-notification', 'success-notification');
    }

    /**
     * Render the component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت دسته بندی های ایرانیک مدیا')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalogs = IranicMediaType::orderBy('updated_at')->paginate(10);

        return view('livewire.catalogs.iranic-media-types', [
            'catalogs' => $catalogs ?? collect()
        ]);
    }
}
