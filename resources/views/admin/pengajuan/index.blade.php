<x-layouts.app :title="'Daftar Pengajuan Kredit'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Data Pengajuan Kredit</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER TABEL + FILTER --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Masuk</h2>

            {{-- FORM FILTER & SEARCH --}}
            <form method="GET" action="{{ route('app.pengajuan.index') }}"
                class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

                {{-- 1. Dropdown Filter Status --}}
                <div class="relative">
                    <select name="status" onchange="this.form.submit()"
                        class="appearance-none pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-600 cursor-pointer w-full md:w-48">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Verifikasi"
                            {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi
                        </option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui
                        </option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

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


                @if (request('status') || request('search'))
                    <a href="{{ route('app.pengajuan.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Responsive --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">No. Pengajuan</th> {{-- Update Header --}}
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Fasilitas & Tenor</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Tanggal Submit</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Tampilkan No Pengajuan Unik --}}
                            <td class="px-6 py-4 font-mono text-blue-600 font-bold text-xs">
                                {{ $app->no_pengajuan ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $app->nasabahProfile->nama_lengkap ?? $app->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $app->nasabahProfile->no_hp ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900">{{ $app->creditFacility->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $app->jangka_waktu }} Bulan</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                Rp {{ number_format($app->jumlah_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $app->submitted_at ? $app->submitted_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'Menunggu Verifikasi' => 'bg-blue-100 text-blue-700',
                                        'Disetujui' => 'bg-green-100 text-green-700',
                                        'Ditolak' => 'bg-red-100 text-red-700',
                                        'Lunas' => 'bg-gray-800 text-white',
                                    ];
                                    $class = $colors[$app->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $class }}">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @can('detail_pengajuan')
                                    <a href="{{ route('app.pengajuan.show', $app->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition shadow-sm"
                                        title="Lihat Detail Pengajuan">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-regular fa-folder-open text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="font-medium">Data tidak ditemukan.</p>
                                    <p class="text-xs mt-1">Coba ubah filter status atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
    </div>
</x-layouts.app>
