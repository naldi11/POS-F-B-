<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use Livewire\WithFileUploads;

class PaymentManager extends Component
{
    use WithFileUploads;

    public $qris_image;
    public $saved_qris_url;

    public function mount()
    {
        $qrisSetting = Setting::where('key', 'qris_image')->first();
        if ($qrisSetting) {
            $this->saved_qris_url = \Illuminate\Support\Facades\Storage::url($qrisSetting->value);
        }
    }

    public function saveQrisImage()
    {
        $this->validate([
            'qris_image' => 'required|image|max:2048', // max 2MB
        ]);

        $path = $this->qris_image->store('settings', 'public');
        
        Setting::updateOrCreate(
            ['key' => 'qris_image'],
            ['value' => $path]
        );

        $this->saved_qris_url = \Illuminate\Support\Facades\Storage::url($path);
        session()->flash('message', 'Gambar QRIS berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.admin.payment-manager');
    }
}
