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
        } else if (Session::has('table_id')) {
            $this->redirect(route('customer.menu'), navigate: true);
        }
    }

    public function processTable($id)
    {
        $this->table = Table::find($id);

        if (!$this->table || $this->table->status === 'maintenance') {
            $this->errorMessage = 'Meja tidak ditemukan atau sedang dalam perbaikan.';
            return;
        }

        Session::put('table_id', $this->table->id);
        $this->redirect(route('customer.menu'), navigate: true);
    }
}; ?>

<div class="flex flex-col items-center justify-center min-h-screen px-6 bg-gradient-to-b from-orange-50 to-white">
    <div class="w-full max-w-sm space-y-6 text-center">
        <!-- Logo/Icon -->
        <div class="mx-auto bg-orange-500 w-20 h-20 rounded-full flex items-center justify-center shadow-lg shadow-orange-200">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>

        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Rumpo Cafe</h1>
            <p class="text-gray-500">Scan QR Code di meja Anda untuk memesan</p>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-100 border border-gray-50">
            @if($errorMessage)
                <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm font-medium border border-red-100">
                    {{ $errorMessage }}
                </div>
            @endif

            <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
            <div id="reader" class="w-full overflow-hidden rounded-xl border-2 border-dashed border-orange-200"></div>

            <script>
                function onScanSuccess(decodedText, decodedResult) {
                    try {
                        let url = new URL(decodedText);
                        let params = new URLSearchParams(url.search);
                        let tableId = params.get('table');
                        if (tableId) {
                            @this.processTable(tableId);
                        } else {
                            @this.processTable(decodedText);
                        }
                    } catch(e) {
                        @this.processTable(decodedText);
                    }
                }

                function onScanFailure(error) {
                    // console.warn(`Code scan error = ${error}`);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    let html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        { fps: 10, qrbox: {width: 250, height: 250} },
                        /* verbose= */ false);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                });
            </script>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-400 hover:text-orange-500 transition-colors">Login sebagai Admin/Staff</a>
            </div>
        </div>
    </div>
</div>

