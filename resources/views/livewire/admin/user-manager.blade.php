<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg backdrop-blur-lg bg-opacity-80 p-6">
                
                {{-- Flash Message Success --}}
                @if (session()->has('message'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('message') }}</p>
                    </div>
                @endif

                {{-- Flash Message Error --}}
                @if (session()->has('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                @endif
                
                {{-- Header & Actions --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Pengguna</h3>
                        <p class="text-sm text-gray-500">Kelola akun dan peranan pengguna sistem</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Search Input --}}
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block pl-9 pr-4 py-2 transition w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Role Filter --}}
                        <select wire:model.live="roleFilter" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 p-2 transition">
                            <option value="">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="cashier">Kasir (Cashier)</option>
                            <option value="kitchen">Dapur (Kitchen)</option>
                            <option value="marketing">Marketing</option>
                        </select>

                        {{-- Add User Button --}}
                        <button wire:click="create" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-5 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Tambah Pengguna
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Pengguna</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Email</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Role</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-gray-900 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="bg-white border-b border-gray-50 hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                    @if (auth()->id() === $user->id)
                                                        <span class="text-[10px] bg-orange-100 text-orange-700 font-bold px-1.5 py-0.5 rounded">Anda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $roleClasses = [
                                                    'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'cashier' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'kitchen' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'marketing' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                ][$user->role] ?? 'bg-gray-50 text-gray-700 border-gray-200';

                                                $roleLabels = [
                                                    'admin' => 'Admin',
                                                    'cashier' => 'Kasir',
                                                    'kitchen' => 'Dapur',
                                                    'marketing' => 'Marketing',
                                                ][$user->role] ?? ucfirst($user->role);
                                            @endphp
                                            <span class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-full border {{ $roleClasses }}">
                                                {{ $roleLabels }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button wire:click="toggleStatus({{ $user->id }})" 
                                                @if(auth()->id() === $user->id) disabled title="Tidak dapat mengubah status akun sendiri" @endif
                                                class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-full cursor-pointer transition-all {{ $user->is_active ? 'bg-green-50 text-green-600 border border-green-200 hover:bg-green-100' : 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' }} {{ auth()->id() === $user->id ? 'opacity-60 cursor-not-allowed' : '' }}">
                                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button wire:click="edit({{ $user->id }})" class="font-medium text-orange-500 hover:text-orange-700 mx-1.5 transition-colors">Edit</button>
                                            
                                            @if (auth()->id() !== $user->id)
                                                <button wire:click="delete({{ $user->id }})" wire:confirm="Apakah Anda yakin ingin menghapus pengguna ini?" class="font-medium text-gray-400 hover:text-red-500 mx-1.5 transition-colors">Hapus</button>
                                            @else
                                                <span class="text-xs text-gray-300 mx-1.5 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                            Tidak ada data pengguna yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Form --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $editingId ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-1">Nama Lengkap</label>
                            <input type="text" id="name" wire:model="name" placeholder="Masukkan nama lengkap" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('name') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-1">Alamat Email</label>
                            <input type="email" id="email" wire:model="email" placeholder="contoh@rumpocafe.com" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('email') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-1">
                                Password
                                @if($editingId)
                                    <span class="text-xs font-normal text-gray-400">(Biarkan kosong jika tidak ingin mengubah password)</span>
                                @endif
                            </label>
                            <input type="password" id="password" wire:model="password" placeholder="Minimal 6 karakter" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                            @error('password') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Role --}}
                        <div class="mb-4">
                            <label for="role" class="block text-gray-700 text-sm font-bold mb-1">Role / Peranan</label>
                            <select id="role" wire:model="role" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 transition">
                                <option value="cashier">Kasir (Cashier)</option>
                                <option value="admin">Administrator</option>
                                <option value="kitchen">Dapur (Kitchen)</option>
                                <option value="marketing">Marketing</option>
                            </select>
                            @error('role') <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Is Active --}}
                        <div class="mb-2">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">Akun Aktif</span>
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
