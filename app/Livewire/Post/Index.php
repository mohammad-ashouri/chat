<?php

namespace App\Livewire\Post;

use App\Models\Catalogs\IranicMediaType;
use App\Models\Catalogs\PostType;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;

#[Title('ایرانیک مدیا')]
class Index extends Component
{
    use WithPagination;

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

    #[Validate('nullable|integer|exists:iranic_media_types,id', message: [
        'iranic_media_category.integer' => 'دسته بندی ایرانیک مدیا از نوع عددی است',
        'iranic_media_category.exists' => 'دسته بندی ایرانیک مدیا نامعتبر',
    ])]
    public $iranic_media_category;

    #[Validate('nullable|integer|exists:post_types,id', message: [
        'post_type.integer' => ' قالب رسانه از نوع عددی است',
        'post_type.exists' => 'قالب رسانه نامعتبر',
    ])]
    public $post_type;

    /**
     * Iranic media categories list
     * @var array
     */
    public array $iranic_media_categories = [];

    /**
     * Post types list
     * @var array
     */
    public array $post_types = [];

    #[Validate('nullable|integer|exists:posts,id', message: [
        'model_id.integer' => ' آیدی پست از نوع عددی است',
        'model_id.exists' => 'آیدی پست نامعتبر',
    ])]
    public ?int $model_id;

    #[Validate('nullable|string|min:3|max:100', message: [
        'title.string' => ' عنوان از نوع متن است',
        'title.min' => 'حداقل تعداد کاراکتر: 3',
        'title.max' => 'حداکثر تعداد کاراکتر: 100',
    ])]
    public string $title;

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
     * Listeners
     * @var string[]
     */
    protected $listeners = [
        'set-selected-id' => 'setSelectedId',
    ];

    /**
     * Post variable
     * @var Post
     */
    public Post $post;

    /**
     * Set selected id to post
     * @param $id
     * @return void
     */
    public function setSelectedId($id): void
    {
        $this->post = Post::findOrFail($id);
        $this->dispatch('open-modal', 'delete');
    }

    /**
     * Delete selected post
     * @return void
     */
    public function delete(): void
    {
        $this->post->delete();
        $this->dispatch('close-modal', 'delete');
        $this->dispatch('show-notification', 'success-notification');
    }

    /**
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        $this->setInitialValues();
    }

    /**
     * Set initial dynamic values
     * @return void
     */
    public function setInitialValues(): void
    {
        $adders = Post::pluck('adder');
        if ($adders->isNotEmpty() and $adders[0] != null) {
            foreach ($adders as $adder) {
                $userModel = User::find($adder);
                $this->adders[$adder] = $userModel->name;
            }
        }

        $editors = Post::pluck('editor');
        if ($editors->isNotEmpty() and $editors[0] != null) {
            foreach ($editors as $editor) {
                $userModel = User::find($editor);
                $this->editors[$editor] = $userModel->name;
                if ($userModel) $this->editors[$editor] = $userModel->name;
            }
        }

        $post_types = Post::pluck('post_type');
        foreach ($post_types as $post_type) {
            $post_type_model = PostType::find($post_type);
            $this->post_types[$post_type] = $post_type_model->name;
        }

        $iranic_media_categories = Post::pluck('iranic_media_type');
        foreach ($iranic_media_categories as $iranic_media_category) {
            $iranic_media_category_model = IranicMediaType::find($iranic_media_category);
            $this->iranic_media_categories[$iranic_media_category] = $iranic_media_category_model->name;
        }
        $collator = new \Collator('fa_IR');

        $collator->asort($this->iranic_media_categories);
        $collator->asort($this->post_types);
        $collator->asort($this->adders);
        $collator->asort($this->editors);
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
     * Render posts index component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت ایرانیک مدیا | صفحه اصلی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $posts = Post::query();

        $posts->when(!empty($this->model_id), function ($query) {
            $query->where('id', $this->model_id);
        });

        $posts->when(!empty($this->title), function ($query) {
            $query->where('title', 'like', '%' . $this->title . '%');
        });

        $posts->when(!empty($this->post_type), function ($query) {
            $query->where('post_type', $this->post_type);
        });

        $posts->when(!empty($this->iranic_media_category), function ($query) {
            $query->where('iranic_media_type', $this->iranic_media_category);
        });

        $posts->when(!empty($this->created_at), function ($query) {
            $query->whereDate('created_at', Jalalian::fromFormat('Y/m/d', $this->created_at)->toCarbon());
        });

        $posts->when(!empty($this->updated_at), function ($query) {
            $query->whereDate('updated_at', Jalalian::fromFormat('Y/m/d', $this->updated_at)->toCarbon());
        });

        $posts->when(isset($this->status), function ($query) {
            $query->where('status', '=', (string)$this->status);
        });

        $posts->when(!empty($this->adder), function ($query) {
            $query->where('adder', '=', $this->adder);
        });

        $posts->when(!empty($this->editor), function ($query) {
            $query->where('editor', '=', $this->editor);
        });
        return view('livewire.post.index', ['posts' => $posts->orderByDesc('updated_at')->orderByDesc('created_at')->paginate(50)]);
    }
}
