<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Table;
use Illuminate\Support\Facades\Session;

new #[Layout('layouts.customer')] class extends Component {
    public ?Table $table = null;
    public $errorMessage = '';

    public function mount()
    {
        $tableId = request()->query('table');

        if ($tableId) {
            $this->processTable($tableId);
        } elseif (Session::has('table_id')) {
            // Cek apakah ada pesanan aktif (belum selesai) untuk meja ini
            $activeOrder = \App\Models\Order::where('table_id', Session::get('table_id'))
                ->whereNotIn('status', ['completed'])
                ->latest()
                ->first();

            if ($activeOrder) {
                // Ada pesanan aktif, langsung ke menu
                $this->redirect(route('customer.menu'), navigate: true);
            } else {
                // Tidak ada pesanan aktif, hapus session & minta scan ulang
                Session::forget('table_id');
                Session::forget('table_number');
            }
        }
    }

    public function processTable($id)
    {
        // Mencari berdasarkan table_number terlebih dahulu (karena QR Code menggunakan table_number)
        // Fallback ke find($id) jika tidak ditemukan.
        $this->table = Table::where('table_number', $id)->first() ?? Table::find($id);

        if (!$this->table || $this->table->status === 'maintenance') {
            $this->errorMessage = 'Meja tidak ditemukan atau sedang dalam perbaikan.';
            return;
        }

        Session::put('table_id', $this->table->id);
        Session::put('table_number', $this->table->table_number);
        $this->redirect(route('customer.menu'), navigate: true);
    }
}; ?>

<div class="relative min-h-screen flex flex-col items-center justify-center bg-gray-900 overflow-hidden text-white">
    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-20" alt="Coffee Background">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-yellow-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-20"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-6 flex flex-col items-center">
        
        <!-- Welcome Text -->
        <div class="text-center mb-10 transform transition-all translate-y-0 opacity-100" style="animation: fade-in-up 1s ease-out;">
            <div class="inline-flex items-center justify-center p-1 bg-white/10 rounded-full mb-6 ring-2 ring-orange-500/50 backdrop-blur-md overflow-hidden shadow-xl shadow-orange-500/20">
                <img src="{{ asset('logo/logo.jpg') }}" alt="Rumpo Cafe Logo" class="w-24 h-24 md:w-28 md:h-28 object-cover rounded-full">
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                Selamat Datang di <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400">Rumpo Cafe</span>
            </h1>
            <p class="text-gray-300 text-lg leading-relaxed">Nikmati hidangan terbaik kami. Silakan pindai QR Code di meja Anda untuk melihat menu dan mulai memesan.</p>
        </div>

        <!-- Scanner Card -->
        <div class="w-full bg-white/10 backdrop-blur-xl border border-white/20 p-6 sm:p-8 rounded-[2rem] shadow-2xl transition-all hover:bg-white/15" style="animation: fade-in-up 1.2s ease-out;">
            
            @if($errorMessage)
                <div class="mb-6 bg-red-500/20 text-red-200 p-4 rounded-xl text-sm font-medium border border-red-500/30 flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <!-- QR Scanner Container -->
            <div class="relative rounded-2xl overflow-hidden bg-gray-900/50 border border-white/10 aspect-square flex items-center justify-center">
                <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
                <div id="reader" class="w-full h-full [&_video]:object-cover [&_video]:w-full [&_video]:h-full border-none"></div>
                
                <style>
                    /* Customizing html5-qrcode UI */
                    #reader__dashboard_section_csr span, 
                    #reader__dashboard_section_csr a { color: #facc15 !important; }
                    #reader__dashboard_section_swaplink { color: #fb923c !important; font-weight: bold; }
                    #reader__camera_selection { background: #374151; color: white; border-radius: 0.5rem; padding: 0.5rem; margin-bottom: 0.5rem; border: none; width: 100%; max-width: 250px;}
                    #reader__dashboard_section_csr button { background: #f97316; color: white; border: none; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: bold; cursor: pointer; transition: 0.2s;}
                    #reader__dashboard_section_csr button:hover { background: #ea580c; }
                    #reader { border: none !important; }
                    #reader img { display: none !important; } /* Hide the default logo if any */
                </style>
            </div>

            @script
            <script>
                let html5Qrcode = null;
                let currentCameraIndex = 0;
                let cameras = [];

                async function startScanner(cameraId) {
                    if (html5Qrcode && html5Qrcode.isScanning) {
                        await html5Qrcode.stop();
                    }
                    if (!html5Qrcode) {
                        html5Qrcode = new Html5Qrcode('reader');
                    }
                    await html5Qrcode.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            html5Qrcode.stop();
                            try {
                                let url = new URL(decodedText);
                                let tableId = new URLSearchParams(url.search).get('table');
                                $wire.processTable(tableId ?? decodedText);
                            } catch(e) {
                                $wire.processTable(decodedText);
                            }
                        },
                        () => {}
                    );
                }

                async function initScanner() {
                    try {
                        cameras = await Html5Qrcode.getCameras();
                        if (!cameras || cameras.length === 0) {
                            document.getElementById('reader').innerHTML = '<p style="color:#f97316;text-align:center;padding:1rem;">Kamera tidak ditemukan.</p>';
                            return;
                        }
                        // Default ke kamera belakang jika tersedia
                        currentCameraIndex = cameras.length > 1 ? 1 : 0;
                        await startScanner(cameras[currentCameraIndex].id);

                        // Tambahkan tombol switch kamera jika ada lebih dari 1
                        if (cameras.length > 1) {
                            const btn = document.createElement('button');
                            btn.textContent = '🔄 Ganti Kamera';
                            btn.style.cssText = 'margin-top:10px;width:100%;background:#f97316;color:white;border:none;padding:8px 16px;border-radius:9999px;font-weight:bold;cursor:pointer;transition:0.2s;';
                            btn.addEventListener('click', async () => {
                                currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                                await startScanner(cameras[currentCameraIndex].id);
                            });
                            document.getElementById('reader').after(btn);
                        }
                    } catch (err) {
                        console.error('Camera init error:', err);
                    }
                }

                initScanner();
            </script>
            @endscript
            
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center space-x-2 text-sm font-medium text-gray-400 hover:text-orange-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <span>Login sebagai Admin / Staff</span>
                </a>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>

