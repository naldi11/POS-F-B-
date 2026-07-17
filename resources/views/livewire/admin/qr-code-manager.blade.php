<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola QR Code</h2>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-2">QR Code Pelanggan</h3>
            <p class="text-gray-500 text-sm">Cetak dan letakkan QR code ini di semua meja. Pelanggan cukup memindai QR code ini untuk melihat menu.</p>
        </div>

        <div class="bg-orange-50 p-6 rounded-3xl border-2 border-dashed border-orange-200 mb-8 shadow-inner flex flex-col items-center justify-center w-full max-w-sm">
            @php
                // 1. SVG untuk Preview (Ukuran pas untuk tampilan web)
                $qrSvgPreview = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->margin(2)->generate($baseUrl ?: url('/menu'));
                
                // 2. SVG untuk Unduhan (Ukuran besar 500px dan terpusat di layar browser)
                $qrSvgDownload = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(500)->margin(4)->generate($baseUrl ?: url('/menu'));
                $qrSvgDownload = preg_replace('/<\?xml[^>]*\?>/', '', $qrSvgDownload); // Buang tag xml prolog

                $centeredSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" style="background-color: white;">
                    <svg x="50%" y="50%" style="overflow:visible">
                        <g transform="translate(-250, -250)">
                            ' . $qrSvgDownload . '
                        </g>
                    </svg>
                </svg>';

                $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($centeredSvg);
            @endphp
            <div class="w-full max-w-[250px] mx-auto rounded-xl overflow-hidden bg-white shadow-sm p-2 flex justify-center items-center">
                {!! $qrSvgPreview !!}
            </div>
        </div>

        <div class="w-full max-w-sm">
            <label for="baseUrl" class="block text-sm font-bold text-gray-700 mb-2 text-center">URL / IP Tujuan:</label>
            <p class="text-xs text-gray-500 mb-3 text-center">Ubah ke IP laptop Anda agar pelanggan bisa mengaksesnya.</p>
            
            <input type="text" id="baseUrl" wire:model.live.debounce.500ms="baseUrl" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition text-center mb-6" placeholder="http://192.168.1.x:8000/menu">
            
            <a href="{{ $qrDataUri }}" download="QR_Rumpo_Cafe.svg" class="w-full flex justify-center items-center space-x-2 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-md active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh QR Code</span>
            </a>
        </div>
    </div>
</div>
