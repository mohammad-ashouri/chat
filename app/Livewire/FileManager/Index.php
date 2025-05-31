<?php

namespace App\Livewire\FileManager;

use App\Models\File;
use Livewire\Attributes\Title;
use Livewire\Component;
use Morilog\Jalali\Jalalian;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('مدیریت فایل')]
class Index extends Component
{
    use WithFilePond;

    /**
     * Get file source for show in preview modal
     * @param $fileId
     * @return mixed
     */
    public function getFileSource($fileId): mixed
    {
        $file = File::findOrFail($fileId);
        return $file->src;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        if (!auth()->user()->can('مدیریت فایل')) {
            abort(403, 'دسترسی غیرمجاز');
        }
        
        $files = File::with('adderInfo')->orderByDesc('updated_at')->paginate(100);

        $files->getCollection()->transform(function ($file) {
            $file->jalali_created_at = Jalalian::fromDateTime($file->created_at)->format('H:i:s Y/m/d');
            return $file;
        });

        return view('livewire.file-manager.index', [
            'files' => $files,
        ]);
    }

    public function deleteFile($fileId): void
    {
        try {
            $file = File::findOrFail($fileId);

            // Check if file has no model
            if ($file->model) {
                session()->flash('error', 'این فایل به یک مدل متصل است و نمی‌توان آن را حذف کرد.');
                return;
            }

            // Delete the physical file from storage
//            $filePath = str_replace('/storage/', '', $file->src);
//            if (Storage::disk('public')->exists($filePath)) {
//                Storage::disk('public')->delete($filePath);
//            }

            // Delete the database record
            $file->delete();
            $this->dispatch('show-notification', 'success-notification');

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در حذف فایل: ' . $e->getMessage());
        }
    }
}
