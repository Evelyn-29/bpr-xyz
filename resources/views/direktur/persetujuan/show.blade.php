<x-layouts.app :title="'Keputusan Kredit'">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.persetujuan.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Keputusan Kredit</h1>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{
        decision: '{{ old('decision') }}',
        maxTenor: {{ $application->creditFacility->max_jangka_waktu ?? 60 }},
        finalTenor: {{ old('final_tenor', $application->manager_recommended_tenor ?? $application->jangka_waktu) }},
    
        {{-- Mengambil nilai awal: dari old input, atau rekomendasi manager, atau pengajuan awal --}}
        finalAmount: '{{ old('final_amount')
            ? number_format((float) old('final_amount'), 2, ',', '.')
            : number_format($application->manager_recommended_amount ?? $application->jumlah_pinjaman, 2, ',', '.') }}',
    
        formatCurrency(val) {
            if (!val) return '';
            let cleanValue = val.toString().replace(/[^0-9,]/g, '');
            let parts = cleanValue.split(',');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            let decimalPart = parts.length > 1 ? parts[1] : null;
    
            if (decimalPart !== null) {
                return integerPart + ',' + decimalPart.substring(0, 2);
            }
            return integerPart;
        }
    }" x-init="finalAmount = formatCurrency(finalAmount)">

        {{-- KOLOM KIRI: SUMMARY & REKOMENDASI MANAGER --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Box Rekomendasi Manager --}}
            <div class="bg-yellow-50 rounded-2xl shadow-sm border border-yellow-200 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i class="fa-solid fa-gavel text-8xl text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-bold text-yellow-800 mb-4 border-b border-yellow-200 pb-2">Analisa & Rekomendasi
                    Manager</h3>

                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Keputusan Manager</p>
                        <p
                            class="text-lg font-bold {{ $application->recommendation_status == 'Rekomendasi Disetujui' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $application->recommendation_status }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Tgl Analisa</p>
                        <p class="text-gray-800 font-medium">
                            {{ $application->managed_at ? $application->managed_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>

                @if ($application->recommendation_status == 'Rekomendasi Disetujui')
                    <div class="grid grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-yellow-100 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Plafond Rekomendasi</p>
                            <p class="text-xl font-bold text-gray-800">Rp
                                {{ number_format($application->manager_recommended_amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tenor Rekomendasi</p>
                            <p class="text-xl font-bold text-gray-800">{{ $application->manager_recommended_tenor }}
                                Bulan</p>
                        </div>
                    </div>
                @endif

                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Catatan Manager</p>
                    <p class="text-sm text-gray-800 italic bg-white p-4 rounded-lg border border-yellow-100">
                        "{{ $application->manager_note }}"</p>
                </div>
            </div>

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

        {{-- KOLOM KANAN: FORM APPROVAL DIREKTUR --}}
        <div class="lg:col-span-1">
            @can('update_persetujuan')
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Keputusan Akhir</h3>
                        <p class="text-sm text-gray-500">Keputusan bersifat final dan mengikat.</p>
                    </div>

                    <form action="{{ route('app.persetujuan.update', $application->id) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin dengan keputusan ini?');">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 mb-6">
                            <label
                                class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-green-50 transition"
                                :class="decision === 'approve' ? 'bg-green-50 border-green-500 ring-1 ring-green-500' : ''">
                                <input type="radio" name="decision" value="approve" x-model="decision"
                                    class="w-5 h-5 text-green-600 focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-gray-900">SETUJUI PENGAJUAN</span>
                                </div>
                            </label>

                            <label
                                class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-red-50 transition"
                                :class="decision === 'reject' ? 'bg-red-50 border-red-500 ring-1 ring-red-500' : ''">
                                <input type="radio" name="decision" value="reject" x-model="decision"
                                    class="w-5 h-5 text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-gray-900">TOLAK PENGAJUAN</span>
                                </div>
                            </label>
                        </div>

                        {{-- FORM INPUT (Hanya jika Approve) --}}
                        <div x-show="decision === 'approve'" x-transition
                            class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Plafond Disetujui
                                        (Rp)</label>
                                    <input type="text" name="final_amount" x-model="finalAmount"
                                        @input="finalAmount = formatCurrency($event.target.value)"
                                        class="w-full rounded-lg border-gray-300 font-bold text-green-700 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tenor (Bulan)</label>
                                    <input type="number" name="final_tenor" x-model="finalTenor"
                                        @input="if(finalTenor > maxTenor) finalTenor = maxTenor"
                                        @blur="if(finalTenor < 1 || !finalTenor) finalTenor = 1"
                                        class="w-full rounded-lg border-gray-300 focus:ring-green-500">
                                    <p class="text-[10px] text-gray-500 mt-1">Maks: <span x-text="maxTenor"></span> Bln
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akad</label>
                                    <input type="date" name="tgl_akad" value="{{ old('tgl_akad', date('Y-m-d')) }}"
                                        class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Akad</label>
                                    <input type="time" name="jam_akad" value="{{ old('jam_akad', '10:00') }}"
                                        class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Direktur</label>
                            <textarea name="direktur_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm"
                                placeholder="Tambahkan pesan jika perlu...">{{ old('direktur_note') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-gray-900 text-white py-4 rounded-xl font-bold hover:bg-black transition shadow-lg">
                            Simpan Keputusan
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>
