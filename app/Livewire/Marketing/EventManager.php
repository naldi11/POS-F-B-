<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\EventPromotion;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class EventManager extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $theme = 'valentine'; // valentine, kemerdekaan, natal, general
    public $headline;
    public $description;
    public $banner_image;
    public $existingBanner;
    public $coupon_code;
    public $discount_percentage = 0;
    public $start_date;
    public $end_date;
    public $is_active = true;
    
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'theme' => 'required|in:valentine,kemerdekaan,natal,general',
        'headline' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'banner_image' => 'nullable|image|max:2048',
        'coupon_code' => 'nullable|string|max:50',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'is_active' => 'boolean',
    ];

    public function openModal()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $event = EventPromotion::findOrFail($id);
        $this->editingId = $id;
        $this->title = $event->title;
        $this->theme = $event->theme;
        $this->headline = $event->headline;
        $this->description = $event->description;
        $this->existingBanner = $event->banner_image;
        $this->coupon_code = $event->coupon_code;
        $this->discount_percentage = $event->discount_percentage;
        $this->start_date = $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : null;
        $this->end_date = $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : null;
        $this->is_active = $event->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $bannerPath = $this->existingBanner;
        if ($this->banner_image) {
            if ($this->existingBanner) {
                Storage::disk('public')->delete($this->existingBanner);
            }
            $bannerPath = $this->banner_image->store('events', 'public');
        }

        EventPromotion::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title' => $this->title,
                'theme' => $this->theme,
                'headline' => $this->headline,
                'description' => $this->description,
                'banner_image' => $bannerPath,
                'coupon_code' => strtoupper($this->coupon_code),
                'discount_percentage' => $this->discount_percentage ?: 0,
                'start_date' => $this->start_date ?: null,
                'end_date' => $this->end_date ?: null,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->editingId ? 'Promo Event berhasil diperbarui.' : 'Promo Event berhasil dibuat.');
        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $event = EventPromotion::findOrFail($id);
        $event->update(['is_active' => !$event->is_active]);
        session()->flash('message', 'Status Promo Event berhasil diubah.');
    }

    public function delete($id)
    {
        $event = EventPromotion::findOrFail($id);
        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }
        $event->delete();
        session()->flash('message', 'Promo Event berhasil dihapus.');
    }

    public function resetInputFields()
    {
        $this->title = '';
        $this->theme = 'valentine';
        $this->headline = '';
        $this->description = '';
        $this->banner_image = null;
        $this->existingBanner = null;
        $this->coupon_code = '';
        $this->discount_percentage = 0;
        $this->start_date = null;
        $this->end_date = null;
        $this->is_active = true;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.marketing.event-manager', [
            'events' => EventPromotion::latest()->paginate(10)
        ]);
    }
}
