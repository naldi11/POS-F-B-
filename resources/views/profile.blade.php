<x-app-layout>

    <div class="space-y-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Profil Pengguna</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="p-8 bg-white shadow-sm border border-gray-100 rounded-xl">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-8 bg-white shadow-sm border border-gray-100 rounded-xl">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
