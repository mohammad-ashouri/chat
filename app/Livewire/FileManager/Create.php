<?php

namespace App\Livewire\FileManager;

use App\Models\File;
use Carbon\Carbon;
use getID3;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('مدیریت فایل - ایجاد فایل')]
class Create extends Component
{
    use WithFilePond;

    /**
     * Make form for create file
     * @var \App\Livewire\Forms\FileManager\Create
     */
    public \App\Livewire\Forms\FileManager\Create $form;

    /**
     * Mount the component
     * @return void
     */
    public function mount(): void
    {
        if (!auth()->user()->can('مدیریت فایل')) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    public function store(): void
    {
        if (!auth()->user()->can('مدیریت فایل')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        try {
            $this->form->validate();

            $fileName = uniqid() . '_' . Carbon::now()->format('s-i-H-d-m-Y');
            $file = $this->form->file->storeAs('files/made', $fileName, 'public');
            $file_url = Storage::url($file);

            $mimeType = $this->form->file->getMimeType();
            \Log::info('File MIME type: ' . $mimeType);

            $type = 'file';
            $duration = null;

            if (str_starts_with($mimeType, 'image/')) {
                $type = 'picture';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $type = 'video';
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $type = 'audio';
                $getID3 = new getID3();
                $filePath = $this->form->file->getPathname();

                $fileInfo = $getID3->analyze($filePath);

                if (isset($fileInfo['playtime_seconds'])) {
                    $duration = round($fileInfo['playtime_seconds'], 2);
                    \Log::info('Duration from playtime_seconds: ' . $duration);
                } elseif (isset($fileInfo['audio']['playtime_seconds'])) {
                    $duration = round($fileInfo['audio']['playtime_seconds'], 2);
                    \Log::info('Duration from audio.playtime_seconds: ' . $duration);
                } else {
                    \Log::info('No duration found in file info');
                    $duration = 0;
                }
            }

            $fileData = [
                'title' => $this->form->title,
                'type' => $type,
                'src' => $file_url,
                'adder' => auth()->user()->id,
                'duration' => $duration
            ];

            \Log::info('Final file data: ' . json_encode($fileData));
            $createdFile = File::create($fileData);
            \Log::info('Created file: ' . json_encode($createdFile->toArray()));
            session()->flash('success', 'فایل با موفقیت ایجاد شد.');
            $this->redirect(route('file-manager.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ثبت فایل: ' . $e->getMessage());
            $this->redirect(route('file-manager.create'));
        }
    }
}
