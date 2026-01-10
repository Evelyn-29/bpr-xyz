@php
    $biaya_provisi = ($application->recommended_amount * 1.5) / 100;
    $jumlah_diterima = $application->recommended_amount - $biaya_provisi;
@endphp

<x-layouts.nasabah :title="'Detail Pengajuan'">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            {{-- Update: Tampilkan No Pengajuan (Tiket) alih-alih ID --}}
            <h1 class="font-bold text-xl">
                Pinjaman Aktif # <span
                    class="font-mono text-blue-600">{{ $application->no_pengajuan ?? '#' . $application->id }}</span>
            </h1>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Info Utama --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. INFORMASI KREDIT --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Kredit</h3>

                {{-- BARU: Tampilkan Nomor PK jika Disetujui --}}
                @if ($application->status == 'Disetujui' && $application->no_perjanjian_kredit)
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                        <div class="p-2 bg-green-100 rounded-full text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-green-800 uppercase tracking-wider">Nomor Perjanjian Kredit
                            </p>
                            <p class="text-lg font-mono font-bold text-gray-900">
                                {{ $application->no_perjanjian_kredit }}</p>
                        </div>
                    </div>
                @endif

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Fasilitas</dt>
                        <dd class="font-medium">{{ $application->creditFacility->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Tujuan</dt>
                        <dd class="font-medium">{{ $application->tujuan_pinjaman }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jumlah Diajukan</dt>
                        <dd class="font-medium text-lg text-orange-600">Rp
                            {{ number_format($application->jumlah_pinjaman, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jumlah Disetujui</dt>
                        <dd class="font-medium text-lg text-green-600">Rp
                            {{ number_format($application->recommended_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jangka Waktu diajukan</dt>
                        <dd class="font-medium text-orange-600">{{ $application->jangka_waktu }} Bulan</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jangka Waktu disetujui</dt>
                        <dd class="font-medium text-green-600">{{ $application->recommended_tenor }} Bulan</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Potongan Admin & Provisi (1,5%)</dt>
                        <dd class="font-medium text-orange-600">Rp {{ number_format($biaya_provisi, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jumlah diterima</dt>
                        <dd class="font-medium text-green-600">Rp {{ number_format($jumlah_diterima, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- 3. JADWAL ANGSURAN (BARU) --}}
            {{-- Hanya muncul jika status Disetujui dan tabel payments sudah digenerate --}}
            @if ($application->status == 'Disetujui' && $application->payments->count() > 0)
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Jadwal Angsuran
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3">Ke</th>
                                    <th class="px-4 py-3">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-right">Tagihan</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($application->payments as $pay)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium">{{ $pay->angsuran_ke }}</td>
                                        <td class="px-4 py-3">{{ $pay->jatuh_tempo->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-right font-medium">Rp
                                            {{ number_format($pay->jumlah_angsuran, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($pay->status_pembayaran == 'Paid')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Lunas
                                                </span>
                                            @elseif($pay->status_pembayaran == 'Partial')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Sebagian
                                                </span>
                                            @elseif($pay->jatuh_tempo < now() && $pay->status_pembayaran == 'Unpaid')
                                                {{-- Logic Telat Bayar --}}
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Telat
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        {{-- Kolom Kanan: Status --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Status Terkini</h3>

                <div class="text-center py-4">
                    @if ($application->status == 'Disetujui')
                        <div
                            class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-green-700">Disetujui</h4>
                        <p class="text-sm text-gray-500 mt-1">Selamat! Pengajuan Anda diterima.</p>
                    @elseif($application->status == 'Lunas')
                        <div
                            class="w-16 h-16 bg-gray-800 text-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">Lunas / Selesai</h4>
                        <p class="text-sm text-gray-500 mt-1">Terima kasih, kewajiban kredit Anda telah selesai.</p>

                        <a href="{{ route('nasabah.pengajuan.step1') }}"
                            class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                            Ajukan Kredit Baru
                        </a>
                    @elseif($application->status == 'Ditolak')
                        <div
                            class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-red-700">Ditolak</h4>
                        <p class="text-sm text-gray-500 mt-1">Mohon maaf, pengajuan belum memenuhi syarat.</p>
                    @elseif($application->status == 'Menunggu Verifikasi')
                        <div
                            class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-blue-700">Dalam Proses</h4>
                        <p class="text-sm text-gray-500 mt-1">Tim kami sedang memverifikasi data Anda.</p>
                    @else
                        <div
                            class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-yellow-700">Draft</h4>
                        <p class="text-sm text-gray-500 mt-1">Pengajuan belum diselesaikan.</p>
                        <a href="{{ route('nasabah.pengajuan.step1') }}"
                            class="mt-4 inline-block bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-yellow-600">Lanjutkan
                            Pengisian</a>
                    @endif
                </div>

                <div class="mt-6 pt-6 pb-3 border-t">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Diajukan pada:</span>
                        <span
                            class="font-medium">{{ $application->submitted_at ? $application->submitted_at->format('d M Y') : '-' }}</span>
                    </div>
                    @if ($application->approved_at)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Disetujui pada:</span>
                            <span class="font-medium">{{ $application->approved_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. DATA AGUNAN --}}
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Agunan</h3>
                @if ($application->collateral)
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Jenis Sertifikat</dt>
                            <dd class="font-medium">{{ $application->collateral->jenis_agunan }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">No. Sertifikat</dt>
                            <dd class="font-medium">{{ $application->collateral->nomor_sertifikat }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Atas Nama</dt>
                            <dd class="font-medium">{{ $application->collateral->atas_nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Foto Agunan</dt>
                            <a href="{{ Storage::url($application->collateral->foto_agunan) }}" target="_blank"
                                class="text-blue-600 hover:underline text-sm font-semibold">Lihat Foto</a>
                        </div>
                    </dl>
                @else
                    <p class="text-gray-500 text-sm">Data agunan belum lengkap.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.nasabah>
