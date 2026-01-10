<x-layouts.nasabah :title="'Edit Profil'">
    <x-slot name="header">
        <h1 class="text-base md:text-lg font-bold text-gray-800">Pengaturan Akun</h1>
    </x-slot>

    <div class="mx-auto space-y-8">

        @if (session('success'))
            <x-alert type="success">
                <strong>Berhasil!</strong> {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error">
                <strong>Gagal!</strong> {{ session('error') }}
            </x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- BAGIAN 1: EDIT BIODATA (Lebar 2 Kolom) --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800 mb-1">Biodata Diri</h2>
                    <p class="text-xs text-gray-500 mb-6">Informasi identitas utama tidak dapat diubah demi keamanan.
                        Jika data masih ada yang kosong, silakan <a href="{{ route('nasabah.pengajuan.step1') }}"
                            class="underline">buat pengajuan kredit.</a></p>

                    <form method="POST" action="{{ route('nasabah.profile-update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- READONLY FIELDS (LOCKED) --}}
                            <div
                                class="col-span-1 md:col-span-2 bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-2">
                                <h3 class="text-xs font-bold text-yellow-800 uppercase mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    Data Identitas (Terkunci)
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">No. KTP</label>
                                        <input type="text" value="{{ $profile->no_ktp }}" readonly
                                            class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap</label>
                                        <input type="text" value="{{ $profile->nama_lengkap ?? $user->name }}"
                                            readonly
                                            class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Ibu
                                            Kandung</label>
                                        <input type="text" value="{{ $profile->nama_ibu_kandung }}" readonly
                                            class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Jenis
                                            Kelamin</label>
                                        <input type="text" value="{{ $profile->jenis_kelamin }}" readonly
                                            class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- EDITABLE FIELDS --}}
                            <div class="col-span-1 md:col-span-2 mt-2">
                                <h3 class="text-sm font-bold text-gray-700 mb-3">Data Dapat Diubah</h3>
                            </div>

                            {{-- Email --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Login</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WA</label>
                                <input type="number" name="no_hp" value="{{ old('no_hp', $profile->no_hp) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                                <input type="number" name="no_npwp" value="{{ old('no_npwp', $profile->no_npwp) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Perkawinan</label>
                                <select name="status_perkawinan"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['Belum Menikah', 'Menikah', 'Cerai'] as $st)
                                        <option value="{{ $st }}"
                                            {{ old('status_perkawinan', $profile->status_perkawinan) == $st ? 'selected' : '' }}>
                                            {{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $pd)
                                        <option value="{{ $pd }}"
                                            {{ old('pendidikan_terakhir', $profile->pendidikan_terakhir) == $pd ? 'selected' : '' }}>
                                            {{ $pd }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                                <select name="agama"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $ag)
                                        <option value="{{ $ag }}"
                                            {{ old('agama', $profile->agama) == $ag ? 'selected' : '' }}>
                                            {{ $ag }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Rumah</label>
                                <select name="status_rumah"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['Milik Sendiri', 'Milik Keluarga', 'Sewa/Kontrak', 'Dinas'] as $sr)
                                        <option value="{{ $sr }}"
                                            {{ old('status_rumah', $profile->status_rumah) == $sr ? 'selected' : '' }}>
                                            {{ $sr }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat KTP</label>
                                <textarea name="alamat_ktp" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat_ktp', $profile->alamat_ktp) }}</textarea>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Tinggal Saat
                                    Ini</label>
                                <textarea name="alamat_tinggal" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat_tinggal', $profile->alamat_tinggal) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition shadow-md text-sm">
                                Simpan Biodata
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- BAGIAN 2: UBAH PASSWORD (Lebar 1 Kolom) --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Ubah Password</h2>

                    <form method="POST" action="{{ route('nasabah.password-update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password', 'updatePassword')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password" required autocomplete="new-password"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password', 'updatePassword')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-gray-800 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-gray-900 transition shadow-md text-sm">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.nasabah>
