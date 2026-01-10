<x-layouts.app title="Master Fasilitas Kredit">
    {{-- TITLE HALAMAN --}}
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Master Data Produk Kredit</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- HEADER TABEL: JUDUL + PENCARIAN & TOMBOL --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            
            {{-- Kiri: Judul Seksi --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Daftar Produk</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola fasilitas kredit dan skema bunga.</p>
            </div>

            {{-- Kanan: Form Search & Tombol Aksi --}}
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                
                {{-- 1. FORM SEARCH --}}
                <form action="{{ route('app.products.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Kode / Nama..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                    
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>

                    {{-- Tombol Reset Search (Kecil di dalam input) --}}
                    @if(request('search'))
                        <a href="{{ route('app.products.index') }}" 
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition"
                           title="Hapus Pencarian">
                            <i class="fa-solid fa-times-circle"></i>
                        </a>
                    @endif
                </form>

                {{-- 2. TOMBOL TAMBAH DATA --}}
                @can('create_master_produk')
                <a href="{{ route('app.products.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2 shadow-sm transition transform active:scale-95 text-sm font-medium whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> 
                    Tambah Produk
                </a>
                @endcan
            </div>
        </div>

        {{-- TABEL RESPONSIVE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Kode Produk</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Max Tenor</th>
                        <th class="px-6 py-4">Skema Bunga</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $item)
                    <tr class="hover:bg-gray-50 transition">
                        
                        {{-- Kode Produk --}}
                        <td class="px-6 py-4 font-mono font-bold text-blue-600 text-xs">
                            {{ $item->kode }}
                        </td>

                        {{-- Nama Produk --}}
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $item->nama }}</div>
                            <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ $item->deskripsi ?? '-' }}</div>
                        </td>

                        {{-- Max Tenor --}}
                        <td class="px-6 py-4">
                            <span class="text-gray-800 font-medium">{{ $item->max_jangka_waktu }}</span> <span class="text-xs text-gray-500">Bulan</span>
                        </td>

                        {{-- Jumlah Tier (Skema) --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-medium border border-gray-200">
                                <i class="fa-solid fa-layer-group text-gray-400"></i>
                                {{ $item->tiers_count }} Tiering
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($item->aktif)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Non-Aktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @can('edit_master_produk')
                                <a href="{{ route('app.products.edit', $item->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition shadow-sm"
                                   title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endcan
                                
                                @can('delete_master_produk')
                                <form action="{{ route('app.products.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600 transition shadow-sm"
                                            title="Hapus Data">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-box-open text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-medium">Belum ada data produk.</p>
                                @if(request('search'))
                                    <p class="text-xs mt-1">Coba ubah kata kunci pencarian atau <a href="{{ route('app.products.index') }}" class="text-blue-600 hover:underline">reset filter</a>.</p>
                                @else
                                    <p class="text-xs mt-1">Silakan tambah produk baru untuk memulai.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $products->links() }}
        </div>
    </div>
</x-layouts.app>