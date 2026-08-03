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
        $qris = \App\Models\Qris::first();
        if ($qris) {
            $this->saved_qris_url = \Illuminate\Support\Facades\Storage::url($qris->image_path);
        }
    }

    public function saveQrisImage()
    {
        $this->validate([
            'qris_image' => 'required|image|max:51200', // max 50MB
        ]);

        $path = $this->qris_image->store('qris', 'public');
        
        $qris = \App\Models\Qris::first();
        if ($qris) {
            $qris->update(['image_path' => $path]);
        } else {
            \App\Models\Qris::create([
                'image_path' => $path,
                'is_active' => true
            ]);
        }

        $this->saved_qris_url = \Illuminate\Support\Facades\Storage::url($path);
        session()->flash('message', 'Gambar QRIS berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.admin.payment-manager');
    }
}
