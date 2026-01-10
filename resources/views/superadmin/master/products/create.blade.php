<x-layouts.app title="Tambah Produk Kredit">
    <div class="mb-6">
        <a href="{{ route('app.products.index') }}" class="text-gray-500 hover:text-gray-700"><i
                class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    {{-- ALERT ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">
                        Ups! Ada beberapa masalah pada inputan Anda:
                    </h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ALERT ERROR SESSION (Dari try-catch controller) --}}
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm text-red-700 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('app.products.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: INFO DASAR --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="font-bold text-lg mb-4 text-gray-800">Informasi Produk</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Produk</label>
                            <input type="text" name="kode" value="{{ old('kode') }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500" placeholder="">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="nama" value="{{ old('nama') }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500" placeholder="">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Max Tenor (Bulan)</label>
                                <input type="number" name="max_jangka_waktu" value="{{ old('max_jangka_waktu', 60) }}"
                                    class="w-full border-gray-300 rounded-lg">
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="aktif" value="1" checked
                                        class="form-checkbox text-blue-600 rounded">
                                    <span class="text-sm font-bold text-gray-700">Status Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: SKEMA BUNGA (DYNAMIC) --}}
            <div class="lg:col-span-2">
                {{-- AREA DYNAMIC TABLE (FULL ALPINE JS) --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100" x-data="{
                    rows: {{ isset($product)
                        ? $product->tiers->map(
                            fn($t) => [
                                // Load data edit: Format ke tampilan Indo (1.000,00)
                                'min_plafond' => number_format($t->min_plafond, 0, ',', '.'),
                                'max_plafond' => $t->max_plafond ? number_format($t->max_plafond, 0, ',', '.') : '',
                                'bunga' => $t->bunga,
                            ],
                        )
                        : json_encode([['min_plafond' => '', 'max_plafond' => '', 'bunga' => '']]) }},
                
                    // FORMATTER RUPIAH (Support Desimal Koma)
                    formatRupiah(value) {
                        // 1. Simpan posisi kursor (opsional, biar UX enak)
                        // 2. Izinkan angka dan satu koma
                        let raw = value.replace(/[^0-9,]/g, '');
                
                        // 3. Pisahkan bagian Ribuan dan Desimal
                        let parts = raw.split(',');
                        let integerPart = parts[0];
                        let decimalPart = parts.length > 1 ? ',' + parts[1].substring(0, 2) : ''; // Max 2 desimal
                
                        // 4. Format Ribuan (Hapus 0 di depan jika ada)
                        if (integerPart.length > 1 && integerPart.startsWith('0')) {
                            integerPart = integerPart.substring(1);
                        }
                
                        // 5. Beri titik
                        integerPart = new Intl.NumberFormat('id-ID').format(integerPart || 0);
                
                        // 6. Gabungkan (Handle kasus user hapus semua jadi kosong)
                        if (value === '') return '';
                
                        // Jika user baru ketik koma (misal '100,'), biarkan komanya
                        if (raw.endsWith(',')) {
                            return integerPart + ',';
                        }
                
                        return integerPart + decimalPart;
                    }
                }">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-bold text-lg text-gray-800">Skema Bunga & Plafond</h2>
                        <button type="button" @click="rows.push({ min_plafond: '', max_plafond: '', bunga: '' })"
                            class="text-sm text-blue-600 hover:text-blue-800 font-bold">
                            <i class="fa-solid fa-plus-circle"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-50 text-blue-800 uppercase text-xs">
                                <tr>
                                    <th class="p-3 w-1/3">Min Plafond (Rp)</th>
                                    <th class="p-3 w-1/3">Max Plafond (Rp)</th>
                                    <th class="p-3 w-24">Bunga %</th>
                                    <th class="p-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(row, index) in rows" :key="index">
                                    <tr>
                                        {{-- INPUT MIN PLAFOND --}}
                                        <td class="p-2">
                                            <div class="relative">
                                                <span class="absolute left-3 top-2 text-gray-400">Rp</span>
                                                {{-- x-model menyambungkan data, @input memformat saat mengetik --}}
                                                <input type="text" :name="'tiers[' + index + '][min_plafond]'"
                                                    x-model="row.min_plafond"
                                                    @input="row.min_plafond = formatRupiah($event.target.value)"
                                                    class="w-full pl-9 border-gray-300 rounded text-sm font-bold text-gray-700"
                                                    placeholder="0" required>
                                            </div>
                                        </td>

                                        {{-- INPUT MAX PLAFOND --}}
                                        <td class="p-2">
                                            <div class="relative">
                                                <span class="absolute left-3 top-2 text-gray-400">Rp</span>
                                                <input type="text" :name="'tiers[' + index + '][max_plafond]'"
                                                    x-model="row.max_plafond"
                                                    @input="row.max_plafond = formatRupiah($event.target.value)"
                                                    class="w-full pl-9 border-gray-300 rounded text-sm font-bold text-gray-700 placeholder-blue-300"
                                                    placeholder="Kosongkan jika unlimited">
                                            </div>
                                        </td>

                                        {{-- INPUT BUNGA --}}
                                        <td class="p-2">
                                            <input type="number" step="0.01" :name="'tiers[' + index + '][bunga]'"
                                                x-model="row.bunga"
                                                class="w-full border-gray-300 rounded text-sm text-center"
                                                placeholder="%" required>
                                        </td>

                                        {{-- TOMBOL HAPUS --}}
                                        <td class="p-2 text-center">
                                            <button type="button"
                                                @click="rows.length > 1 ? rows.splice(index, 1) : alert('Minimal 1 tier!')"
                                                class="text-red-500 hover:text-red-700">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg">
                        Simpan Produk
                    </button>
                </div>
            </div>

        </div>
    </form>
</x-layouts.app>
