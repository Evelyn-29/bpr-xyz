<x-layouts.nasabah :title="'Pengajuan Kredit'">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-bold">Form Pengajuan Kredit</h1>
            <span class="text-sm text-gray-500 ms-4 pt-1">Step 1 dari 3</span>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success">
            <strong>Information:</strong> {{ session('success') }}
        </x-alert>
    @endif

    @if (session('warning'))
        <x-alert type="warning">
            <strong>Peringatan:</strong> {{ session('warning') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error">
            <strong>Error:</strong> {{ session('error') }}
        </x-alert>
    @endif

    <section id="pengajuan" class="bg-white rounded-2xl shadow-md p-8 mx-auto">
        @php
            $isLocked = !empty($profile->no_ktp);
        @endphp

        @if ($isLocked)
            <div
                class="mb-6 p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-100 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    Informasi identitas utama (<strong>Nama, KTP, Ibu Kandung, Jenis Kelamin</strong>)
                    diambil dari data profil Anda sebelumnya. Hubungi admin jika terdapat kesalahan data.
                </div>
            </div>
        @endif
        <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-2">Data Pemohon</h2>

        <form method="POST" action="{{ route('nasabah.pengajuan.step1.post') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if ($profile->kode_nasabah)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Nasabah</label>
                        <input type="text" value="{{ $profile->kode_nasabah }}" readonly
                            class="w-full md:w-1/3 bg-gray-100 border-gray-300 rounded-lg text-gray-600 font-mono font-bold shadow-sm cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Kode identitas unik Anda di sistem kami.</p>
                    </div>
                @endif
                <x-text-input name="nama_lengkap" label="Nama Lengkap" :value="old('nama_lengkap', $profile->nama_lengkap ?? Auth::user()->name)" :readonly="$isLocked" required />
                <x-text-input name="email" label="Email" type="email"
                    value="{{ old('email', $profile->email ?? Auth::user()->email) }}" required readonly />
                <x-select-input name="jenis_kelamin" label="Jenis Kelamin" :options="['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']" :value="$profile->jenis_kelamin"
                    :disabled="$isLocked" required />
                @if ($isLocked)
                    <input type="hidden" name="jenis_kelamin" value="{{ $profile->jenis_kelamin }}">
                @endif
                <x-text-input name="no_ktp" label="Nomor KTP" value="{{ old('no_ktp', $profile->no_ktp) }}"
                    :readonly="$isLocked" required />
                <x-text-input name="nama_ibu_kandung" label="Nama Ibu Kandung"
                    value="{{ old('nama_ibu_kandung', $profile->nama_ibu_kandung) }}" :readonly="$isLocked" required />
                <x-text-input name="no_hp" label="Nomor HP" value="{{ old('no_hp', $profile->no_hp) }}" required />
                <x-textarea-input name="alamat_tinggal" label="Alamat Tempat Tinggal" :value="$profile->alamat_tinggal" required />
                <x-textarea-input name="alamat_ktp" label="Alamat Sesuai KTP" :value="$profile->alamat_ktp" required />
                <x-select-input name="pendidikan_terakhir" label="Pendidikan Terakhir" :options="[
                    'SD' => 'SD',
                    'SMP' => 'SMP',
                    'SMA' => 'SMA',
                    'D3' => 'D3',
                    'S1' => 'S1',
                    'S2' => 'S2',
                    'S3' => 'S3',
                ]"
                    :value="$profile->pendidikan_terakhir" required />
                <x-select-input name="agama" label="Agama" :options="[
                    'Islam' => 'Islam',
                    'Protestan' => 'Protestan',
                    'Katolik' => 'Katolik',
                    'Hindu' => 'Hindu',
                    'Buddha' => 'Buddha',
                    'Konghucu' => 'Konghucu',
                ]" :value="$profile->agama" required />
                <x-select-input name="status_perkawinan" label="Status Perkawinan" :options="['Belum Menikah' => 'Belum Menikah', 'Menikah' => 'Menikah', 'Cerai' => 'Cerai']" :value="$profile->status_perkawinan"
                    required />
                <x-select-input name="status_rumah" label="Status Rumah" :options="['Milik Sendiri' => 'Milik Sendiri', 'Sewa' => 'Sewa']" :value="$profile->status_rumah"
                    required />
                <x-text-input name="no_npwp" label="No. NPWP (wajib untuk pinjaman lebih dari 50 juta)"
                    value="{{ old('no_npwp', $profile->no_npwp) }}" />
            </div>

            <div class="pt-8 mt-8 border-t text-right">
                <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-6 py-3 ms-2 rounded-xl hover:bg-blue-700 transition">
                    Simpan & Lanjut →
                </button>
            </div>
        </form>
    </section>

</x-layouts.nasabah>
