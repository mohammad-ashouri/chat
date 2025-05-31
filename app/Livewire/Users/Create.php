<?php

namespace App\Livewire\Users;

use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('مدیریت کاربران | کاربر جدید')]
class Create extends Component
{
    public array $roles = [];

    #[Validate('required|string|min:3|max:50', message: [
        'name.required' => 'لطفا مشخصات را وارد کنید',
        'name.string' => 'فیلد مشخصات از نوع عددی است',
        'name.min' => 'حداقل تعداد کاراکتر مشخصات: 3',
        'name.max' => 'حداکثر تعداد کاراکتر مشخصات: 50',
    ])]
    public $name;

    #[Validate('required|email', message: [
        'email.required' => 'لطفا ایمیل را وارد کنید',
        'email.email' => 'فیلد ایمیل از نوع ایمیل است',
    ])]
    public $email;

    #[Validate('required|string|min:8|max:24', message: [
        'password.required' => 'لطفا رمز عبور را وارد کنید',
        'password.string' => 'فیلد رمز عبور از نوع متن است',
        'password.min' => 'حداقل تعداد کاراکتر رمز عبور: 3',
        'password.max' => 'حداکثر تعداد کاراکتر رمز عبور: 50',
    ])]
    public $password;

    #[Validate('required|integer|exists:roles,id', message: [
        'role.required' => 'لطفا یک نقش انتخاب کنید',
        'role.integer' => 'فیلد نقش از نوع عددی است',
        'role.exists' => 'نقش نامعتبر',
    ])]
    public $role;

    /**
     * Store user
     * @return RedirectResponse|void
     */
    public function store()
    {
        if (!auth()->user()->can('مدیریت کاربران | کاربر جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->validate();

        $userCheck = \App\Models\User::where('email', $this->email)->first();
        if (!empty($userCheck)) {
            $this->addError('email', 'ایمیل تکراری وارد شده است');
            return;
        }

        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'adder' => auth()->id(),
        ]);

        $role = Role::findById($this->role);
        $user->assignRole($role);

        session()->flash('success', 'کاربر جدید اضافه شد');
        return redirect()->to(route('users.index'));
    }

    /**
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        if (!auth()->user()->can('مدیریت کاربران | کاربر جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->roles = Role::orderBy('name')->get()->pluck('name', 'id')->toArray();
    }
}
