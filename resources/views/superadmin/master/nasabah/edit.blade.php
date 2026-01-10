<x-layouts.app :title="'Edit Nasabah: ' . $nasabah->nama_lengkap">
    <div class="max-w-3xl mx-auto">
        
        {{-- BACK BUTTON --}}
        <div class="mb-6">
            <a href="{{ route('app.nasabah.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ALERT INFO --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fa-solid fa-circle-info text-blue-500"></i></div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Anda berada dalam <strong>Mode Edit Terbatas</strong>. Hanya data identitas krusial yang dapat diubah oleh Admin. Data kontak dan alamat dikelola oleh Nasabah atau flow pengajuan.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-yellow-50">
                <h2 class="text-lg font-bold text-gray-800">Edit Data Identitas</h2>
                <p class="text-xs text-gray-500">{{ $nasabah->kode_nasabah }}</p>
            </div>

            <form action="{{ route('app.nasabah.update', $nasabah->id) }}" method="POST" class="p-6 space-y-6">
                @csrf @method('PUT')
                
                {{-- 1. NAMA LENGKAP --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $nasabah->nama_lengkap) }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 font-bold text-gray-800">
                    @error('nama_lengkap') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                {{-- 2. NO KTP --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nomor KTP (NIK)</label>
                    <input type="number" name="no_ktp" value="{{ old('no_ktp', $nasabah->no_ktp) }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 font-mono">
                    @error('no_ktp') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- 3. NAMA IBU KANDUNG --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ibu Kandung</label>
                        <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung', $nasabah->nama_ibu_kandung) }}" required
                            class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                        @error('nama_ibu_kandung') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- 4. JENIS KELAMIN --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 bg-white">
                            <option value="Laki-laki" {{ $nasabah->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $nasabah->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- READ ONLY INFO (Hanya untuk display, tidak dikirim form) --}}
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-4">Informasi Kontak (Read Only)</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block text-gray-500 text-xs">No. HP / WhatsApp</span>
                            <span class="font-medium text-gray-800">{{ $nasabah->no_hp }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs">Email Terdaftar</span>
                            <span class="font-medium text-gray-800">{{ $nasabah->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('app.nasabah.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>