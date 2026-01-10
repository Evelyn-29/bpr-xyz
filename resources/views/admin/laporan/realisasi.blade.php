<x-layouts.app :title="'Laporan Realisasi Pinjaman'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Laporan Realisasi Pinjaman</h1>
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
            <h2 class="text-xl font-bold mt-2 underline">LAPORAN HASIL ANALISIS KREDIT</h2>
        </div>

        <table class="w-full text-xs text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300 print:bg-gray-200">
                    <th class="p-2 border">TANGGAL REALISASI</th>
                    <th class="p-2 border">NO PK</th>
                    <th class="p-2 border">NAMA NASABAH</th>
                    <th class="p-2 border">JANGKA WAKTU</th>
                    <th class="p-2 border">PLAFOND</th>
                    <th class="p-2 border">STATUS KREDIT</th>
                    <th class="p-2 border">PELUNASAN</th> {{-- Diisi 0 jika baru cair, atau data angsuran jika ada --}}
                    <th class="p-2 border">NETTING CAIR</th>
                    <th class="p-2 border">NETTING CAIR NASABAH</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Inisialisasi Grand Total
                    $grandTotalPlafond = 0;
                    $grandTotalPelunasan = 0;
                    $grandTotalNetting = 0;
                    $grandTotalNettingNasabah = 0;
                @endphp

                @foreach ($facilities as $facility)
                    {{-- Hanya tampilkan fasilitas yang ada datanya --}}
                    @if ($facility->creditApplications->count() > 0)
                        {{-- HEADER GROUP (NAMA FASILITAS) --}}
                        <tr class="group-header">
                            <td class="p-2 border font-bold" colspan="9">{{ strtoupper($facility->nama) }}</td>
                        </tr>

                        @php
                            // Inisialisasi Sub Total per Fasilitas
                            $subPlafond = 0;
                            $subPelunasan = 0;
                            $subNetting = 0;
                            $subNettingNasabah = 0;
                        @endphp

                        @foreach ($facility->creditApplications as $app)
                            @php
                                // PERHITUNGAN RUMUS
                                $plafond = $app->jumlah_pinjaman;

                                // Netting Cair = Plafond (Sesuai request)
                                $nettingCair = $plafond;

                                // Biaya Admin & Provisi 1.5%
                                $biaya = $plafond * 0.015;

                                // Netting Cair Nasabah = Plafond - 1.5%
                                $nettingNasabah = $plafond - $biaya;

                                // Data Pelunasan (Opsional: Ambil dari total yang sudah dibayar nasabah)
                                // Jika ini laporan pencairan murni, biasanya pelunasan 0.
                                // Tapi saya ambilkan dari data pembayaran agar informatif.
                                $pelunasan = $app->payments()->where('status_pembayaran', 'Paid')->sum('jumlah_bayar');

                                // Akumulasi Sub Total
                                $subPlafond += $plafond;
                                $subPelunasan += $pelunasan;
                                $subNetting += $nettingCair;
                                $subNettingNasabah += $nettingNasabah;
                            @endphp

                            <tr class="border-b">
                                <td class="p-2 border">{{ $app->tgl_akad ? $app->tgl_akad->format('d/m/Y') : '-' }}
                                </td>
                                <td class="p-2 border">{{ $app->no_perjanjian_kredit }}</td>
                                <td class="p-2 border">{{ $app->nasabahProfile->nama_lengkap }}</td>
                                <td class="p-2 border">{{ $app->jangka_waktu }} Bln</td>
                                <td class="p-2 border text-right">{{ number_format($plafond, 0, ',', '.') }}</td>
                                <td class="p-2 border text-center">{{ strtoupper($app->status) }}</td>
                                <td class="p-2 border text-right">{{ number_format($pelunasan, 0, ',', '.') }}</td>
                                <td class="p-2 border text-right" class="text-right">
                                    {{ number_format($nettingCair, 0, ',', '.') }}</td>
                                <td class="p-2 border text-right">{{ number_format($nettingNasabah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- BARIS SUB TOTAL --}}
                        <tr class="bg-subtotal">
                            <td class="p-2 border font-bold" colspan="4">SUB TOTAL {{ strtoupper($facility->nama) }}
                            </td>
                            <td class="p-2 border text-right font-bold" class="text-right">
                                {{ number_format($subPlafond, 0, ',', '.') }}</td>
                            <td class="p-2 border"></td>
                            <td class="p-2 border text-right font-bold">{{ number_format($subPelunasan, 0, ',', '.') }}
                            </td>
                            <td class="p-2 border text-right font-bold">{{ number_format($subNetting, 0, ',', '.') }}
                            </td>
                            <td class="p-2 border text-right font-bold">
                                {{ number_format($subNettingNasabah, 0, ',', '.') }}</td>
                        </tr>

                        @php
                            // Akumulasi ke Grand Total
                            $grandTotalPlafond += $subPlafond;
                            $grandTotalPelunasan += $subPelunasan;
                            $grandTotalNetting += $subNetting;
                            $grandTotalNettingNasabah += $subNettingNasabah;
                        @endphp
                    @endif
                @endforeach
            </tbody>

            {{-- FOOTER GRAND TOTAL --}}
            <tfoot>
                <tr class="bg-total">
                    <td class="p-2 border font-bold" colspan="4">TOTAL KESELURUHAN</td>
                    <td class="p-2 border text-right font-bold">{{ number_format($grandTotalPlafond, 0, ',', '.') }}
                    </td>
                    <td class="p-2 border"></td>
                    <td class="p-2 border text-right font-bold">{{ number_format($grandTotalPelunasan, 0, ',', '.') }}
                    </td>
                    <td class="p-2 border text-right font-bold">{{ number_format($grandTotalNetting, 0, ',', '.') }}
                    </td>
                    <td class="p-2 border text-right font-bold">
                        {{ number_format($grandTotalNettingNasabah, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-layouts.app>
