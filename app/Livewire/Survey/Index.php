<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

#[Title('پویش')]
class Index extends Component
{
    public int $status;

    #[Validate('nullable|integer|exists:users,id', message: [
        'adder.integer' => 'ثبت کننده از نوع عددی است',
        'adder.exists' => 'ثبت کننده نامعتبر',
    ])]
    public $adder;

    #[Validate('nullable|integer|exists:users,id', message: [
        'editor.integer' => 'ویرایش کننده از نوع عددی است',
        'editor.exists' => 'ویرایش کننده نامعتبر',
    ])]
    public $editor;

    /**
     * Adders list
     * @var array
     */
    public array $adders = [];

    /**
     * Editors list
     * @var array
     */
    public array $editors = [];

    /**
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        $this->setInitialValues();
    }

    /**
     * Reset search form
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->resetErrorBag();
    }

    /**
     * Search and re-render component after validate search form
     * @return void
     */
    public function search(): void
    {
        $this->validate();
    }

    /**
     * Listeners
     * @var string[]
     */
    protected $listeners = [
        'set-selected-id' => 'setSelectedId',
    ];

    /**
     * Post variable
     * @var Survey
     */
    public Survey $survey;

    /**
     * Set selected id to post
     * @param $id
     * @return void
     */
    public function setSelectedId($id): void
    {
        $this->survey = Survey::findOrFail($id);
        $this->dispatch('open-modal', 'delete');
    }

    /**
     * Delete selected post
     * @return void
     */
    public function delete(): void
    {
        $this->survey->delete();
        $this->dispatch('close-modal', 'delete');
        $this->dispatch('show-notification', 'success-notification');
    }

    /**
     * Set initial dynamic values
     * @return void
     */
    public function setInitialValues(): void
    {
        $adders = Survey::pluck('adder');
        foreach ($adders as $adder) {
            $userModel = User::find($adder);
            $this->adders[$adder] = $userModel->name;
        }

        $editors = Survey::pluck('editor');
        foreach ($editors as $editor) {
            $userModel = User::find($editor);
            if ($userModel) $this->editors[$editor] = $userModel->name;
        }

        $collator = new \Collator('fa_IR');

        $collator->asort($this->adders);
        $collator->asort($this->editors);
    }

    /**
     * Render posts index component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت پویش ها | صفحه اصلی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $surveys = Survey::query();

        $surveys->when(!empty($this->model_id), function ($query) {
            $query->where('id', $this->model_id);
        });

        $surveys->when(!empty($this->title), function ($query) {
            $query->where('title', 'like', '%' . $this->title . '%');
        });


        $surveys->when(!empty($this->created_at), function ($query) {
            $query->whereDate('created_at', Jalalian::fromFormat('Y/m/d', $this->created_at)->toCarbon());
        });

        $surveys->when(!empty($this->updated_at), function ($query) {
            $query->whereDate('updated_at', Jalalian::fromFormat('Y/m/d', $this->updated_at)->toCarbon());
        });

        $surveys->when(isset($this->status), function ($query) {
            $query->where('status', '=', (string)$this->status);
        });

        $surveys->when(!empty($this->adder), function ($query) {
            $query->where('adder', '=', $this->adder);
        });

        $surveys->when(!empty($this->editor), function ($query) {
            $query->where('editor', '=', $this->editor);
        });


        return view('livewire.survey.index', [
            'surveys' => $surveys->orderByDesc('updated_at')->orderByDesc('created_at')->paginate(50)
        ]);
    }
}
