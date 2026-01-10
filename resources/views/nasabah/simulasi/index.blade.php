<x-layouts.nasabah :title="'Simulasi Kredit'">
    <x-slot name="header">
        <h1 class="font-bold">Simulasi Kredit</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="simulasiKredit()">

        {{-- FORM INPUT --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Hitung Angsuran</h3>

                <form @submit.prevent="hitungSimulasi">
                    @csrf

                    {{-- Pilih Fasilitas --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kredit</label>
                        <select x-model="form.facility_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Fasilitas --</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->nama }} (Max
                                    {{ $facility->max_jangka_waktu }} Bln)</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Nominal (UPDATED SUPPORT CURRENCY) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pinjaman</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 font-semibold">Rp</span>
                            <input type="text" x-model="form.amount"
                                @input="form.amount = formatRupiah($event.target.value)" placeholder="0"
                                class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-800">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Masukkan nominal pinjaman.</p>
                    </div>

                    {{-- Input Tenor --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jangka Waktu (Bulan)</label>
                        <input type="number" x-model="form.tenor" placeholder="Contoh: 12"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition flex justify-center items-center">
                        <span x-show="!loading">Hitung Simulasi</span>
                        <span x-show="loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Menghitung...
                        </span>
                    </button>
                </form>

                {{-- Error Message --}}
                <div x-show="errorMessage" class="mt-4 p-3 bg-red-100 text-red-700 text-sm rounded-lg"
                    style="display: none;">
                    <span x-text="errorMessage"></span>
                </div>
            </div>
        </div>

        {{-- HASIL SIMULASI --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md p-6 h-full">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Hasil Simulasi</h3>

                {{-- State Awal --}}
                <div x-show="!result && !loading" class="flex flex-col items-center justify-center h-64 text-gray-400">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p>Silakan isi formulir untuk melihat estimasi angsuran.</p>
                </div>

                {{-- State Hasil --}}
                <div x-show="result" style="display: none;" class="space-y-6">

                    <div class="bg-blue-50 rounded-xl p-6 text-center border border-blue-100">
                        <p class="text-sm text-gray-600 mb-1">Estimasi Angsuran Per Bulan</p>
                        <h2 class="text-4xl font-bold text-blue-700">
                            Rp <span x-text="result?.total_angsuran"></span>
                        </h2>
                        <div
                            class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Bunga <span x-text="result?.bunga_persen"></span> % / bulan (Flat)
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Rincian Pinjaman</h4>
                            <ul class="space-y-3 text-sm">
                                <li class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Fasilitas</span>
                                    <span class="font-medium text-gray-900" x-text="result?.facility_name"></span>
                                </li>
                                <li class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Plafond</span>
                                    <span class="font-medium text-gray-900">Rp <span
                                            x-text="result?.plafond"></span></span>
                                </li>
                                <li class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Tenor</span>
                                    <span class="font-medium text-gray-900"><span x-text="result?.tenor"></span>
                                        Bulan</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Rincian Pembayaran</h4>
                            <ul class="space-y-3 text-sm">
                                <li class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Angsuran Pokok</span>
                                    <span class="font-medium text-gray-900">Rp <span
                                            x-text="result?.angsuran_pokok"></span></span>
                                </li>
                                <li class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Angsuran Bunga</span>
                                    <span class="font-medium text-gray-900">Rp <span
                                            x-text="result?.angsuran_bunga"></span></span>
                                </li>
                                <li class="flex justify-between border-b pb-2 bg-gray-50 px-2 rounded">
                                    <span class="text-gray-800 font-semibold">Total Pembayaran</span>
                                    <span class="font-bold text-gray-900">Rp <span
                                            x-text="result?.total_bayar"></span></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-4 border-t">
                        <p class="text-xs text-gray-500 italic">
                            * Perhitungan ini hanyalah estimasi simulasi kredit dengan metode Bunga Flat.
                            Nilai realisasi dapat berbeda sesuai dengan analisa kredit dan ketentuan yang berlaku.
                        </p>
                    </div>

                    {{-- Tombol Ajukan --}}
                    <div class="mt-6">
                        <a href="{{ route('nasabah.pengajuan.step1') }}"
                            class="block w-full text-center bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition shadow-lg transform hover:-translate-y-1">
                            Ajukan Kredit Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function simulasiKredit() {
                return {
                    form: {
                        facility_id: '',
                        amount: '',
                        tenor: ''
                    },
                    loading: false,
                    result: null,
                    errorMessage: null,

                    // Helper Format Rupiah
                    formatRupiah(value) {
                        let number = value.replace(/[^0-9]/g, '');
                        if (number === '') return '';
                        return new Intl.NumberFormat('id-ID').format(number);
                    },

                    async hitungSimulasi() {
                        this.loading = true;
                        this.errorMessage = null;
                        this.result = null;

                        if (!this.form.facility_id || !this.form.amount || !this.form.tenor) {
                            this.errorMessage = "Mohon lengkapi semua kolom.";
                            this.loading = false;
                            return;
                        }

                        // SANITASI: Hilangkan titik sebelum kirim ke server
                        // Copy object form biar tampilan di input tetap ada titiknya
                        let payload = {
                            ...this.form,
                            amount: this.form.amount.toString().replace(/\./g, '')
                        };

                        try {
                            const response = await fetch("{{ route('nasabah.simulasi.calculate') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify(payload) // Kirim payload yg sudah bersih
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Terjadi kesalahan.');
                            }

                            this.result = data.data;

                        } catch (error) {
                            this.errorMessage = error.message;
                        } finally {
                            this.loading = false;
                        }
                    }
                }
            }
        </script>
    @endpush
</x-layouts.nasabah>
