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
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('پویش - پویش جدید')]
class Create extends Component
{
    use WithFilePond;

    /**
     * Make form for create post
     * @var \App\Livewire\Forms\Survey\Create
     */
    public \App\Livewire\Forms\Survey\Create $form;

    protected $listeners = ['tagsUpdated'];

    /**
     * Update tags from tagify
     * @param $tags
     * @return void
     */
    public function tagsUpdated($tags): void
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
     * Store post
     * @return void
     */
    public function store(): void
    {
        if (!auth()->user()->can('مدیریت پویش ها | پویش جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $this->form->validate();

        $post = new Survey();
        $post->title = $this->form->title;
        $post->short_title = $this->form->short_title;
        $post->body = $this->form->body;
        $post->help = $this->form->help;
        $post->roles = $this->form->roles;
        $post->faq = $this->form->faq;
        $post->meta_description = $this->form->meta_description;
        $post->adder = auth()->user()->id;
        $post->save();

        foreach ($this->form->survey_formats as $survey_format) {
            SurveyFormat::insert(['survey_id' => $post->id, 'name' => $survey_format]);
        }

        $originalExtension = $this->form->main_file->getClientOriginalExtension();
        $fileName = uniqid() . Carbon::now()->format('s-i-H-d-m-Y') . '.' . $originalExtension;
        $main_file = $this->form->main_file->storeAs("survey/$post->id", $fileName, 'public');
        $main_file_url = Storage::url($main_file);

        File::create([
            'type' => 'main_file',
            'model' => 'App\Models\Survey',
            'model_id' => $post->id,
            'src' => $main_file_url,
            'adder' => auth()->user()->id
        ]);

        if (!empty($this->form->tags)) {
            $post->syncTags($this->form->tags);
        }

        session()->flash('success', 'پویش با موفقیت ایجاد شد.');
        $this->redirect(route('survey.index'));
    }

    /**
     * Render edit survey component
     * @return Factory|Application|View|\Illuminate\View\View
     */
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت پویش ها | پویش جدید')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('livewire.survey.create');
    }
}
