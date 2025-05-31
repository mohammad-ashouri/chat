<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;

#[Title('نظرات کاربران')]
class Comments extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:50', message: [
        'name.string' => 'مشخصات از نوع متن است',
        'name.max' => 'حداکثر تعداد کاراکتر مشخصات: 50',
    ])]
    public $name;

    #[Validate('nullable|integer|exists:comments,id', message: [
        'model_id.integer' => 'آیدی پست از نوع عددی است',
        'model_id.exists' => 'آیدی پست نامعتبر',
    ])]
    public $model_id;

    #[Validate('nullable|string|max:100', message: [
        'message.string' => 'قسمتی از پیام از نوع متن است',
        'message.max' => 'حداکثر تعداد کاراکتر قسمتی از پیام: 100',
    ])]
    public $message;

    #[Validate('nullable|date', message: [
        'created_at.date' => 'تاریخ ایجاد از نوع تاریخ است',
    ])]
    public $created_at;

    #[Validate('nullable|date', message: [
        'updated_at.date' => 'تاریخ ویرایش از نوع تاریخ است',
    ])]
    public $updated_at;

    #[Validate('nullable|string|in:post,survey_post,survey', message: [
        'category.string' => 'دسته از نوع متن است',
        'category.in' => 'دسته نامعتبر',
    ])]
    public $category;

    #[Validate('nullable|integer|in:0,1,2', message: [
        'status.integer' => 'وضعیت از نوع عددی است',
        'status.in' => 'وضعیت نامعتبر',
    ])]
    public $status;

    #[Validate('nullable|integer|exists:users,id', message: [
        'user.integer' => 'کاربر از نوع عددی است',
        'user.exists' => 'کاربر نامعتبر',
    ])]
    public $user;

    /**
     * Users list
     * @var array
     */
    public array $users = [];

    /**
     * Search and re-render component after validate search form
     * @return void
     */
    public function search(): void
    {
        $this->validate();
    }

    /**
     * Set comment headers
     * @var array
     */
    public array $headers = [];

    /**
     * Change status for comment fixed
     * @param $comment_id
     * @param $status
     * @return void
     */
    public function changeStatus($comment_id, $status): void
    {
        if (!auth()->user()->canAny(['نظرات کاربران | تایید نظر', 'نظرات کاربران | رد نظر'])) {
            abort(403, 'دسترسی غیرمجاز');
        }

        switch ($status) {
            case 'a':
                $status = 1;
                break;
            case 'd':
                $status = 0;
                break;
            default:
                abort(422);
        }
        $comment = Comment::findOrFail($comment_id);
        $comment->status = $status;
        $comment->seconder = auth()->id();
        $comment->save();
        session()->flash('success', 'تغییر وضعیت با موفقیت انجام شد');
    }

    /**
     * Show comment headers
     * @param $id
     * @return void
     */
    public function showHeaders($id): void
    {
        if (!auth()->user()->can('نظرات کاربران | اطلاعات اضافی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $headers = Comment::findOrFail($id)->headers;
        $this->headers = json_decode($headers, true);
        $this->dispatch('open-modal', 'header-modal');
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
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        $users = Comment::pluck('seconder');
        if ($users->isNotEmpty() and $users[0] != null) {
            foreach ($users as $user) {
                $userModel = User::find($user);
                $this->users[$user] = $userModel->name;
            }
        }
    }

    /**
     * Render the component
     * @return View|Application|Factory|\Illuminate\View\View
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('نظرات کاربران | صفحه اصلی')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $comments = Comment::query();

        $comments->when(!empty($this->name), function ($query) {
            $query->where('name', 'like', '%' . $this->name . '%');
        });

        $comments->when(!empty($this->model_id), function ($query) {
            $query->where('model_id', $this->model_id);
        });

        $comments->when(!empty($this->message), function ($query) {
            $query->where('message', 'like', '%' . $this->message . '%');
        });

        $comments->when(!empty($this->created_at), function ($query) {
            $query->whereDate('created_at', Jalalian::fromFormat('Y/m/d', $this->created_at)->toCarbon());
        });

        $comments->when(!empty($this->updated_at), function ($query) {
            $query->whereDate('updated_at', Jalalian::fromFormat('Y/m/d', $this->updated_at)->toCarbon());
        });

        $comments->when(!empty($this->category), function ($query) {
            $query->where('model', $this->category);
        });

        $comments->when(!empty($this->status), function ($query) {
            $query->where('status', '=', $this->status);
        });

        $comments->when(!empty($this->user), function ($query) {
            $query->where('seconder', '=', $this->user);
        });

        return view('livewire.comments', [
            'comments' => $comments->orderByDesc('status')->orderByDesc('created_at')->paginate(50),
        ]);
    }
}
