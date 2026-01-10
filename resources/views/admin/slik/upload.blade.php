<x-layouts.app :title="'Upload Hasil SLIK'">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.slik.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Upload Hasil SLIK</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            {{-- Info Nasabah Singkat --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <h3 class="font-bold text-gray-800 mb-2">{{ $application->no_pengajuan }}</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nama Nasabah</p>
                        <p class="font-medium">{{ $application->nasabahProfile->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">NIK / KTP</p>
                        <p class="font-medium">{{ $application->nasabahProfile->no_ktp }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('app.slik.update', $application->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Input File --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Hasil SLIK (PDF)</label>
                    <input type="file" name="slik_file" accept=".pdf" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Maksimal ukuran file 5MB.</p>
                    @error('slik_file')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Select Status Kolektibilitas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kesimpulan Status
                        (Kolektibilitas)</label>
                    <select name="slik_status" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Status --</option>
                        <option value="KOL 1 - Lancar">KOL 1 - Lancar</option>
                        <option value="KOL 2 - Dalam Perhatian Khusus">KOL 2 - Dalam Perhatian Khusus</option>
                        <option value="KOL 3 - Kurang Lancar">KOL 3 - Kurang Lancar</option>
                        <option value="KOL 4 - Diragukan">KOL 4 - Diragukan</option>
                        <option value="KOL 5 - Macet">KOL 5 - Macet</option>
                        <option value="Tidak Ditemukan">Data Tidak Ditemukan (Bersih)</option>
                    </select>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                    <textarea name="slik_notes" rows="3" placeholder="Contoh: Ada tunggakan paylater, kartu kredit lancar."
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                        Simpan Data SLIK
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
