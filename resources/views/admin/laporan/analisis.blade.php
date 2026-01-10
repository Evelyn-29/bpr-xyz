<x-layouts.app :title="'Laporan Analisis Kredit'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Laporan Analisis Kredit</h1>
    </x-slot>
    <div class="bg-white p-6 rounded-2xl shadow-sm no-print">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="text-sm font-bold text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="border border-gray-200 rounded-lg text-sm p-2 w-full">
            </div>
            <div>
                <label class="text-sm font-bold text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="border border-gray-200 rounded-lg text-sm p-2 w-full">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700">Filter</button>

            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank"
                class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold ml-auto flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </form>
    </div>

    <div class="mt-6 bg-white p-8 rounded-2xl shadow-sm print:shadow-none">
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-6 hidden print:block">
            <h1 class="text-2xl font-bold uppercase">BPR XYZ</h1>
            <h2 class="text-xl font-bold mt-2 underline">LAPORAN ANALISIS KREDIT</h2>
        </div>

        <table class="w-full text-xs text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300 print:bg-gray-200">
                    <th class="p-2 border">No. Pengajuan</th>
                    <th class="p-2 border">Nasabah</th>
                    <th class="p-2 border">Hasil SLIK (Admin)</th>
                    <th class="p-2 border">Rekomendasi Manager</th>
                    <th class="p-2 border">Catatan Manager</th>
                    <th class="p-2 border">Catatan Direktur</th>
                    <th class="p-2 border">Plafond Disetujui</th>
                    <th class="p-2 border">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr class="border-b">
                        <td class="p-2 border">{{ $app->no_pengajuan }}</td>
                        <td class="p-2 border font-bold">{{ $app->nasabahProfile->nama_lengkap }}</td>
                        <td class="p-2 border">
                            <div>Status: <strong>{{ $app->slik_status ?? 'Belum' }}</strong></div>
                            <div class="italic text-gray-500">{{ $app->slik_notes }}</div>
                        </td>
                        <td class="p-2 border">
                            @if ($app->recommendation_status)
                                {{ $app->recommendation_status }}
                                @if ($app->recommendation_status == 'Rekomendasi Disetujui')
                                    <br><span class="text-green-600">Plf: Rp
                                        {{ number_format($app->manager_recommended_amount) }}</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-2 border italic">{{ $app->manager_note ?? '-' }}</td>
                        <td class="p-2 border italic">{{ $app->direktur_note ?? '-' }}</td>
                        <td class="p-2 border font-bold text-right">Rp {{ number_format($app->recommended_amount) }}
                        </td>
                        <td class="p-2 border font-bold text-center">{{ $app->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
