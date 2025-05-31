<?php

namespace App\Livewire\Survey;

use App\Models\File;
use App\Models\Survey;
use App\Models\SurveyFormat;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('پویش - ویرایش پویش')]
class Edit extends Component
{
    use WithFilePond;

    #[Locked]
    public int $survey_id;

    public Survey $survey;

    /**
     * Make form for edit survey
     * @var \App\Livewire\Forms\Survey\Edit
     */
    public \App\Livewire\Forms\Survey\Edit $form;

    protected $listeners = ['tagsUpdated' => 'tagsUpdatedEdit'];

    public function tagsUpdatedEdit($tags): void
    {
        $formTags = [];
        foreach ($tags as $tag) {
            if (isset($tag['value'])) {
                $formTags[] = $tag['value'];
            }
        }
        $this->form->tags = $formTags;
    }

    /**
     * Store survey
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مدیریت پویش ها | ویرایش پویش')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->form->validate();

        $survey = $this->survey;
        $survey->title = $this->form->title;
        $survey->short_title = $this->form->short_title;
        $survey->body = $this->form->body;
        $survey->help = $this->form->help;
        $survey->roles = $this->form->roles;
        $survey->faq = $this->form->faq;
        $survey->meta_description = $this->form->meta_description;
        $survey->editor = auth()->user()->id;
        $survey->save();

        SurveyFormat::where('survey_id', $this->survey_id)->delete();
        foreach ($this->form->survey_formats as $survey_format) {
            SurveyFormat::insert(['survey_id' => $this->survey_id, 'name' => $survey_format]);
        }

        if (!empty($this->form->main_file)) {
            $originalExtension = $this->form->main_file->getClientOriginalExtension();
            $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $originalExtension;
            $main_file = $this->form->main_file->storeAs("survey/$survey->id", $fileName, 'public');
            $main_file_url = Storage::url($main_file);

            File::create([
                'type' => 'main_file',
                'model' => 'App\Models\Survey',
                'model_id' => $survey->id,
                'src' => $main_file_url,
                'adder' => auth()->user()->id
            ]);
        }

        if (!empty($this->form->tags)) {
            $survey->syncTags($this->form->tags);
        }

        session()->flash('success', 'پویش با موفقیت ویرایش شد.');
        $this->redirect(route('survey.index'));
    }

    /**
     * Mount edit survey component
     * @param $id
     * @return void
     */
    public function mount($id): void
    {
        //Take id from url
        $this->survey_id = $id;

        //Find the survey
        $this->survey = Survey::whereId($this->survey_id)->firstOrFail();

        //Fix edit form values
        $this->form->title = $this->survey->title;
        $this->form->short_title = $this->survey->short_title;
        $this->form->body = $this->survey->body;
        $this->form->help = $this->survey->help;
        $this->form->roles = $this->survey->roles;
        $this->form->faq = $this->survey->faq;
        $this->form->meta_description = $this->survey->meta_description;
        $this->form->tags = $this->survey->tags->pluck('name')->toArray();

        $this->form->survey_formats = SurveyFormat::where('survey_id', $id)->get()->pluck('name')->toArray();
    }

    /**
     * Render edit survey component
     * @return Factory|Application|View|\Illuminate\View\View
     */
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت پویش ها | ویرایش پویش')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('livewire.survey.edit');
    }
}
