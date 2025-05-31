<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('تماس با ما')]
class ContactUs extends Component
{
    /**
     * Set \App\Models\ContactUs headers
     * @var array
     */
    public array $headers = [];

    /**
     * Change status for \App\Models\ContactUs fixed
     * @param $contact_us_id
     * @return void
     */
    public function changeStatus($contact_us_id): void
    {
        if (!auth()->user()->canAny(['تماس با ما'])) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $contact_us = \App\Models\ContactUs::findOrFail($contact_us_id);
        $contact_us->status = true;
        $contact_us->seconder = auth()->id();
        $contact_us->save();
        session()->flash('success', 'تغییر وضعیت با موفقیت انجام شد');
    }

    /**
     * Show \App\Models\ContactUs headers
     * @param $id
     * @return void
     */
    public function showHeaders($id): void
    {
        if (!auth()->user()->can('تماس با ما')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $headers = \App\Models\ContactUs::findOrFail($id)->headers;
        $this->headers = json_decode($headers, true);
        $this->dispatch('open-modal', 'header-modal');
    }

    public function render()
    {
        if (!auth()->user()->can('تماس با ما')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $contact_us = \App\Models\ContactUs::query();
        return view('livewire.contact-us', [
            'contact_us' => $contact_us->orderByDesc('created_at')->paginate(50)
        ]);
    }
}
