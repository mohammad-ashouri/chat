<?php

namespace App\Livewire\Settings\Sections;

use App\Models\File;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class Banners extends Component
{
    use WithFilePond;

    /**
     * Make form for edit top header banners in site settings
     * @var \App\Livewire\Forms\Settings\Banners
     */
    public \App\Livewire\Forms\Settings\Banners $form;

    /**
     * Load banner settings from site settings table
     * @var array
     */
    public array $bannersSettings;

    public function mount(): void
    {
        $this->bannersSettings = json_decode(SiteSetting::where('title', 'banners')->first()->value, true);
    }

    /**
     * َUpdate top header banners
     * @return void
     */
    public function update(): void
    {
        $this->form->validate();

        if (!empty($this->form->iranic_media)) {
            $iranic_media_image = $this->form->iranic_media->storeAs("banners/iranic_media/", uniqid() . Carbon::now()->format('s-i-H-d-m-Y'), 'public');
            $iranic_media_image_url = Storage::url($iranic_media_image);

            $iranic_media_file = File::create([
                'type' => 'banner',
                'src' => $iranic_media_image_url,
                'adder' => auth()->user()->id
            ]);

            $this->bannersSettings['iranic_media'] = $iranic_media_file;
        }

        if (!empty($this->form->hottest)) {
            $hottest_image = $this->form->hottest->storeAs("banners/hottest/", uniqid() . Carbon::now()->format('s-i-H-d-m-Y'), 'public');
            $hottest_image_url = Storage::url($hottest_image);

            $hottest_file = File::create([
                'type' => 'banner',
                'src' => $hottest_image_url,
                'adder' => auth()->user()->id
            ]);

            $this->bannersSettings['hottest'] = $hottest_file;
        }

        if (!empty($this->form->survey)) {
            $survey_image = $this->form->survey->storeAs("banners/survey/", uniqid() . Carbon::now()->format('s-i-H-d-m-Y'), 'public');
            $survey_image_url = Storage::url($survey_image);

            $survey_file = File::create([
                'type' => 'banner',
                'src' => $survey_image_url,
                'adder' => auth()->user()->id
            ]);

            $this->bannersSettings['survey'] = $survey_file;
        }

        $bannersSettings = SiteSetting::where('title', 'banners')->first();
        $bannersSettings->value = json_encode($this->bannersSettings);
        $bannersSettings->editor = auth()->user()->id;
        $bannersSettings->save();
        $this->dispatch('show-notification', 'success-notification');
        $this->dispatch('refresh');
    }
}
