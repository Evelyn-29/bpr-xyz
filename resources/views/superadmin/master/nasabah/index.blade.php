<x-layouts.app title="Data Nasabah">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Master Data Nasabah</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER FILTER --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Daftar Nasabah</h2>
                <p class="text-xs text-gray-500 mt-1">Total {{ $nasabahs->total() }} nasabah terdaftar.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                {{-- SEARCH FORM --}}
                <form action="{{ route('app.nasabah.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Nama / KTP / CIF..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                </form>

                {{-- TOMBOL TAMBAH SUDAH DIHAPUS --}}
            </div>
        </div>

        {{-- TABLE (Tetap Sama) --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Identitas (KTP)</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Alamat Domisili</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($nasabahs as $nasabah)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $nasabah->nama_lengkap }}</div>
                                <div class="text-xs text-blue-600 font-mono mt-1">
                                    {{ $nasabah->kode_nasabah ?? 'No CIF' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $nasabah->no_ktp }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $nasabah->jenis_kelamin }} &bull; {{ $nasabah->status_perkawinan }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fa-brands fa-whatsapp text-green-500"></i> {{ $nasabah->no_hp }}
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <i class="fa-regular fa-envelope text-gray-400"></i> {{ $nasabah->email }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs max-w-[200px] truncate" title="{{ $nasabah->alamat_tinggal }}">
                                    {{ $nasabah->alamat_tinggal }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('app.nasabah.show', $nasabah->id) }}"
                                        class="text-gray-500 hover:text-blue-600" title="Detail Profil">
                                        <i class="fa-solid fa-id-card-clip fa-lg"></i>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    @can('edit_nasabah')
                                        <a href="{{ route('app.nasabah.edit', $nasabah->id) }}"
                                            class="text-gray-500 hover:text-green-600" title="Edit Data Krusial">
                                            <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                        </a>
                                    @endcan

                                    {{-- Tombol Hapus --}}
                                    @can('delete_nasabah')
                                        <form action="{{ route('app.nasabah.destroy', $nasabah->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Hapus nasabah ini? Akun login dan data profil akan dihapus.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600"
                                                title="Hapus Data">
                                                <i class="fa-solid fa-trash-can fa-lg"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Data nasabah tidak
                                ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $nasabahs->links() }}</div>
    </div>
</x-layouts.app>
