<x-layouts.app :title="'Laporan Monitoring Angsuran'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Laporan Monitoring Angsuran</h1>
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
            <h2 class="text-xl font-bold mt-2 underline">LAPORAN MONITORING ANGSURAN</h2>
        </div>

        <table class="w-full text-sm text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300 print:bg-gray-200">
                    <th class="p-3 border">No. PK</th>
                    <th class="p-3 border">Nasabah</th>
                    <th class="p-3 border text-right">Plafond</th>
                    <th class="p-3 border text-center">Progress</th>
                    <th class="p-3 border text-right">Total Kewajiban</th>
                    <th class="p-3 border text-right">Total Terbayar</th>
                    <th class="p-3 border text-right">Sisa Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($credits as $c)
                    @php $sisa = $c->total_tagihan - $c->total_terbayar; @endphp
                    <tr class="border-b">
                        <td class="p-3 border font-mono">{{ $c->no_perjanjian_kredit }}</td>
                        <td class="p-3 border font-bold">{{ $c->nasabahProfile->nama_lengkap }}</td>
                        <td class="p-3 border text-right">Rp {{ number_format($c->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td class="p-3 border text-center">{{ $c->sudah_bayar }} / {{ $c->total_angsuran }} Bulan</td>
                        <td class="p-3 border text-right">Rp {{ number_format($c->total_tagihan, 0, ',', '.') }}</td>
                        <td class="p-3 border text-right text-green-600">Rp
                            {{ number_format($c->total_terbayar, 0, ',', '.') }}</td>
                        <td class="p-3 border text-right text-red-600 font-bold">Rp
                            {{ number_format($sisa, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
