@php
    $button_link = 'app.angsuran.show';
    if (Auth::user()->hasRole('Direktur')) {
        $button_text = 'Detail';
    } elseif (Auth::user()->hasRole('Manager')) {
        $button_text = 'Detail / Reversal';
    } else {
        $button_text = 'Detail / Bayar';
    }
@endphp
<x-layouts.app :title="'Monitoring Angsuran'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Kredit Aktif & Angsuran</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- HEADER TABEL + FILTER --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Angsuran</h2>

            {{-- FORM FILTER & SEARCH --}}
            <form method="GET" action="{{ route('app.angsuran.index') }}"
                class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                {{-- 2. Input Search --}}
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari No. Pengajuan / Nama..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>


                @if (request('search'))
                    <a href="{{ route('app.angsuran.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Plafond</th>
                        <th class="px-6 py-4">Tenor</th>
                        <th class="px-6 py-4">Progress</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($credits as $credit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $credit->nasabahProfile->nama_lengkap }}</div>
                                <div
                                    class="text-xs text-green-700 font-mono font-bold bg-green-50 inline-block px-1 rounded border border-green-200">
                                    {{ $credit->no_perjanjian_kredit }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1">Ref: {{ $credit->no_pengajuan }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold">Rp
                                {{ number_format($credit->recommended_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $credit->recommended_tenor }} Bulan</td>
                            <td class="px-6 py-4 w-1/4">
                                @php
                                    $totalBulan = $credit->recommended_tenor;
                                    $sudahBayar = $credit->sudah_bayar;

                                    if ($totalBulan > 0) {
                                        $persen = ($sudahBayar / $totalBulan) * 100;
                                    } else {
                                        $persen = 0;
                                    }

                                    $barColor = 'bg-orange-600';
                                    if ($persen >= 100) {
                                        $barColor = 'bg-green-500';
                                    } elseif ($persen >= 50) {
                                        $barColor = 'bg-blue-500';
                                    }
                                @endphp

                                <div class="flex justify-between mb-1">
                                    <span
                                        class="text-xs font-medium {{ $persen >= 100 ? 'text-green-700' : 'text-blue-700' }}">
                                        {{ $sudahBayar }} / {{ $totalBulan }} Bulan
                                    </span>
                                    <span class="text-xs font-medium text-gray-500">{{ round($persen) }}%</span>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-200">
                                    <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500 ease-out"
                                        style="width: {{ $persen }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('app.angsuran.show', $credit->id) }}"
                                    class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-700">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $credits->links() }}</div>
    </div>
</x-layouts.app>
