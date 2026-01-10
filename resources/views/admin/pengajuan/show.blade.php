<x-layouts.app :title="'Detail Pengajuan ' . $application->no_pengajuan">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.pengajuan.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Detail Pengajuan</h1>
        </div>
    </x-slot>

    {{-- HEADER STATUS --}}
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
                Diajukan pada: {{ $application->submitted_at ? $application->submitted_at->format('d F Y, H:i') : '-' }}
                WIB
                oleh <span
                    class="font-semibold text-gray-700">{{ $application->nasabahProfile->nama_lengkap ?? $application->user->name }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI (Data Utama) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. INFORMASI KREDIT --}}
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

            {{-- 2. DATA PEMOHON --}}
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

            {{-- 3. DATA PASANGAN & PENJAMIN --}}
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

        </div>

        {{-- KOLOM KANAN (Agunan & Dokumen) --}}
        <div class="space-y-6">

            {{-- 4. AGUNAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">
                    <i class="fa-solid fa-house-lock text-blue-600 mr-2"></i> Agunan
                </h3>
                @if ($application->collateral)
                    <div class="space-y-4">
                        {{-- Foto Preview --}}
                        <div class="relative group rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ Storage::url($application->collateral->foto_agunan) }}" alt="Foto Agunan"
                                class="w-full h-48 object-cover">
                            <a href="{{ Storage::url($application->collateral->foto_agunan) }}" target="_blank"
                                class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                <span class="text-white font-semibold text-sm"><i class="fa-solid fa-eye mr-1"></i>
                                    Lihat Foto</span>
                            </a>
                        </div>

                        <div class="space-y-3">
                            <x-detail-item label="Jenis Sertifikat" :value="$application->collateral->jenis_agunan" />
                            <x-detail-item label="Nomor Sertifikat" :value="$application->collateral->nomor_sertifikat" />
                            <x-detail-item label="Atas Nama" :value="$application->collateral->atas_nama" />

                            <div class="pt-2">
                                <a href="{{ Storage::url($application->collateral->file_sertifikat) }}" target="_blank"
                                    class="block w-full text-center py-2 px-4 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-file-pdf mr-2"></i> Lihat File Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Data agunan tidak tersedia.</p>
                @endif
            </div>

            {{-- 5. DOKUMEN PENDUKUNG --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">
                    <i class="fa-solid fa-folder-open text-blue-600 mr-2"></i> Dokumen
                </h3>
                <ul class="space-y-3">
                    @foreach ($application->documents as $doc)
                        <li
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg group hover:bg-blue-50 transition">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="bg-white p-2 rounded shadow-sm text-red-500">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-700 truncate capitalize">
                                        {{ str_replace('_', ' ', str_replace('_path', '', $doc->jenis_dokumen)) }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($doc->path) }}" target="_blank"
                                class="text-gray-400 hover:text-blue-600 p-2" title="Lihat Dokumen">
                                <i class="fa-solid fa-external-link-alt"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- COMPONENT MODAL (Untuk Aksi Approve/Reject) --}}
    {{-- 1. Modal Reject --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeModal('rejectModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="#" method="POST"> {{-- Nanti isi routenya --}}
                    @csrf
                    <div class="bg-white p-6">
                        <div class="flex items-center gap-3 mb-4 text-red-600">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                            <h3 class="text-lg font-bold text-gray-900">Tolak Pengajuan?</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menolak pengajuan ini? Tindakan
                            ini tidak dapat dibatalkan.</p>

                        {{-- Alasan Penolakan (Optional Feature) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan
                                (Opsional)</label>
                            <textarea name="catatan" rows="3"
                                class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                placeholder="Contoh: Dokumen kurang lengkap..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Tolak Pengajuan
                        </button>
                        <button type="button" onclick="closeModal('rejectModal')"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
