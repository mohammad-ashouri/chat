<?php

namespace App\Livewire\Survey;

use App\Models\Country;
use App\Models\File;
use App\Models\State;
use App\Models\Survey;
use App\Models\SurveyFormat;
use App\Models\SurveyPost;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;

class Participants extends Component
{
    use WithPagination;

    /**
     * Survey variable
     * @var Survey
     */
    #[Locked]
    public Survey $survey;

    #[Validate('nullable|integer|exists:posts,id', message: [
        'model_id.integer' => ' ID از نوع عددی است',
        'model_id.exists' => 'ID نامعتبر',
    ])]
    public ?int $model_id;

    #[Validate('nullable|string|min:3|max:100', message: [
        'sender.string' => 'مشخصات از نوع متن است',
        'sender.min' => 'حداقل تعداد کاراکتر مشخصات: 3',
        'sender.max' => 'حداکثر تعداد کاراکتر مشخصات: 50',
    ])]
    public string $sender;

    #[Validate('nullable|string', message: [
        'email.string' => 'ایمیل نامعتبر',
    ])]
    public string $email;

    #[Validate('nullable|string', message: [
        'mobile.string' => 'شماره همراه از نوع متن است',
    ])]
    public string $mobile;

    #[Validate('nullable|string|min:3|max:100', message: [
        'message.string' => 'پیام از نوع متن است',
        'message.min' => 'حداقل تعداد کاراکتر پیام: 3',
        'message.max' => 'حداکثر تعداد کاراکتر پیام: 50',
    ])]
    public string $message;

    #[Validate('nullable|integer|exists:survey_formats,id', message: [
        'survey_format.integer' => 'دسته بندی از نوع عددی است',
        'survey_format.exists' => 'دسته بندی نامعتبر',
    ])]
    public $survey_format;

    #[Validate('nullable|integer|exists:countries,id', message: [
        'country.integer' => 'کشور از نوع عددی است',
        'country.exists' => 'کشور نامعتبر',
    ])]
    public $country;

    #[Validate('nullable|integer|exists:states,id', message: [
        'state.integer' => 'استان از نوع عددی است',
        'state.exists' => 'استان نامعتبر',
    ])]
    public $state;

    /**
     * Survey post status
     * @var int
     */
    public int $status;

    #[Validate('nullable|integer|exists:users,id', message: [
        'editor.integer' => 'ویرایش کننده از نوع عددی است',
        'editor.exists' => 'ویرایش کننده نامعتبر',
    ])]
    public $editor;

    /**
     * Country list
     * @var array
     */
    public array $countries = [];

    /**
     * States list
     * @var array
     */
    public array $states = [];

    /**
     * Survey formats list
     * @var array
     */
    public array $survey_formats = [];

    /**
     * Editors list
     * @var array
     */
    public array $editors = [];

    /**
     * Reset search form
     * @return void
     */
    public function resetForm(): void
    {
        $this->resetExcept('survey');
        $this->resetErrorBag();
        $this->setInitialValues($this->survey->id);
    }

    /**
     * Search and re-render component after validate search form
     * @return void
     */
    public function search(): void
    {
        $this->validate();
        $this->setInitialValues($this->survey->id);
    }

    /**
     * Mount the component
     * @param $survey_id
     * @return void
     */
    public function mount($survey_id): void
    {
        $this->setInitialValues($survey_id);
    }

    /**
     * Set initial dynamic values
     * @param $survey_id
     * @return void
     */
    public function setInitialValues($survey_id = null): void
    {
        $this->survey = Survey::findOrFail($survey_id);

        $countries = SurveyPost::pluck('country');
        foreach ($countries as $country) {
            $countryModel = Country::find($country);
            if ($countryModel) $this->countries[$country] = $countryModel->name;
        }

        $states = SurveyPost::pluck('state');
        foreach ($states as $state) {
            $stateModel = State::find($state);
            if ($stateModel) $this->states[$state] = $stateModel->name;
        }

        $survey_formats = SurveyPost::pluck('survey_format_id');
        foreach ($survey_formats as $survey_format) {
            $surveyFormatModel = SurveyFormat::find($survey_format);
            if ($surveyFormatModel) $this->survey_formats[$survey_format] = $surveyFormatModel->name;
        }

        $editors = SurveyPost::whereHas('surveyFormatInfo', function ($query) {
            $query->where('survey_id', $this->survey->id);
        })
            ->pluck('editor');
        foreach ($editors as $editor) {
            $userModel = User::find($editor);
            if ($userModel) $this->editors[$editor] = $userModel->name;
        }

        $collator = new \Collator('fa_IR');

        $collator->asort($this->editors);
        $collator->asort($this->survey_formats);
        $collator->asort($this->states);
        $collator->asort($this->countries);
    }

    /**
     * Change survey post status
     * @param $post_id
     * @param $format_id
     * @return void
     */
    public function changeStatus($post_id, $format_id): void
    {
        $post = SurveyPost::where('id', $post_id)->where('survey_format_id', $format_id)->firstOrFail();
        $post->status == 2 ? $post->status = 1 : $post->status = 2;
        $post->editor = auth()->user()->id;
        $post->save();
    }

    /**
     * Render the component
     * @return Factory|\Illuminate\Contracts\View\View|Application|View
     */
    public function render(): Factory|Application|\Illuminate\Contracts\View\View|View
    {
        if (!auth()->user()->can('مدیریت پویش ها | وضعیت پویش')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $survey = SurveyPost::query();

        $survey->when(isset($this->model_id), function ($query) {
            $query->where('id', $this->model_id);
        });

        $survey->when(isset($this->title), function ($query) {
            $query->where('title', 'like', '%' . $this->title . '%');
        });

        $survey->when(isset($this->survey_format), function ($query) {
            $query->where('survey_format_id', $this->survey_format);
        });

        $survey->when(isset($this->email), function ($query) {
            $query->where('email', 'like', '%' . $this->email . '%');
        });

        $survey->when(isset($this->mobile), function ($query) {
            $query->where('mobile', 'like', '%' . $this->mobile . '%');
        });

        $survey->when(isset($this->country), function ($query) {
            $query->where('country', $this->country);
        });

        $survey->when(isset($this->state), function ($query) {
            $query->where('state', $this->state);
        });

        $survey->when(isset($this->created_at), function ($query) {
            $query->whereDate('created_at', Jalalian::fromFormat('Y/m/d', $this->created_at)->toCarbon());
        });

        $survey->when(isset($this->updated_at), function ($query) {
            $query->whereDate('updated_at', Jalalian::fromFormat('Y/m/d', $this->updated_at)->toCarbon());
        });

        $survey->when(isset($this->status), function ($query) {
            $query->where('status', '=', (string)$this->status);
        });

        $survey->when(isset($this->adder), function ($query) {
            $query->where('adder', '=', $this->adder);
        });

        $survey->when(isset($this->editor), function ($query) {
            $query->where('editor', '=', $this->editor);
        });

        return view('livewire.survey.participants', [
            'survey' => $this->survey->whereIn('survey_format_id', $this->survey->surveyFormats()->pluck('id')->toArray())->orderByDesc('created_at'),
            'title' => 'پویش - وضعیت پویش | ' . $this->survey->title,
            'posts' => $survey->orderByDesc('created_at')->paginate(50),
        ])->title('پویش - وضعیت پویش | ' . $this->survey->title);
    }

    /**
     * Show post file
     * @param $id
     * @return Application|Redirector|RedirectResponse
     */
    public function showFile($id): Application|Redirector|RedirectResponse
    {
        $file = File::where('id', $id)->where('model_id', $this->survey->id)->firstOrFail()->src;
        return redirect(Storage::url(str_replace('/storage/', '', $file)));
    }

    /**
     * Change file status
     * @param $id
     * @return void
     */
    public function changeFileStatus($id): void
    {
        $file = File::where('id', $id)->firstOrFail();
        $file->status = !$file->status;
        $file->save();
    }

    /**
     * Change chosen status
     * @param $post_id
     * @param $format_id
     * @return void
     */
    public function changeChosenStatus($post_id, $format_id): void
    {
        $post = SurveyPost::where('id', $post_id)->where('survey_format_id', $format_id)->firstOrFail();
        $post->chosen = !$post->chosen;
        $post->save();
    }
}
