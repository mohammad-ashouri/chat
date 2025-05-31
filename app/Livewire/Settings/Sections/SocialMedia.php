<?php

namespace App\Livewire\Settings\Sections;

use App\Models\SiteSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class SocialMedia extends Component
{
    /**
     * Make form for edit social media in site settings
     * @var \App\Livewire\Forms\Settings\SocialMedia
     */
    public \App\Livewire\Forms\Settings\SocialMedia $form;

    public $socialMedia;

    /**
     * Mount about us component
     * @return void
     */
    #[On('refresh')]
    public function mount(): void
    {
        $this->socialMedia = SiteSetting::where('title', 'social-media')->first();
        $values = json_decode($this->socialMedia->value, true);
        $this->form->eitaa = $values['eitaa'] ?? '';
        $this->form->telegram = $values['telegram'] ?? '';
        $this->form->x = $values['x'] ?? '';
        $this->form->instagram = $values['instagram'] ?? '';
        $this->form->youtube = $values['youtube'] ?? '';
        $this->form->tiktok = $values['tiktok'] ?? '';
        $this->form->rubika = $values['rubika'] ?? '';
        $this->form->bale = $values['bale'] ?? '';
        $this->form->soroush = $values['soroush'] ?? '';
    }

    /**
     * َUpdate about us field
     * @return void
     */
    public function update(): void
    {
        $this->form->validate();
        $values = json_encode([
            'eitaa' => trim($this->form->eitaa),
            'telegram' => trim($this->form->telegram),
            'x' => trim($this->form->x),
            'instagram' => trim($this->form->instagram),
            'youtube' => trim($this->form->youtube),
            'tiktok' => trim($this->form->tiktok),
            'rubika' => trim($this->form->rubika),
            'bale' => trim($this->form->bale),
            'soroush' => trim($this->form->soroush),
        ]);
        $this->socialMedia->value = $values;
        $this->socialMedia->editor = auth()->user()->id;
        $this->socialMedia->save();
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('refresh');
    }
}
