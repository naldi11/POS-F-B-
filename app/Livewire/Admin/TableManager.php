<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class TableManager extends Component
{
    public $newTableNumber = '';

    public function addTable()
    {
        $this->validate([
            'newTableNumber' => 'required|string|max:50|unique:tables,table_number',
        ], [
            'newTableNumber.unique' => 'Nomor meja ini sudah ada.',
            'newTableNumber.required' => 'Nomor meja wajib diisi.'
        ]);

        \App\Models\Table::create([
            'table_number' => trim($this->newTableNumber),
            'status' => 'available'
        ]);

        $this->newTableNumber = '';
        session()->flash('message', 'Meja berhasil ditambahkan.');
    }

    public function deleteTable($id)
    {
        $table = \App\Models\Table::findOrFail($id);
        
        // Prevent deletion if occupied or has active orders
        if ($table->status === 'occupied' || $table->orders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            session()->flash('error', 'Meja tidak bisa dihapus karena sedang digunakan atau ada pesanan aktif.');
            return;
        }

        $table->delete();
        session()->flash('message', 'Meja berhasil dihapus.');
    }

    public function getCustomerBaseUrl(): string
    {
        $host = request()->getHost();
        $adminDomain = env('ADMIN_DOMAIN', 'login.rumpocafe.site');

        // Jika diakses dari subdomain admin (misal: login.rumpocafe.site)
        if (str_starts_with($host, 'login.')) {
            $customerHost = substr($host, 6);
            $scheme = request()->getScheme();
            $port = request()->getPort();
            $url = $scheme . '://' . $customerHost;
            if ($port && !in_array($port, [80, 443])) {
                $url .= ':' . $port;
            }
            return $url;
        }

        if ($host === $adminDomain) {
            $customerHost = preg_replace('/^login\./i', '', $adminDomain);
            return request()->getScheme() . '://' . $customerHost;
        }

        // Jika local development (localhost, 127.0.0.1, IP, .local, .test)
        if ($host === 'localhost' || $host === '127.0.0.1' || filter_var($host, FILTER_VALIDATE_IP) || str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
            return request()->getSchemeAndHttpHost();
        }

        // Fallback: config('app.url') jika bukan domain admin
        $appUrl = config('app.url');
        if (!empty($appUrl) && parse_url($appUrl, PHP_URL_HOST) !== $adminDomain) {
            return rtrim($appUrl, '/');
        }

        return rtrim(url('/'), '/');
    }

    public function render()
    {
        $tables = \App\Models\Table::orderByRaw('CAST(table_number AS UNSIGNED), table_number')->get();
        return view('livewire.admin.table-manager', [
            'tables' => $tables,
            'customerBaseUrl' => $this->getCustomerBaseUrl()
        ]);
    }
}
