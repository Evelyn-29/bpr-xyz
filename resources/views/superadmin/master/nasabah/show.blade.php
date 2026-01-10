<x-layouts.app :title="'Detail Nasabah: ' . $nasabah->nama_lengkap">

    {{-- HEADER & NAVIGASI --}}
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <a href="{{ route('app.nasabah.index') }}"
                class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium mb-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Profil Nasabah</h1>
            <p class="text-sm text-gray-500">Kode CIF: <span
                    class="font-mono font-bold text-blue-600">{{ $nasabah->kode_nasabah ?? '-' }}</span></p>
        </div>

        <div class="flex gap-3">
            @can('edit_nasabah')
                <a href="{{ route('app.nasabah.edit', $nasabah->id) }}"
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 flex items-center gap-2 text-sm font-bold shadow-sm transition">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Data
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: RINGKASAN PROFIL --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- KARTU FOTO & INFO UTAMA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden text-center p-6">
                <div
                    class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4 text-blue-600 text-3xl font-bold border-4 border-blue-50">
                    {{ substr($nasabah->nama_lengkap, 0, 1) }}
                </div>
                <h2 class="text-lg font-bold text-gray-800">{{ $nasabah->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $nasabah->user->email ?? '-' }}</p>

                <div class="flex justify-center gap-2 mb-6">
                    <span
                        class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                        Terverifikasi
                    </span>
                    <span
                        class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold border border-gray-200">
                        {{ $nasabah->jenis_kelamin }}
                    </span>
                </div>

                <div class="border-t border-gray-100 pt-4 text-left space-y-3">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase">No. Handphone</span>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-800">{{ $nasabah->no_hp }}</span>
                            <a href="https://wa.me/62{{ substr($nasabah->no_hp, 1) }}" target="_blank"
                                class="text-green-500 hover:text-green-600">
                                <i class="fa-brands fa-whatsapp fa-lg"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase">Bergabung Sejak</span>
                        <span class="font-medium text-gray-800">{{ $nasabah->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- KARTU RINGKASAN KREDIT --}}
            <div class="bg-blue-600 rounded-xl shadow-lg shadow-blue-200 p-6 text-white">
                <h3 class="font-bold text-blue-100 text-sm mb-4">Total Melakukan Pinjaman</h3>
                <div class="text-3xl font-bold mb-1">
                    {{-- Menghitung total pinjaman yang Disetujui (Aktif) maupun Lunas (Selesai) --}}
                    {{ $nasabah->creditApplications->whereIn('status', ['Disetujui', 'Lunas'])->count() }}
                </div>
                <p class="text-sm text-blue-200">Kali Kredit Disetujui</p>
            </div>
        </div>

        {{-- KOLOM KANAN: DETAIL LENGKAP --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TABEL DATA PRIBADI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-600"></i>
                    <h3 class="font-bold text-gray-800">Biodata Lengkap</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Nomor KTP (NIK)</label>
                        <span class="text-base font-bold text-gray-800 font-mono">{{ $nasabah->no_ktp }}</span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Nomor NPWP</label>
                        <span class="text-base font-medium text-gray-800">{{ $nasabah->no_npwp ?? '-' }}</span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Nama Ibu Kandung</label>
                        <span class="text-base font-medium text-gray-800">{{ $nasabah->nama_ibu_kandung }}</span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Status Perkawinan</label>
                        <span class="text-base font-medium text-gray-800">{{ $nasabah->status_perkawinan }}</span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Pendidikan Terakhir</label>
                        <span class="text-base font-medium text-gray-800">{{ $nasabah->pendidikan_terakhir }}</span>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block">Agama</label>
                        <span class="text-base font-medium text-gray-800">{{ $nasabah->agama }}</span>
                    </div>
                </div>
            </div>

            {{-- TABEL ALAMAT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                    <h3 class="font-bold text-gray-800">Data Tempat Tinggal</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 font-medium block mb-1">Status Kepemilikan Rumah</label>
                        <span
                            class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold border border-gray-200">
                            {{ $nasabah->status_rumah }}
                        </span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label class="text-xs text-gray-500 font-bold uppercase block mb-2">Alamat Domisili</label>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $nasabah->alamat_tinggal }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label class="text-xs text-gray-500 font-bold uppercase block mb-2">Alamat KTP</label>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $nasabah->alamat_ktp }}</p>
                    </div>
                </div>
            </div>

            {{-- TABEL RIWAYAT PENGAJUAN (Relasi creditApplications) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                        <h3 class="font-bold text-gray-800">Riwayat Pengajuan Kredit</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 font-medium">No. Pengajuan</th>
                                <th class="px-6 py-3 font-medium">Tanggal</th>
                                <th class="px-6 py-3 font-medium">Nominal</th>
                                <th class="px-6 py-3 font-medium">Tenor</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($nasabah->creditApplications as $kredit)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 font-mono text-xs text-blue-600 font-bold">
                                        {{ $kredit->no_pengajuan }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $kredit->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-3 font-medium text-gray-800">
                                        Rp {{ number_format($kredit->jumlah_pinjaman, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $kredit->jangka_waktu }} Bln
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusClass = match ($kredit->status) {
                                                'Disetujui' => 'bg-green-100 text-green-700',
                                                'Ditolak' => 'bg-red-100 text-red-700',
                                                'Menunggu Verifikasi' => 'bg-yellow-100 text-yellow-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $statusClass }}">
                                            {{ $kredit->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('app.pengajuan.show', $kredit->id) }}"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            Detail <i class="fa-solid fa-arrow-right ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada riwayat pengajuan kredit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
