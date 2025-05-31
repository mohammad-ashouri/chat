<?php

namespace App\Livewire\Settings\Sections;

use App\Models\SiteSetting;
use Livewire\Component;

class AboutUs extends Component
{
    /**
     * Make form for edit about us in site settings
     * @var \App\Livewire\Forms\Settings\AboutUs
     */
    public \App\Livewire\Forms\Settings\AboutUs $form;

    public $aboutUs;

    /**
     * Mount about us component
     * @return void
     */
    public function mount(): void
    {
        $this->aboutUs = SiteSetting::where('title', 'about-us')->first();
        $values = json_decode($this->aboutUs->value, true);
        $this->form->raahbari = $values['raahbari'] ?? '';
        $this->form->resalat = $values['resalat'] ?? '';
        $this->form->ghavanin = $values['ghavanin'] ?? '';
        $this->form->masooliat = $values['masooliat'] ?? '';
    }

    /**
     * َUpdate about us field
     * @return void
     */
    public function update(): void
    {
        $this->form->validate();
        $values = json_encode([
            'raahbari' => trim($this->form->raahbari),
            'resalat' => trim($this->form->resalat),
            'ghavanin' => trim($this->form->ghavanin),
            'masooliat' => trim($this->form->masooliat),
        ]);
        $this->aboutUs->value = $values;
        $this->aboutUs->editor = auth()->user()->id;
        $this->aboutUs->save();
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('refresh');
    }
}
