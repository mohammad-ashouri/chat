<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;
use Spatie\Permission\Models\Role;

#[Title('مدیریت کاربران')]
class Index extends Component
{
    use WithPagination;

    #[Validate('nullable|string|min:3|max:50', message: [
        'name.string' => 'فیلد مشخصات از نوع عددی است',
        'name.min' => 'حداقل تعداد کاراکتر مشخصات: 3',
        'name.max' => 'حداکثر تعداد کاراکتر مشخصات: 50',
    ])]
    public $name;

    #[Validate('nullable|email', message: [
        'email.email' => 'فیلد ایمیل از نوع ایمیل است',
    ])]
    public $email;

    public int $status;

    #[Validate('nullable|integer|exists:roles,id', message: [
        'role.integer' => 'نقش از نوع عددی است',
        'role.exists' => 'نقش نامعتبر',
    ])]
    public $role;

    #[Validate('nullable|integer|exists:users,id', message: [
        'adder.integer' => 'ثبت کننده از نوع عددی است',
        'adder.exists' => 'ثبت کننده نامعتبر',
    ])]
    public $adder;

    #[Validate('nullable|integer|exists:users,id', message: [
        'editor.integer' => 'ویرایش کننده از نوع عددی است',
        'editor.exists' => 'ویرایش کننده نامعتبر',
    ])]
    public $editor;

    /**
     * Roles list
     * @var array
     */
    public array $roles = [];

    /**
     * Adders list
     * @var array
     */
    public array $adders = [];

    /**
     * Editors list
     * @var array
     */
    public array $editors = [];

    /**
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        $this->setInitialValues();
    }

    /**
     * Set initial dynamic values
     * @return void
     */
    public function setInitialValues(): void
    {
        if (!auth()->user()->can('مدیریت کاربران | صفحه اصلی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $adders = User::pluck('adder');
        foreach ($adders as $adder) {
            $userModel = User::find($adder);
            $this->adders[$adder] = $userModel->name;
        }

        $editors = User::pluck('editor');
        foreach ($editors as $editor) {
            $userModel = User::find($editor);
            if ($userModel) $this->editors[$editor] = $userModel->name;
        }

        $this->roles = Role::get()->pluck('name', 'id')->toArray();

        $collator = new \Collator('fa_IR');

        $collator->asort($this->adders);
        $collator->asort($this->editors);
        $collator->asort($this->roles);
    }

    /**
     * Reset search form
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->resetErrorBag();
    }

    /**
     * Search and re-render component after validate search form
     * @return void
     */
    public function search(): void
    {
        $this->validate();
        $this->resetPage();
    }

    /**
     * Render the component
     * @return Factory|Application|View|\Illuminate\View\View
     */
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت کاربران | صفحه اصلی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $users = User::query();

        $users->when(!empty($this->model_id), function ($query) {
            $query->where('id', $this->model_id);
        });

        $users->when(!empty($this->name), function ($query) {
            $query->where('name', 'like', '%' . $this->name . '%');
        });

        $users->when(!empty($this->email), function ($query) {
            $query->where('email', 'like', '%' . $this->email . '%');
        });

        $users->when(!empty($this->role), function ($query) {
            $role = Role::find($this->role);
            $query->role($role->name);
        });

        $users->when(!empty($this->created_at), function ($query) {
            $query->whereDate('created_at', Jalalian::fromFormat('Y/m/d', $this->created_at)->toCarbon());
        });

        $users->when(!empty($this->updated_at), function ($query) {
            $query->whereDate('updated_at', Jalalian::fromFormat('Y/m/d', $this->updated_at)->toCarbon());
        });

        $users->when(!empty($this->status), function ($query) {
            $query->where('status', '=', (string)$this->status);
        });

        $users->when(!empty($this->adder), function ($query) {
            $query->where('adder', '=', $this->adder);
        });

        $users->when(!empty($this->editor), function ($query) {
            $query->where('editor', '=', $this->editor);
        });

        return view('livewire.users.index', [
            'users' => $users->paginate(50),
        ]);
    }
}
