<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function __invoke($id)
    {
        $order = Order::with(['table', 'orderDetails.menu', 'orderDetails.bundle'])->findOrFail($id);
        
        $settingsRaw = \App\Models\Setting::pluck('value', 'key')->toArray();
        $settings = [
            'paper_size' => $settingsRaw['receipt_paper_size'] ?? '58mm',
            'printer_type' => $settingsRaw['receipt_printer_type'] ?? 'thermal',
            'store_name' => $settingsRaw['receipt_store_name'] ?? 'RUMPO CAFE',
            'address' => $settingsRaw['receipt_address'] ?? 'Jl. Contoh No. 123, Kota',
            'phone' => $settingsRaw['receipt_phone'] ?? '0812-3456-7890',
            'email' => $settingsRaw['receipt_email'] ?? '',
            'website' => $settingsRaw['receipt_website'] ?? '',
            'social_media' => $settingsRaw['receipt_social_media'] ?? '',
        ];

        return view('print.order', compact('order', 'settings'));
    }
}
