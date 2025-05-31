<?php

namespace App\Livewire\Settings\Sections;

use App\Models\SiteSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class ContactUs extends Component
{
    /**
     * Make form for edit contact us in site settings
     * @var \App\Livewire\Forms\Settings\ContactUs
     */
    public \App\Livewire\Forms\Settings\ContactUs $form;

    public $contactUs;

    /**
     * Mount about us component
     * @return void
     */
    #[On('refresh')]
    public function mount(): void
    {
        $this->contactUs = SiteSetting::where('title', 'contact-us')->first();
        $values = json_decode($this->contactUs->value, true);
        $this->form->hemayat = $values['hemayat'] ?? '';
        $this->form->hamkari = $values['hamkari'] ?? '';
    }

    /**
     * َUpdate about us field
     * @return void
     */
    public function update(): void
    {
        $this->form->validate();
        $values = json_encode([
            'hemayat' => trim($this->form->hemayat),
            'hamkari' => trim($this->form->hamkari),
        ]);
        $this->contactUs->value = $values;
        $this->contactUs->editor = auth()->user()->id;
        $this->contactUs->save();
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('refresh');
    }
}
