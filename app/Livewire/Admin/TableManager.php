<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class TableManager extends Component
{
    public $baseUrl;
    public $newTableNumber = '';

    private function getLocalIp()
    {
        $ip = '127.0.0.1'; // default fallback
        
        try {
            if (function_exists('socket_create')) {
                $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                if ($sock) {
                    socket_connect($sock, "8.8.8.8", 53);
                    socket_getsockname($sock, $socketIp);
                    socket_close($sock);
                    if ($socketIp) {
                        $ip = $socketIp;
                        return $ip;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error
        }
        
        $fallback = gethostbyname(gethostname());
        if (filter_var($fallback, FILTER_VALIDATE_IP)) {
            $ip = $fallback;
        }
        
        return $ip;
    }

    public function mount()
    {
        $currentIp = $this->getLocalIp();
        $setting = Setting::where('key', 'qr_code_url')->first();
        
        if ($setting && !empty($setting->value)) {
            $this->baseUrl = $setting->value;
            
            $parsedHost = parse_url($this->baseUrl, PHP_URL_HOST);
            
            if ($parsedHost && (filter_var($parsedHost, FILTER_VALIDATE_IP) || str_ends_with($parsedHost, '.local'))) {
                if ($parsedHost !== $currentIp && $parsedHost !== '127.0.0.1' && $parsedHost !== 'localhost') {
                    $this->baseUrl = str_replace($parsedHost, $currentIp, $this->baseUrl);
                    Setting::updateOrCreate(['key' => 'qr_code_url'], ['value' => $this->baseUrl]);
                }
            }
        } else {
            $this->baseUrl = "http://{$currentIp}:8000/menu";
            Setting::updateOrCreate(['key' => 'qr_code_url'], ['value' => $this->baseUrl]);
        }
    }

    public function updatedBaseUrl($value)
    {
        Setting::updateOrCreate(['key' => 'qr_code_url'], ['value' => $value]);
    }

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

    public function render()
    {
        $tables = \App\Models\Table::orderByRaw('CAST(table_number AS UNSIGNED), table_number')->get();
        return view('livewire.admin.table-manager', [
            'tables' => $tables
        ]);
    }
}
