<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

new #[Layout('layouts.customer')] class extends Component {
    public ?Table $table = null;
    public ?Table $occupiedTable = null;
    public $errorMessage = '';

    public function mount()
    {
        $tableId = request()->query('table');

        if ($tableId) {
            $this->processTable($tableId);
        } elseif (Session::has('table_id')) {
            $table = Table::find(Session::get('table_id'));
            if ($table) {
                $hasActiveOrders = Order::where('table_id', $table->id)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->exists();

                if ($table->status === 'occupied' || $hasActiveOrders) {
                    $this->occupiedTable = $table;
                    Session::forget(['table_id', 'table_number', 'cart']);
                } else {
                    Session::forget(['table_id', 'table_number']);
                }
            } else {
                Session::forget(['table_id', 'table_number']);
            }
        }
    }

    public function processTable($id)
    {
        $this->errorMessage = '';
        $this->table = Table::where('table_number', $id)->first() ?? Table::find($id);

        if (!$this->table || $this->table->status === 'maintenance') {
            $this->errorMessage = 'Meja tidak ditemukan atau sedang dalam perbaikan.';
            $this->occupiedTable = null;
            return;
        }

        // Failsafe otomatis: Cek apakah meja tercatat occupied padahal pesanan lama sudah completed/cancelled
        $hasActiveOrders = Order::where('table_id', $this->table->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        if (!$hasActiveOrders && $this->table->status === 'occupied') {
            $this->table->update(['status' => 'available']);
        }

        // Jika meja terisi atau masih memiliki pesanan aktif
        if ($this->table->status === 'occupied' || $hasActiveOrders) {
            $this->occupiedTable = $this->table;
            Session::forget(['table_id', 'table_number', 'cart']);
            return;
        }

        // Meja tersedia
        $this->occupiedTable = null;
        Session::put('table_id', $this->table->id);
        Session::put('table_number', $this->table->table_number);
        $this->redirect(route('customer.menu'), navigate: true);
    }

    public function checkTableStatus()
    {
        if (!$this->occupiedTable) return;
        $this->processTable($this->occupiedTable->table_number);
    }

    public function clearOccupiedTable()
    {
        $this->occupiedTable = null;
        $this->errorMessage = '';
        Session::forget(['table_id', 'table_number', 'cart']);
        return $this->redirect(route('welcome'), navigate: true);
    }

    #[\Livewire\Attributes\On('echo:tables,TableUpdated')]
    #[\Livewire\Attributes\On('echo:orders,OrderUpdated')]
    public function onTableUpdated()
    {
        if ($this->occupiedTable) {
            $this->occupiedTable->refresh();
            $hasActiveOrders = Order::where('table_id', $this->occupiedTable->id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->exists();

            if ($this->occupiedTable->status === 'available' && !$hasActiveOrders) {
                $this->processTable($this->occupiedTable->table_number);
            }
        }
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

    <div class="relative z-10 w-full max-w-md px-6 flex flex-col items-center my-8">
        
        <!-- Welcome Text -->
        <div class="text-center mb-8 transform transition-all translate-y-0 opacity-100" style="animation: fade-in-up 1s ease-out;">
            <div class="inline-flex items-center justify-center p-1 bg-white/10 rounded-full mb-5 ring-2 ring-orange-500/50 backdrop-blur-md overflow-hidden shadow-xl shadow-orange-500/20">
                <img src="{{ asset('logo/logo.jpg') }}" alt="Rumpo Cafe Logo" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-full">
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                Selamat Datang di <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-400">Rumpo Cafe</span>
            </h1>
            <p class="text-gray-300 text-sm md:text-base leading-relaxed">Nikmati hidangan terbaik kami dengan memindai QR Code di meja Anda.</p>
        </div>

        <!-- Main Card Container -->
        <div class="w-full bg-white/10 backdrop-blur-xl border border-white/20 p-6 sm:p-8 rounded-[2rem] shadow-2xl transition-all" style="animation: fade-in-up 1.2s ease-out;">
            
            @if($errorMessage)
                <div class="mb-6 bg-red-500/20 text-red-200 p-4 rounded-xl text-sm font-medium border border-red-500/30 flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            @if($occupiedTable)
                <!-- Pemberitahuan Meja Masih Terisi -->
                <div class="text-center py-4 px-2">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-red-500/20 text-red-400 mb-4 ring-2 ring-red-500/40">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-3xl bg-red-400 opacity-20"></span>
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>

                    <div class="inline-block px-3.5 py-1 bg-red-500/20 border border-red-500/40 rounded-full text-xs font-black text-red-300 uppercase tracking-wider mb-3">
                        Status Meja: Terisi
                    </div>

                    <h3 class="text-2xl font-black text-white mb-2">Meja {{ $occupiedTable->table_number }} Masih Terisi</h3>
                    
                    <p class="text-sm text-gray-300 leading-relaxed mb-6">
                        Meja ini masih tercatat digunakan oleh pelanggan sebelumnya.<br>
                        Jika meja ini sudah kosong, silakan <strong>lapor ke pihak kasir atau staf Rumpo Cafe</strong> untuk membuka dan mengosongkan meja ini.
                    </p>

                    <!-- Realtime Indicator -->
                    <div class="inline-flex items-center space-x-2 bg-white/5 border border-white/10 rounded-2xl p-3 mb-6 text-xs text-orange-200">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500"></span>
                        </span>
                        <span class="text-left font-medium">Menunggu konfirmasi kasir... Layar akan otomatis terbuka saat meja telah dikosongkan.</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button wire:click="checkTableStatus" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-orange-500 to-yellow-500 hover:from-orange-600 hover:to-yellow-600 text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center space-x-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span wire:loading.remove wire:target="checkTableStatus">Cek Ulang Status Meja</span>
                            <span wire:loading wire:target="checkTableStatus">Memeriksa...</span>
                        </button>

                        <button wire:click="clearOccupiedTable" class="w-full bg-white/10 hover:bg-white/15 text-gray-300 font-semibold py-3 px-4 rounded-2xl transition text-sm">
                            Pindai QR Meja Lain
                        </button>
                    </div>
                </div>
            @else
                <!-- QR Scanner Container -->
                <div class="relative rounded-2xl overflow-hidden bg-gray-900 border border-white/10 aspect-square shadow-inner">
                    <!-- Loading Skeleton -->
                    <div id="scanner-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-20">
                        <div class="w-12 h-12 border-4 border-orange-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <p class="text-orange-400 text-sm font-medium animate-pulse">Menyiapkan Kamera...</p>
                    </div>

                    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
                    <div id="reader" class="absolute inset-0 w-full h-full border-none bg-black"></div>
                    
                    <!-- Scanning animation overlay -->
                    <div class="absolute inset-0 pointer-events-none z-10 border-2 border-orange-500/30 rounded-2xl">
                        <div class="w-full h-1 bg-orange-500 shadow-[0_0_15px_#f97316] animate-[scan_2s_ease-in-out_infinite]"></div>
                    </div>

                    <style>
                        @keyframes scan {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(calc(100cqw - 4px)); }
                        }
                        #reader__dashboard_section_csr { display: none !important; }
                        #reader__dashboard_section_swaplink { display: none !important; }
                        #reader { border: none !important; }
                        #reader img { display: none !important; }
                        #reader video { object-fit: cover !important; width: 100% !important; height: 100% !important; }
                    </style>
                </div>

                <!-- Switch Camera Button Container (Below Scanner) -->
                <div id="camera-controls" class="mt-4 flex justify-center hidden">
                    <!-- Button will be injected here -->
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
                            { fps: 15, qrbox: { width: 280, height: 280 } },
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
                        
                        let loader = document.getElementById('scanner-loader');
                        if (loader) loader.classList.add('hidden');
                    }

                    async function initScanner() {
                        let readerElem = document.getElementById('reader');
                        if (!readerElem) return;

                        try {
                            cameras = await Html5Qrcode.getCameras();
                            if (!cameras || cameras.length === 0) {
                                readerElem.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-orange-400 font-medium text-sm text-center px-4">Kamera tidak ditemukan. Pastikan izin kamera telah diberikan.</div>';
                                return;
                            }
                            
                            currentCameraIndex = cameras.length > 1 ? 1 : 0;
                            await startScanner(cameras[currentCameraIndex].id);

                            if (cameras.length > 1) {
                                const controls = document.getElementById('camera-controls');
                                if (controls) {
                                    controls.classList.remove('hidden');
                                    controls.innerHTML = '';
                                    
                                    const btn = document.createElement('button');
                                    btn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Ganti Kamera`;
                                    btn.className = "flex items-center justify-center w-full sm:w-auto px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-lg transition-transform active:scale-95";
                                    btn.addEventListener('click', async () => {
                                        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                                        await startScanner(cameras[currentCameraIndex].id);
                                    });
                                    controls.appendChild(btn);
                                }
                            }
                        } catch (err) {
                            console.error('Camera init error:', err);
                        }
                    }

                    initScanner();
                </script>
                @endscript
            @endif
            
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
