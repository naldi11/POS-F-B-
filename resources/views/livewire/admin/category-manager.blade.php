<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg backdrop-blur-lg bg-opacity-80 p-6">
                @if (session()->has('message'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium">{{ session('message') }}</p>
                    </div>
                @endif
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Daftar Kategori</h3>
                    <button wire:click="create" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-5 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Kategori
                    </button>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">ID</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Nama Kategori</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900 text-center">Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition duration-150">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $category->id }}</td>
                                    <td class="px-6 py-4">{{ $category->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-full {{ $category->is_active ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="edit({{ $category->id }})" class="font-medium text-orange-500 hover:text-orange-700 mx-2 transition-colors">Edit</button>
                                        <button wire:click="delete({{ $category->id }})" class="font-medium text-gray-400 hover:text-red-500 mx-2 transition-colors">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Data Kategori</h3>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                            <input type="text" id="name" wire:model="name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('name') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Kategori Aktif</span>
                            </label>
                            @error('is_active') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-orange-500 text-sm font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto transition">
                            Simpan Data
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-200 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
