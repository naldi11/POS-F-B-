<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class QrCodeManager extends Component
{
    public $baseUrl;

    private function getLocalIp()
    {
        $ip = '127.0.0.1'; // default fallback
        
        try {
            // Trik paling akurat untuk dapat IP Address asli (mengabaikan .local)
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
        } catch (\Exception $e) {
            // Abaikan error jika ekstensi socket tidak aktif
        }
        
        // Fallback jika gagal
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
            
            // Periksa jika URL yang tersimpan menggunakan IP atau .local
            $parsedHost = parse_url($this->baseUrl, PHP_URL_HOST);
            
            // Jika host valid dan (berupa IP ATAU mengandung .local)
            if ($parsedHost && (filter_var($parsedHost, FILTER_VALIDATE_IP) || str_ends_with($parsedHost, '.local'))) {
                // Jika host tersimpan beda dengan IP asli saat ini (dan bukan localhost), perbarui otomatis
                if ($parsedHost !== $currentIp && $parsedHost !== '127.0.0.1' && $parsedHost !== 'localhost') {
                    $this->baseUrl = str_replace($parsedHost, $currentIp, $this->baseUrl);
                    
                    Setting::updateOrCreate(
                        ['key' => 'qr_code_url'],
                        ['value' => $this->baseUrl]
                    );
                }
            }
        } else {
            // Default auto-detect local network IP
            $this->baseUrl = "http://{$currentIp}:8000/menu";
            
            // Save the default immediately
            Setting::updateOrCreate(
                ['key' => 'qr_code_url'],
                ['value' => $this->baseUrl]
            );
        }
    }

    public function updatedBaseUrl($value)
    {
        Setting::updateOrCreate(
            ['key' => 'qr_code_url'],
            ['value' => $value]
        );
    }

    public function render()
    {
        return view('livewire.admin.qr-code-manager');
    }
}
