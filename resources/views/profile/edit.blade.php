<x-layouts.app :title="'Edit Profil'">

    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Pengaturan Akun</h1>
    </x-slot>

    <div class="space-y-6">

        @if (session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">Profil berhasil diperbarui.</span>
            </div>
        @elseif (session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">Password berhasil diubah.</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-gray-100">
                <h2 class="text-lg font-medium text-gray-900 mb-5 border-b pb-3">Informasi Profil</h2>
                <p class="text-sm text-gray-600 mb-6">Perbarui informasi profil akun dan alamat email Anda.</p>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-6 bg-white shadow-sm rounded-2xl border border-gray-100">
                <h2 class="text-lg font-medium text-gray-900 mb-5 border-b pb-3">Perbarui Password</h2>
                <p class="text-sm text-gray-600 mb-6">Pastikan akun Anda menggunakan password yang panjang dan acak agar
                    tetap aman.</p>

                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('current_password', 'updatePassword')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('password', 'updatePassword')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('password_confirmation', 'updatePassword')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit"
                            class="bg-gray-800 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-900 transition">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</x-layouts.app>
