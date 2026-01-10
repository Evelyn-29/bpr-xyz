<x-layouts.app :title="'Antrian Upload SLIK'">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Antrian Upload SLIK</h1>
    </x-slot>

    @if (session('success'))
        <x-alert type="success">
            <strong>Information:</strong> {{ session('success') }}
        </x-alert>
    @endif

    @if (session('warning'))
        <x-alert type="warning">
            <strong>Peringatan:</strong> {{ session('warning') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error">
            <strong>Error:</strong> {{ session('error') }}
        </x-alert>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER TABEL + PENCARIAN --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Nasabah</h2>

            {{-- Form Search --}}
            <form method="GET" action="{{ route('app.slik.index') }}" class="flex w-full md:w-auto gap-2">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Tiket / Nama / NIK..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Tombol Reset jika sedang mencari --}}
                @if (request('search'))
                    <a href="{{ route('app.slik.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">No. Pengajuan</th>
                        <th class="px-6 py-4">Nasabah</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Status SLIK</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                {{ $app->no_pengajuan }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $app->nasabahProfile->nama_lengkap ?? $app->user->name }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $app->nasabahProfile->no_ktp ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                Rp {{ number_format($app->jumlah_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($app->slik_path)
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold border border-green-200">
                                        Sudah Upload
                                    </span>
                                    <div class="text-xs mt-1 text-gray-500 font-medium truncate max-w-[150px]"
                                        title="{{ $app->slik_status }}">
                                        {{ $app->slik_status }}
                                    </div>
                                @else
                                    <span
                                        class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold border border-red-200">
                                        Belum Upload
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @can('upload_slik')
                                    <a href="{{ route('app.slik.edit', $app->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition shadow-sm"
                                        title="{{ $app->slik_path ? 'Update' : 'Upload' }} SLIK Nasabah">
                                        
                                        <i class="fa-solid fa-upload"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-search text-gray-400"></i>
                                    </div>
                                    <p>Data tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
    </div>
</x-layouts.app>
