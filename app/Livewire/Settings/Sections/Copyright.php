<?php

namespace App\Livewire\Settings\Sections;

use App\Models\SiteSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class Copyright extends Component
{
    /**
     * Make form for edit copyright in site settings
     * @var \App\Livewire\Forms\Settings\Copyright
     */
    public \App\Livewire\Forms\Settings\Copyright $form;

    public $copyright;

    /**
     * Mount about us component
     * @return void
     */
    #[On('refresh')]
    public function mount(): void
    {
        $this->copyright = SiteSetting::where('title', 'copyright')->first();
        $values = json_decode($this->copyright->value, true);
        $this->form->copyright = $values['copyright'] ?? '';
    }

    /**
     * َUpdate about us field
     * @return void
     */
    public function update(): void
    {
        $this->form->validate();
        $values = json_encode([
            'copyright' => trim($this->form->copyright),
        ]);
        $this->copyright->value = $values;
        $this->copyright->editor = auth()->user()->id;
        $this->copyright->save();
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('refresh');
    }
}
