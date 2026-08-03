<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Marketing Dashboard</h2>
            <a href="{{ route('marketing.promotions') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                Kelola Promo
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Metric 1 -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="mb-1 text-sm font-medium text-gray-500">Promo Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $activePromosCount }}</h3>
                    </div>
                </div>
            </div>

            <!-- Metric 2 -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="mb-1 text-sm font-medium text-gray-500">Promo Digunakan</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $totalPromosUsed }} Kali</h3>
                    </div>
                </div>
            </div>

            <!-- Metric 3 -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="mb-1 text-sm font-medium text-gray-500">Total Diskon Diberikan</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Promos -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Promo Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentPromos as $promo)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">{{ $promo->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ ucfirst($promo->type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($promo->type === 'percentage')
                                    {{ $promo->value }}%
                                @else
                                    Rp {{ number_format($promo->value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($promo->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Belum ada promo yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
