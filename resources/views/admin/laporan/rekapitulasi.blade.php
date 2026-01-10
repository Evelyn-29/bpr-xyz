<x-layouts.app :title="'Laporan Rekapitulasi'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Laporan Rekapitulasi</h1>
    </x-slot>
    <div class="bg-white p-6 rounded-2xl shadow-sm no-print flex gap-4 items-center mb-5">
        <form method="GET" class="flex gap-4 items-center">
            <label class="text-sm font-bold">Tahun:</label>
            <select name="year" onchange="this.form.submit()"
                class="appearance-none pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-600 cursor-pointer w-full md:w-48">
                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank"
            class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold ml-auto flex items-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
            <thead class="text-center">
                {{-- Baris Header 1 --}}
                <tr class="bg-gray-100 border-b border-gray-300 print:bg-gray-200">
                    <th class="p-2 border" rowspan="2" style="width: 15%">JENIS PINJAMAN</th>
                    <th class="p-2 border" rowspan="2" style="width: 12%">JUMLAH</th>
                    <th class="p-2 border" colspan="2">ANGSURAN</th>
                    <th class="p-2 border" rowspan="2" style="width: 12%">TOTAL ANGSURAN</th>
                    <th class="p-2 border" colspan="3">TUNGGAKAN</th>
                    <th class="p-2 border" rowspan="2" style="width: 12%">TOTAL TUNGGAKAN</th>
                </tr>
                {{-- Baris Header 2 --}}
                <tr class="bg-gray-100 border-b border-gray-300 print:bg-gray-200">
                    <th class="p-2 border">POKOK</th>
                    <th class="p-2 border">BUNGA</th>
                    <th class="p-2 border">POKOK</th>
                    <th class="p-2 border">BUNGA</th>
                    <th class="p-2 border">DENDA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Inisialisasi Grand Total
                    $t_jumlah = 0;
                    $t_ang_pokok = 0;
                    $t_ang_bunga = 0;
                    $t_ang_total = 0;
                    $t_tung_pokok = 0;
                    $t_tung_bunga = 0;
                    $t_tung_denda = 0;
                    $t_tung_total = 0;
                @endphp

                @foreach ($data as $row)
                    @php
                        // Akumulasi Grand Total
                        $t_jumlah += $row->rekap->jumlah;
                        $t_ang_pokok += $row->rekap->angsuran_pokok;
                        $t_ang_bunga += $row->rekap->angsuran_bunga;
                        $t_ang_total += $row->rekap->total_angsuran;
                        $t_tung_pokok += $row->rekap->tunggakan_pokok;
                        $t_tung_bunga += $row->rekap->tunggakan_bunga;
                        $t_tung_denda += $row->rekap->tunggakan_denda;
                        $t_tung_total += $row->rekap->total_tunggakan;
                    @endphp
                    <tr class="border-b">
                        <td class="p-2 border">{{ strtoupper($row->nama) }}</td>
                        <td class="p-2 border text-right">{{ number_format($row->rekap->jumlah, 0, ',', '.') }}</td>

                        {{-- Angsuran --}}
                        <td class="p-2 border text-right">{{ number_format($row->rekap->angsuran_pokok, 0, ',', '.') }}
                        </td>
                        <td class="p-2 border text-right">{{ number_format($row->rekap->angsuran_bunga, 0, ',', '.') }}
                        </td>
                        <td class="p-2 border text-right">{{ number_format($row->rekap->total_angsuran, 0, ',', '.') }}
                        </td>

                        {{-- Tunggakan --}}
                        <td class="p-2 border text-right">{{ number_format($row->rekap->tunggakan_pokok, 0, ',', '.') }}
                        </td>
                        <td class="p-2 border text-right">
                            {{ number_format($row->rekap->tunggakan_bunga, 0, ',', '.') }}</td>
                        <td class="p-2 border text-right">
                            {{ number_format($row->rekap->tunggakan_denda, 0, ',', '.') }}</td>
                        <td class="p-2 border text-right">
                            {{ number_format($row->rekap->total_tunggakan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-yellow border-b">
                    <td class="p-2 border font-bold">TOTAL</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_jumlah, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_ang_pokok, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_ang_bunga, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_ang_total, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_tung_pokok, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_tung_bunga, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_tung_denda, 0, ',', '.') }}</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($t_tung_total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-layouts.app>
