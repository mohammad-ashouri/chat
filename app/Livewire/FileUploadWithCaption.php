<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class FileUploadWithCaption extends Component
{
    use WithFileUploads;

    public $file;
    public $caption = '';
    public $uploadedFile = null;

    public function rules()
    {
        return [
            'file' => 'required|file|max:10240', // 10MB max
            'caption' => 'required|min:3|max:255',
        ];
    }

    public function save()
    {
        $this->validate();

        // Store the file
        $path = $this->file->store('uploads', 'public');

        // Here you can save the file path and caption to your database
        // For example:
        // YourModel::create([
        //     'file_path' => $path,
        //     'caption' => $this->caption
        // ]);

        $this->uploadedFile = $path;

        session()->flash('message', 'فایل با موفقیت آپلود شد!');

        $this->reset(['file', 'caption']);
    }

    public function render()
    {
        return view('livewire.file-upload-with-caption');
    }
}
