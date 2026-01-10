<x-layouts.nasabah :title="'Riwayat Pengajuan Kredit'">
    <x-slot name="header">
        <h1 class="font-bold">Status & Riwayat Kredit</h1>
    </x-slot>

    @if (session('success'))
        <x-alert type="success">
            <strong>Berhasil!</strong> {{ session('success') }}
        </x-alert>
    @endif

    @if (session('warning'))
        <x-alert type="warning">
            <strong>Maaf!</strong> {{ session('warning') }}
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

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        @if ($applications->isEmpty())
            <div class="p-10 text-center">
                <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-4">
                    {{-- Icon Empty --}}
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada pengajuan</h3>
                <p class="text-gray-500 mt-1 mb-6">Anda belum pernah mengajukan kredit sebelumnya.</p>
                <a href="{{ route('nasabah.pengajuan.step1') }}"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Ajukan Kredit Sekarang
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">No. Pengajuan</th>
                            <th class="px-6 py-4">Fasilitas</th>
                            <th class="px-6 py-4">Jumlah Pinjaman</th>
                            <th class="px-6 py-4">Tanggal Akad</th>
                            <th class="px-6 py-4">Jam Akad</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($applications as $app)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    @if ($app->status == 'Disetujui')
                                        <div class="text-xs font-bold text-green-600 mb-1">PK:
                                            {{ $app->no_perjanjian_kredit }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500">{{ $app->no_pengajuan }}</div>
                                    <div class="text-xs text-gray-400">{{ $app->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{-- KOREKSI: Pakai ->nama --}}
                                    {{ $app->creditFacility->nama ?? '-' }}
                                    <div class="text-xs text-gray-500">Tenor: {{ $app->jangka_waktu }} Bulan</div>
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    Rp {{ number_format($app->jumlah_pinjaman, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $app->tgl_akad ? \Carbon\Carbon::parse($app->tgl_akad)->format('d F Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $app->jam_akad ? \Carbon\Carbon::parse($app->jam_akad)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'draft_step1' => 'bg-gray-100 text-gray-600',
                                            'draft_step2' => 'bg-gray-100 text-gray-600',
                                            'draft_step3' => 'bg-yellow-100 text-yellow-700',
                                            'Menunggu Verifikasi' => 'bg-blue-100 text-blue-700',
                                            'Disetujui' => 'bg-green-100 text-green-700',
                                            'Ditolak' => 'bg-red-100 text-red-700',
                                        ];
                                        $class = $statusClasses[$app->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <div class="text-center py-2 rounded-full text-xs font-bold {{ $class }}">
                                        {{ str_replace('_', ' ', ucfirst($app->status)) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    <div
        class="bg-gray-200 rounded-2xl border border-gray-300 overflow-hidden mt-6 p-4 text-red-500 text-sm font-semibold">
        * Harap membawa file dokumen asli untuk dilampirkan saat pengikatan kredit.
    </div>
</x-layouts.nasabah>
