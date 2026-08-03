<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Meja</h2>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow-sm">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Pengaturan URL Base -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pengaturan Jaringan</h3>
        <div>
            <label for="baseUrl" class="block text-sm font-bold text-gray-700 mb-2">URL / IP Tujuan untuk QR Code:</label>
            <input type="text" id="baseUrl" wire:model.live.debounce.500ms="baseUrl" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition" placeholder="http://192.168.1.x:8000/menu">
            <p class="text-xs text-gray-500 mt-2">Pastikan URL ini dapat diakses oleh HP pelanggan (biasanya menggunakan IP lokal WiFi kafe).</p>
        </div>
    </div>

    <!-- Tambah Meja Baru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Meja Baru</h3>
        <form wire:submit.prevent="addTable" class="flex gap-4 items-start">
            <div class="flex-1">
                <input type="text" wire:model="newTableNumber" class="w-full bg-orange-50 border border-orange-200 text-orange-900 font-bold text-lg rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition" placeholder="Nomor / Nama Meja (Misal: 01, VIP-1)">
                @error('newTableNumber') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-sm active:scale-95 whitespace-nowrap">
                + Tambah Meja
            </button>
        </form>
    </div>

    <!-- Daftar Meja -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Meja</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tables as $table)
                @php
                    $fullUrl = ($baseUrl ?: url('/menu')) . '?table=' . urlencode(trim($table->table_number));
                    
                    // Preview SVG
                    $qrSvgPreview = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(1)->generate($fullUrl);

                    // Download SVG
                    $qrSvgDownload = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(500)->margin(4)->generate($fullUrl);
                    $qrSvgDownload = preg_replace('/<\?xml[^>]*\?>/', '', $qrSvgDownload);
                    $centeredSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" style="background-color: white;">
                        <svg x="50%" y="50%" style="overflow:visible">
                            <g transform="translate(-250, -250)">
                                ' . $qrSvgDownload . '
                            </g>
                        </svg>
                    </svg>';
                    $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($centeredSvg);
                @endphp
                <div class="border border-gray-200 rounded-2xl p-5 hover:shadow-md transition relative group bg-gray-50 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">Meja {{ $table->table_number }}</span>
                            <div class="mt-1">
                                @if($table->status === 'available')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200">Tersedia</span>
                                @elseif($table->status === 'occupied')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200">Terisi</span>
                                @elseif($table->status === 'reserved')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-yellow-200">Reservasi</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-gray-200">Perbaikan</span>
                                @endif
                            </div>
                        </div>
                        <button wire:click="deleteTable({{ $table->id }})" wire:confirm="Yakin ingin menghapus Meja {{ $table->table_number }}?" class="text-gray-400 hover:text-red-500 transition p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    <div class="flex-1 flex items-center justify-center py-4">
                        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                            {!! $qrSvgPreview !!}
                        </div>
                    </div>

                    <a href="{{ $qrDataUri }}" download="QR_Meja_{{ preg_replace('/[^A-Za-z0-9\-]/', '_', $table->table_number) }}.svg" class="mt-2 w-full flex justify-center items-center space-x-2 bg-white border border-gray-200 hover:bg-orange-50 text-orange-600 font-bold py-2 px-4 rounded-xl transition shadow-sm active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh QR</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-gray-500 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    Belum ada meja yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
