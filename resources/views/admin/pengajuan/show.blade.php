<x-layouts.app :title="'Detail Pengajuan'">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.pengajuan.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Detail Pengajuan #{{ $application->no_pengajuan }}</h1>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================= KOLOM KIRI (SAMA PERSIS DENGAN SHOW) ================= --}}
        <div class="lg:col-span-2 space-y-6">
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $application->no_pengajuan }}</h2>

                        @php
                            $statusColors = [
                                'Menunggu Verifikasi' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Disetujui' => 'bg-green-100 text-green-700 border-green-200',
                                'Ditolak' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                            $statusClass = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                            {{ $application->status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        Diajukan pada:
                        {{ $application->submitted_at ? $application->submitted_at->format('d F Y, H:i') : '-' }}
                        WIB
                        oleh <span
                            class="font-semibold text-gray-700">{{ $application->nasabahProfile->nama_lengkap ?? $application->user->name }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-blue-50 rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                    <i class="fa-solid fa-magnifying-glass-chart mr-2"></i> Hasil Pengecekan Admin (SLIK)
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    @isset($application->slik_status)
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Status Kolektibilitas</p>
                            <p class="font-bold text-gray-800 text-lg">{{ $application->slik_status ?? 'Belum Ada Data' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">File SLIK</p>
                            @if ($application->slik_path)
                                <a href="{{ Storage::url($application->slik_path) }}" target="_blank"
                                    class="text-blue-600 underline text-sm font-semibold hover:text-blue-800">
                                    <i class="fa-solid fa-file-pdf mr-1"></i> Lihat PDF SLIK
                                </a>
                            @else
                                <span class="text-red-500 text-sm">File belum diupload</span>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 uppercase">Catatan Admin</p>
                            <p class="text-sm text-gray-700 italic bg-white p-3 rounded border border-blue-100 mt-1">
                                "{{ $application->slik_notes ?? 'Belum ada catatan' }}"
                            </p>
                        </div>
                    @else
                        <p class="text-gray-400">Belum ada upload SLIK dari Admin</p>
                    @endisset
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">
                    <i class="fa-solid fa-sack-dollar text-blue-600 mr-2"></i> Informasi Kredit
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Fasilitas Kredit</p>
                        <p class="text-base font-medium text-gray-900">{{ $application->creditFacility->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Tujuan Penggunaan</p>
                        <p class="text-base font-medium text-gray-900">{{ $application->tujuan_pinjaman }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Plafond Pengajuan</p>
                        <p class="text-xl font-bold text-green-600">Rp
                            {{ number_format($application->jumlah_pinjaman, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Jangka Waktu</p>
                        <p class="text-base font-medium text-gray-900">{{ $application->jangka_waktu }} Bulan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">
                    <i class="fa-solid fa-user text-blue-600 mr-2"></i> Data Pemohon
                </h3>
                @php $profile = $application->nasabahProfile; @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    <x-detail-item label="NIK / No. KTP" :value="$profile->no_ktp" />
                    <x-detail-item label="Nama Lengkap" :value="$profile->nama_lengkap" />
                    <x-detail-item label="Jenis Kelamin" :value="$profile->jenis_kelamin" />
                    <x-detail-item label="Ibu Kandung" :value="$profile->nama_ibu_kandung" />
                    <x-detail-item label="Status Perkawinan" :value="$profile->status_perkawinan" />
                    <x-detail-item label="Pendidikan" :value="$profile->pendidikan_terakhir" />
                    <x-detail-item label="No. HP / WA" :value="$profile->no_hp" />
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Alamat KTP</p>
                        <p class="text-sm font-medium text-gray-900">{{ $profile->alamat_ktp }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Alamat Domisili</p>
                        <p class="text-sm font-medium text-gray-900">{{ $profile->alamat_tinggal }}</p>
                    </div>
                </div>
            </div>

            @if ($application->detail)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">
                        <i class="fa-solid fa-users text-blue-600 mr-2"></i> Keluarga & Penjamin
                    </h3>

                    @if ($profile->status_perkawinan == 'Menikah')
                        <h4 class="text-sm font-bold text-gray-700 mb-3 bg-gray-50 p-2 rounded">Data Pasangan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8 mb-6">
                            <x-detail-item label="Nama Pasangan" :value="$application->detail->nama_pasangan" />
                            <x-detail-item label="NIK Pasangan" :value="$application->detail->no_ktp_pasangan" />
                            <x-detail-item label="Pekerjaan" :value="$application->detail->pekerjaan_pasangan" />
                            <x-detail-item label="No. HP" :value="$application->detail->no_hp_pasangan" />
                        </div>
                    @endif

                    <h4 class="text-sm font-bold text-gray-700 mb-3 bg-gray-50 p-2 rounded">Data Penjamin</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8">
                        <x-detail-item label="Nama Penjamin" :value="$application->detail->nama_penjamin" />
                        <x-detail-item label="Hubungan" :value="$application->detail->hubungan_penjamin" />
                        <x-detail-item label="NIK Penjamin" :value="$application->detail->no_ktp_penjamin" />
                        <x-detail-item label="No. HP" :value="$application->detail->no_hp_penjamin" />
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">
                    <i class="fa-solid fa-house-lock text-blue-600 mr-2"></i> Data Agunan
                </h3>
                @if ($application->collateral)
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/3">
                            <div class="rounded-lg overflow-hidden border border-gray-200 group relative">
                                <img src="{{ Storage::url($application->collateral->foto_agunan) }}"
                                    class="w-full h-32 object-cover">
                                <a href="{{ Storage::url($application->collateral->foto_agunan) }}" target="_blank"
                                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                    <span class="text-white text-xs font-bold">Lihat Foto</span>
                                </a>
                            </div>
                        </div>

                        <div class="w-full md:w-2/3 grid grid-cols-2 gap-4">
                            <x-detail-item label="Jenis Sertifikat" :value="$application->collateral->jenis_agunan" />
                            <x-detail-item label="Nomor Sertifikat" :value="$application->collateral->nomor_sertifikat" />
                            <x-detail-item label="Atas Nama" :value="$application->collateral->atas_nama" />
                            @if ($application->collateral->jenis_agunan == 'SHGB')
                                <x-detail-item label="Masa Berlaku" :value="$application->collateral->masa_berlaku->format('d F Y')" />
                            @endif
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">File Sertifikat</p>
                                <a href="{{ Storage::url($application->collateral->file_sertifikat) }}" target="_blank"
                                    class="text-blue-600 text-xs font-bold bg-blue-50 px-3 py-1 rounded border border-blue-200 hover:bg-blue-100">
                                    Lihat File Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 italic text-sm">Tidak ada data agunan.</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">
                    <i class="fa-solid fa-folder-open text-blue-600 mr-2"></i> Dokumen Pendukung
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($application->documents as $doc)
                        <a href="{{ Storage::url($doc->path) }}" target="_blank"
                            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition group">
                            <div class="p-2 bg-red-100 text-red-600 rounded-lg mr-3 group-hover:bg-red-200">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-gray-700 truncate capitalize">
                                    {{ str_replace('_', ' ', str_replace('_path', '', $doc->jenis_dokumen)) }}
                                </p>
                                <p class="text-xs text-blue-500 group-hover:underline">Klik untuk melihat</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ================= KOLOM KANAN (READ ONLY KEPUTUSAN) ================= --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24">
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 mb-8">

                    <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">
                        <i class="fa-solid fa-gavel text-gray-600 mr-2"></i> Keputusan Manager
                    </h3>
                    @isset($application->recommendation_status)
                        {{-- Status Rekomendasi --}}
                        <div class="mb-6">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Status Rekomendasi</p>
                            @if ($application->recommendation_status == 'Rekomendasi Disetujui')
                                <div
                                    class="bg-green-100 text-green-800 px-4 py-3 rounded-xl border border-green-200 flex items-center">
                                    <i class="fa-solid fa-check-circle text-xl mr-3"></i>
                                    <div>
                                        <span class="block font-bold">DISETUJUI</span>
                                        <span class="text-xs">Direkomendasikan Lanjut</span>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="bg-red-100 text-red-800 px-4 py-3 rounded-xl border border-red-200 flex items-center">
                                    <i class="fa-solid fa-times-circle text-xl mr-3"></i>
                                    <div>
                                        <span class="block font-bold">DITOLAK</span>
                                        <span class="text-xs">Tidak Direkomendasikan</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Detail Angka (Hanya muncul jika disetujui) --}}
                        @if ($application->recommendation_status == 'Rekomendasi Disetujui')
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Plafond Direkomendasikan</p>
                                    <p class="text-xl font-bold text-green-700">Rp
                                        {{ number_format($application->manager_recommended_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Tenor Direkomendasikan</p>
                                    <p class="text-xl font-bold text-gray-800">
                                        {{ $application->manager_recommended_tenor }}
                                        Bulan</p>
                                </div>
                            </div>
                        @endif

                        {{-- Catatan --}}
                        <div class="mb-6">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Catatan Analisa (5C)</p>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm text-gray-700 italic">
                                "{{ $application->manager_note }}"
                            </div>
                        </div>

                        {{-- Info Waktu --}}
                        <div class="text-center text-xs text-gray-400 pt-4 border-t">
                            Diproses <b>{{ $application->manager->name }}</b> pada:
                            {{ $application->managed_at ? $application->managed_at->format('d F Y, H:i') : '-' }} WIB
                        </div>
                    @else
                        <p class="text-gray-400">Belum ada rekomendasi dari Manager</p>
                    @endisset

                </div>

                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">

                    <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">
                        <i class="fa-solid fa-gavel text-gray-600 mr-2"></i> Keputusan Direktur
                    </h3>

                    @if ($application->status == 'Disetujui' || $application->status == 'Ditolak' || $application->status == 'Lunas')
                        {{-- Persetujuan Direktur --}}
                        <div class="mb-6">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Status Pengajuan</p>
                            @if ($application->status == 'Disetujui')
                                <div
                                    class="bg-green-100 text-green-800 px-4 py-3 rounded-xl border border-green-200 flex items-center">
                                    <i class="fa-solid fa-check-circle text-xl mr-3"></i>
                                    <div>
                                        <span class="block font-bold">DISETUJUI</span>
                                        <span class="text-xs">Pengajuan kredit telah disetujui</span>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="bg-red-100 text-red-800 px-4 py-3 rounded-xl border border-red-200 flex items-center">
                                    <i class="fa-solid fa-times-circle text-xl mr-3"></i>
                                    <div>
                                        <span class="block font-bold">DITOLAK</span>
                                        <span class="text-xs">Pengajuan kredit telah ditolak</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Detail Angka (Hanya muncul jika disetujui) --}}
                        @if ($application->recommendation_status == 'Rekomendasi Disetujui')
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Plafond Disetujui</p>
                                    <p class="text-xl font-bold text-green-700">Rp
                                        {{ number_format($application->recommended_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Tenor Disetujui</p>
                                    <p class="text-xl font-bold text-gray-800">{{ $application->recommended_tenor }}
                                        Bulan</p>
                                </div>
                            </div>
                        @endif

                        {{-- Catatan --}}
                        <div class="mb-6">
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Catatan Direktur</p>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm text-gray-700 italic">
                                "{{ $application->direktur_note }}"
                            </div>
                        </div>

                        {{-- Info Waktu --}}
                        <div class="text-center text-xs text-gray-400 pt-4 border-t">
                            Diproses <b>{{ $application->direktur->name }}</b> pada:
                            {{ $application->approved_at ? $application->approved_at->format('d F Y, H:i') : '-' }} WIB
                        </div>
                    @else
                        <p class="text-gray-400">Belum ada keputusan dari Direktur</p>
                    @endif

                </div>
            </div>

        </div>

    </div>
</x-layouts.app>
