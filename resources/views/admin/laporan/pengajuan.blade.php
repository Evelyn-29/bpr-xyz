<x-layouts.app :title="'Laporan Persetujuan Kredit'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Laporan Persetujuan Kredit</h1>
    </x-slot>
    <div class="bg-white p-6 rounded-2xl shadow-sm">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="text-sm font-bold text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="border border-gray-200 rounded-lg text-sm p-2 w-full mt-1">
            </div>
            <div>
                <label class="text-sm font-bold text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="border border-gray-200 rounded-lg text-sm p-2 w-full mt-1">
            </div>
            <div>
                <label class="text-sm font-bold text-gray-700">Status</label>
                <select name="status" class="border border-gray-200 rounded-lg text-sm p-2 w-full mt-1">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ $status == 'Menunggu Verifikasi' ? 'selected' : '' }}>
                        Menunggu Verifikasi</option>
                    <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
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
            <h2 class="text-xl font-bold mt-2 underline">LAPORAN STATUS PENGAJUAN KREDIT</h2>
        </div>

        <table class="w-full text-xs text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300 print:bg-gray-200 text-center">
                    <th class="p-2 border">NO PENGAJUAN</th>
                    <th class="p-2 border">NAMA NASABAH</th>
                    <th class="p-2 border">JENIS KREDIT</th>
                    <th class="p-2 border">JENIS SERTIFIKAT</th>
                    <th class="p-2 border">TENOR</th>
                    <th class="p-2 border">PLAFOND </th>
                    <th class="p-2 border">PLAFOND DISETUJUI</th>
                    <th class="p-2 border">TANGGAL PENGAJUAN</th>
                    <th class="p-2 border">TANGGAL REALISASI</th>
                    <th class="p-2 border">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr>
                        <td class="p-3 border">{{ $app->no_pengajuan }}</td>
                        <td class="p-3 border">{{ $app->nasabahProfile->nama_lengkap ?? '-' }}</td>
                        <td class="p-3 border">{{ $app->creditFacility->nama ?? '-' }}</td>
                        <td class="text-center p-3 border">
                            {{ $app->recommended_tenor ?? $app->jangka_waktu }} Bln
                        </td>
                        <td class="text-center p-3 border">{{ $app->collateral->jenis_agunan ?? '-' }}</td>
                        <td class="text-right p-3 border">Rp {{ number_format($app->jumlah_pinjaman, 0, ',', '.') }}
                        </td>
                        <td class="text-right p-3 border">
                            @if ($app->status == 'Disetujui' || $app->status == 'Lunas')
                                Rp {{ number_format($app->recommended_amount ?? $app->jumlah_pinjaman, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center p-3 border">
                            {{ $app->submitted_at ? $app->submitted_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center p-3 border">{{ $app->tgl_akad ? $app->tgl_akad->format('d/m/Y') : '-' }}
                        </td>
                        <td class="p-3 border text-center">
                            <span
                                class="px-2 py-1 rounded text-xs font-bold border {{ $app->status == 'Disetujui' ? 'bg-green-100 text-green-700 border-green-200' : ($app->status == 'Ditolak' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-100 text-blue-700 border-blue-200') }}">
                                {{ $app->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
