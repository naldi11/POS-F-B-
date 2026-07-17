<div class="min-h-screen bg-gray-50 pt-6 pb-24 relative">
    <div class="max-w-md mx-auto px-4">
        
        <!-- Header -->
        <div class="flex items-center mb-6 sticky top-0 bg-gray-50 bg-opacity-90 backdrop-blur-sm pt-4 pb-2 z-10">
            <a href="{{ route('customer.menu', ['table_id' => session('table_id')]) }}" class="p-2 mr-3 bg-white rounded-full shadow-sm hover:shadow-md transition">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Keranjang Pesanan</h1>
        </div>

        @if(empty($cart))
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center flex flex-col items-center">
                <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
                <p class="text-sm text-gray-500 mb-6">Kamu belum menambahkan menu apapun ke dalam keranjang.</p>
                <a href="{{ route('customer.menu', ['table_id' => session('table_id')]) }}" class="w-full bg-orange-500 text-white font-semibold py-3 px-6 rounded-xl hover:bg-orange-600 transition">Lihat Menu</a>
            </div>
        @else
            <!-- Cart Items -->
            <div class="space-y-4 mb-8">
                @foreach($cart as $id => $item)
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                        @if($item['image'])
                            <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-xl">
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        
                        <div class="ml-4 flex-grow">
                            <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $item['name'] }}</h3>
                            <p class="text-orange-600 font-bold text-sm mb-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            @if(!empty($item['notes']))
                                <p class="text-xs text-gray-500 italic mb-2 bg-gray-50 p-1.5 rounded-lg border border-gray-100">Catatan: {{ $item['notes'] }}</p>
                            @else
                                <div class="mb-2"></div>
                            @endif
                            
                            <div class="flex items-center space-x-3">
                                <button wire:click="decrease('{{ $id }}')" class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <span class="font-semibold text-gray-900 w-4 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="increase('{{ $id }}')" class="w-7 h-7 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 hover:bg-orange-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="ml-2 flex flex-col items-end justify-between self-stretch">
                            <button wire:click="remove('{{ $id }}')" class="text-red-400 hover:text-red-600 transition p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <p class="font-bold text-gray-900 text-sm mt-auto">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-20">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500 text-sm">Subtotal</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-3 mt-1">
                    <span class="font-bold text-gray-900">Total Pembayaran</span>
                    <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Checkout Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                <div class="max-w-md mx-auto">
                    <a href="{{ route('customer.checkout') }}" class="w-full block text-center bg-orange-500 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:bg-orange-600 transition transform hover:scale-[1.02] active:scale-[0.98]">
                        Lanjutkan Pembayaran
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
