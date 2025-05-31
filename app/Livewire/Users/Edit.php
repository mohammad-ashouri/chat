<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('مدیریت کاربران | ویرایش کاربر')]
class Edit extends Component
{
    /**
     * Set user from mount
     * @var User
     */
    #[Locked]
    public User $user;

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

    #[Validate('nullable|string|min:8|max:24', message: [
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

    #[Validate('required|bool', message: [
        'status.required' => 'لطفا یک وضعیت انتخاب کنید',
        'status.bool' => 'فیلد وضعیت غیر معتبر',
    ])]
    public $status = null;

    /**
     * Edit user
     * @return RedirectResponse|void
     */
    public function edit()
    {
        if (!auth()->user()->can('مدیریت کاربران | ویرایش کاربر')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->validate();

        $userCheck = User::where('email', $this->email)->where('id', '!=', $this->user->id)->first();
        if (!empty($userCheck)) {
            $this->addError('email', 'ایمیل تکراری وارد شده است');
            return;
        }

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'editor' => auth()->id(),
        ]);

        if ($this->password) {
            $this->user->update([
                'password' => bcrypt($this->password),
            ]);
        }

        $role = Role::findById($this->role);
        $this->user->syncRoles($role);

        session()->flash('success', 'کاربر ویرایش شد');
        return redirect()->to(route('users.index'));
    }

    /**
     * Mount the component
     * @param $id
     * @return void
     */
    public function mount($id): void
    {
        if (!auth()->user()->can('مدیریت کاربران | ویرایش کاربر')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->user = User::findOrFail($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->status = (int)$this->user->status;

        $role = Role::where('name', $this->user->getRoleNames())->first();

        $this->role = $role->id;
        $this->roles = Role::orderBy('name')->get()->pluck('name', 'id')->toArray();
    }
}
