<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Setting;

#[Layout('layouts.app')]
class ReceiptSettings extends Component
{
    public $receipt_paper_size = '58mm';
    public $receipt_printer_type = 'thermal';
    public $receipt_store_name = 'RUMPO CAFE';
    public $receipt_address = 'Jl. Contoh No. 123, Kota';
    public $receipt_phone = '0812-3456-7890';
    public $receipt_email = '';
    public $receipt_website = '';
    public $receipt_social_media = '';

    public function mount()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $this->receipt_paper_size = $settings['receipt_paper_size'] ?? '58mm';
        $this->receipt_printer_type = $settings['receipt_printer_type'] ?? 'thermal';
        $this->receipt_store_name = $settings['receipt_store_name'] ?? 'RUMPO CAFE';
        $this->receipt_address = $settings['receipt_address'] ?? 'Jl. Contoh No. 123, Kota';
        $this->receipt_phone = $settings['receipt_phone'] ?? '0812-3456-7890';
        $this->receipt_email = $settings['receipt_email'] ?? '';
        $this->receipt_website = $settings['receipt_website'] ?? '';
        $this->receipt_social_media = $settings['receipt_social_media'] ?? '';
    }

    public function updatedReceiptPrinterType($value)
    {
        if ($value === 'standard') {
            $this->receipt_paper_size = 'a4';
        } elseif ($this->receipt_paper_size === 'a4') {
            $this->receipt_paper_size = '58mm';
        }
    }

    public function save()
    {
        $this->validate([
            'receipt_paper_size' => 'required|in:58mm,80mm,a4',
            'receipt_printer_type' => 'required|in:thermal,standard',
            'receipt_store_name' => 'required|string|max:100',
            'receipt_address' => 'required|string|max:255',
            'receipt_phone' => 'nullable|string|max:50',
            'receipt_email' => 'nullable|email|max:100',
            'receipt_website' => 'nullable|string|max:100',
            'receipt_social_media' => 'nullable|string|max:150',
        ]);

        $settings = [
            'receipt_paper_size' => $this->receipt_paper_size,
            'receipt_printer_type' => $this->receipt_printer_type,
            'receipt_store_name' => $this->receipt_store_name,
            'receipt_address' => $this->receipt_address,
            'receipt_phone' => $this->receipt_phone,
            'receipt_email' => $this->receipt_email,
            'receipt_website' => $this->receipt_website,
            'receipt_social_media' => $this->receipt_social_media,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('message', 'Pengaturan struk berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.admin.receipt-settings');
    }
}
