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

            {{-- 2. DATA SLIK (Summary) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Data SLIK (BI Checking)</h3>
                <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg">
                    <div>
                        <span class="text-xs text-gray-500 uppercase">Status Kolektibilitas</span>
                        <p class="font-bold text-blue-800 text-lg">{{ $application->slik_status }}</p>
                    </div>
                    <div>
                        <a href="{{ Storage::url($application->slik_path) }}" target="_blank"
                            class="px-4 py-2 bg-white text-blue-600 text-sm font-bold rounded shadow hover:bg-blue-50">
                            Lihat File PDF
                        </a>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-gray-500">Catatan Admin:</p>
                    <p class="text-sm text-gray-700">{{ $application->slik_notes ?? '-' }}</p>
                </div>
            </div>

            {{-- 3. DATA NASABAH --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <x-detail-item label="Nama Nasabah" :value="$application->nasabahProfile->nama_lengkap" />
                <x-detail-item label="Sumber Pendapatan" :value="$application->sumber_pendapatan" />
                <x-detail-item label="Tujuan Pinjaman" :value="$application->tujuan_pinjaman" />
            </div>
        </div>

        {{-- KOLOM KANAN: FORM APPROVAL DIREKTUR --}}
        <div class="lg:col-span-1">
            @can('update_persetujuan')
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sticky top-6">
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

                            <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-red-50 transition"
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
                                    <p class="text-[10px] text-gray-500 mt-1">Maks: <span x-text="maxTenor"></span> Bln</p>
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
