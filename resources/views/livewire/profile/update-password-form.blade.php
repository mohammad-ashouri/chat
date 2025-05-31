<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'وارد کردن رمز عبور فعلی الزامی است.',
                'current_password.string' => 'رمز عبور فعلی باید به صورت متن باشد.',
                'current_password.current_password' => 'رمز عبور فعلی نادرست است.',

                'password.required' => 'وارد کردن رمز عبور جدید الزامی است.',
                'password.string' => 'رمز عبور جدید باید به صورت متن باشد.',
                'password.confirmed' => 'تکرار رمز عبور جدید مطابقت ندارد.',
                'password.min' => 'رمز عبور جدید باید حداقل 8 کاراکتر باشد.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            تغییر کلمه عبور
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            اطمینان حاصل کنید که حساب شما از یک رمز عبور طولانی و تصادفی برای ایمن ماندن استفاده می کند
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="'رمز عبور فعلی'"/>
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password"
                          type="password" class="mt-1 block w-full" autocomplete="current-password"/>
            <x-input-error :messages="$errors->get('current_password')" class="mt-2"/>
        </div>

        <div>
            <x-input-label for="update_password_password" :value="'رمز عبور جدید'"/>
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password"
                          class="mt-1 block w-full" autocomplete="new-password"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="'تکرار رمز عبور جدید'"/>
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation"
                          name="password_confirmation" type="password" class="mt-1 block w-full"
                          autocomplete="new-password"/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="flex items-center justify-end gap-4">
            <x-action-message class="me-3" on="password-updated">
                ذخیره شد!
            </x-action-message>
            <x-primary-button>ذخیره</x-primary-button>
        </div>
    </form>
</section>
