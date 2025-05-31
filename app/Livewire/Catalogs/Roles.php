<?php

namespace App\Livewire\Catalogs;

use App\Attributes\HasPermission;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('مقادیر اولیه - نقش های کاربری')]
class Roles extends Component
{
    use WithPagination;

    #[Validate('string')]
    #[Validate('required', message: 'لطفا نام نقش را وارد کنید')]
    #[Validate('unique:roles,name', message: 'نام نقش تکراری می باشد')]
    public string $name = '';

    #[Validate('int')]
    #[Validate('required')]
    #[Validate('exists:roles,id')]
    public int $id;

    /**
     * Get permissions with render the component
     * @var array
     */
    public array $permissions = [];

    /**
     * Selected permissions for add or edit role
     * @var array
     */
    public array $selected_permissions = [];

    /**
     * Listeners
     * @var string[]
     */
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
        if (!auth()->user()->can('مقادیر اولیه | مدیریت نقش های کاربری')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalog = Role::findOrFail($id);
        $this->id = $catalog->id;
        $this->name = $catalog->name;
        $this->selected_permissions = $catalog->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Store catalog
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت نقش های کاربری')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $rules = [
            'name' => ['required', 'string', 'unique:roles,name'],
            'selected_permissions' => ['required', 'array'],
            'selected_permissions.*' => ['exists:permissions,name'], // اصلاح شده
        ];

        $messages = [
            'name.required' => 'وارد کردن نام نقش الزامی است.',
            'name.unique' => 'این نقش قبلاً ثبت شده است.',
            'selected_permissions.required' => 'حداقل یک دسترسی باید انتخاب شود.',
            'selected_permissions.*.exists' => 'دسترسی انتخاب شده معتبر نیست.'
        ];

        $this->validate($rules, $messages);
        $this->name = trim($this->name);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $this->name,
                'adder' => auth()->id(),
            ]);

            $permissionIds = Permission::whereIn('name', $this->selected_permissions)
                ->pluck('id')
                ->toArray();

            $role->permissions()->sync($permissionIds);

            DB::commit();

            $this->reset(['name', 'selected_permissions']);
            $this->dispatch('close-modal', 'create');
            $this->dispatch('show-notification', 'success-notification');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-notification',
                type: 'error',
                message: 'خطا در ایجاد نقش: ' . $e->getMessage()
            );
        }
    }

    /**
     * Update catalog
     * @return void
     */
    public function update(): void
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت نقش های کاربری')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalog = Role::findOrFail($this->id);

        $rules = [
            'name' => ['required', 'string'],
        ];

        $roleCheck = Role::where('name', $this->name)->where('id', '!=', $this->id)->first();
        if (!empty($roleCheck)) {
            $this->addError('email', 'نام نقش تکراری وارد شده است');
            return;
        }

        $this->validate($rules);

        try {
            DB::beginTransaction();

            $catalog->update([
                'name' => $this->name,
                'editor' => auth()->user()->id,
            ]);

            $permissionIds = Permission::whereIn('name', $this->selected_permissions)
                ->pluck('id')
                ->toArray();

            $catalog->permissions()->sync($permissionIds);

            DB::commit();

            $this->reset(['name', 'selected_permissions']);
            $this->dispatch('close-modal', 'update');
            $this->dispatch('show-notification', 'success-notification');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-notification',
                type: 'error',
                message: 'خطا در ایجاد نقش: ' . $e->getMessage()
            );
        }


        $this->dispatch('close-modal', 'edit');
        $this->dispatch('show-notification', 'success-notification');
    }

    /**
     * Render the component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مقادیر اولیه | مدیریت نقش های کاربری')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $catalogs = Role::query();
        $this->permissions = Permission::orderBy('name')->pluck('name', 'id')->toArray();
        return view('livewire.catalogs.roles', [
            'catalogs' => $catalogs->orderBy('name')->paginate(20)
        ]);
    }
}
