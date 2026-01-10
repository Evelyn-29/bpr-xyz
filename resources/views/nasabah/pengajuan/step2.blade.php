<x-layouts.nasabah :title="'Pengajuan Kredit'">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-bold">Form Pengajuan Kredit</h1>
            <span class="text-sm text-gray-500 ms-4 pt-1">Step 2 dari 3</span>
        </div>
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

    <section id="pengajuan" class="bg-white rounded-2xl shadow-md p-8 mx-auto" x-data="{
        menikah: '{{ $application->nasabahProfile->status_perkawinan ?? '' }}' === 'Menikah',
        maxJangkaWaktu: null,
        jangkaWaktu: '{{ old('jangka_waktu', $application->jangka_waktu) }}' != '0' ? '{{ old('jangka_waktu', $application->jangka_waktu) }}' : '',
        facilities: {{ Js::from($facilities->map(fn($f) => ['id' => $f->id, 'nama' => $f->nama, 'max' => $f->max_jangka_waktu])) }},
        updateMaxJangka(id) {
            const f = this.facilities.find(f => f.id == id);
            this.maxJangkaWaktu = f ? f.max : null;
        }
    }"
        x-init="updateMaxJangka($refs.facility.value)">
        <h2 class="text-xl font-semibold mb-6 text-gray-800 border-b pb-2">Fasilitas Kredit</h2>

        <form method="POST" action="{{ route('nasabah.pengajuan.step2.post') }}">
            @csrf

            {{-- Fasilitas Kredit --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jenis Fasilitas Kredit --}}
                <div>
                    <label for="credit_facility_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Fasilitas Kredit <span class="text-red-500">*</span>
                    </label>
                    <select x-ref="facility" id="credit_facility_id" name="credit_facility_id"
                        class="border-gray-300 rounded-md w-full" x-on:change="updateMaxJangka($event.target.value)">
                        <option value="">-- Pilih Fasilitas --</option>
                        @foreach ($facilities as $facility)
                            <option value="{{ $facility->id }}"
                                {{ old('credit_facility_id', $application->credit_facility_id) == $facility->id ? 'selected' : '' }}>
                                {{ $facility->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('credit_facility_id')
                        <p class="text-sm text-red-500 mt-1">Pilihan Fasilitas Kredit harus diisi</p>
                    @enderror
                </div>

                <x-text-input name="jumlah_pinjaman" id="jumlah_pinjaman" label="Jumlah Pinjaman (Rp)" type="text"
                    required :value="old('jumlah_pinjaman', $application->jumlah_pinjaman ?: '')" x-data="{
                        formatCurrency(val) {
                            if (!val || val == 0) return '';
                            let number = val.toString().replace(/\D/g, '');
                            return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }
                    }" x-init="$el.value = formatCurrency($el.value)"
                    x-on:input="$el.value = formatCurrency($el.value)" />

                {{-- Jangka Waktu --}}
                <div>
                    <label for="jangka_waktu" class="block text-sm font-medium text-gray-700 mb-1">
                        Jangka Waktu (bulan) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jangka_waktu" id="jangka_waktu"
                        class="w-full border-gray-300 rounded-md" x-model="jangkaWaktu"
                        x-bind:placeholder="maxJangkaWaktu ? `Maksimum ${maxJangkaWaktu} bulan` : 'Masukkan jangka waktu'"
                        x-bind:max="maxJangkaWaktu" required />
                    <template x-if="maxJangkaWaktu">
                        <p class="text-xs text-gray-500 mt-1">
                            Batas maksimal: <span x-text="maxJangkaWaktu"></span> bulan
                        </p>
                    </template>

                    @error('jangka_waktu')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <x-select-input name="sumber_pendapatan" label="Sumber Pendapatan" :options="['Gaji' => 'Gaji', 'Usaha' => 'Usaha']" :value="$application->sumber_pendapatan"
                    required />
                <x-textarea-input name="tujuan_pinjaman" label="Tujuan Penggunaan Pinjaman" required
                    :value="$application->tujuan_pinjaman" />
            </div>

            {{-- Data Pasangan (jika menikah) --}}
            <template x-if="menikah">
                <div class="mt-3 pt-8">
                    <h3 class="text-lg font-semibold mb-6 pb-2 text-gray-800 border-b">Data Pasangan</h3>

                    {{-- Notifikasi kecil bahwa data diambil dari sebelumnya --}}
                    @if ($applicationDetail->exists && $applicationDetail->credit_application_id != $application->id)
                        <div class="mb-4 text-xs text-blue-600 bg-blue-50 p-2 rounded border border-blue-100">
                            <i class="fa-solid fa-info-circle mr-1"></i> Data pasangan diisi otomatis dari pengajuan
                            sebelumnya. Silakan ubah jika ada perubahan.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-text-input name="nama_pasangan" label="Nama Pasangan" :value="old('nama_pasangan', $applicationDetail->nama_pasangan)" required />
                        <x-text-input name="no_ktp_pasangan" label="No KTP Pasangan" :value="old('no_ktp_pasangan', $applicationDetail->no_ktp_pasangan)" required />
                        <x-textarea-input name="alamat_tinggal_pasangan" label="Alamat Tinggal Pasangan" required
                            :value="old('alamat_tinggal_pasangan', $applicationDetail->alamat_tinggal_pasangan)" />
                        <x-textarea-input name="alamat_ktp_pasangan" label="Alamat KTP Pasangan" required
                            :value="old('alamat_ktp_pasangan', $applicationDetail->alamat_ktp_pasangan)" />
                        <x-text-input name="no_hp_pasangan" label="Nomor HP Pasangan" :value="old('no_hp_pasangan', $applicationDetail->no_hp_pasangan)" required />
                        <x-text-input name="email_pasangan" label="Email Pasangan" :value="old('email_pasangan', $applicationDetail->email_pasangan)" required />
                        <x-text-input name="pekerjaan_pasangan" label="Pekerjaan Pasangan" :value="old('pekerjaan_pasangan', $applicationDetail->pekerjaan_pasangan)"
                            required />
                    </div>
                </div>
            </template>

            {{-- Data Penjamin --}}
            <div class="mt-3 pt-8">
                <h3 class="text-lg font-semibold mb-6 pb-2 text-gray-800 border-b">Data Penjamin</h3>

                {{-- Notifikasi kecil --}}
                @if ($applicationDetail->exists && $applicationDetail->credit_application_id != $application->id)
                    <div class="mb-4 text-xs text-blue-600 bg-blue-50 p-2 rounded border border-blue-100">
                        <i class="fa-solid fa-info-circle mr-1"></i> Data penjamin diisi otomatis dari pengajuan
                        sebelumnya.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-text-input name="nama_penjamin" label="Nama Penjamin" :value="old('nama_penjamin', $applicationDetail->nama_penjamin)" required />
                    <x-text-input name="no_ktp_penjamin" label="No KTP Penjamin" :value="old('no_ktp_penjamin', $applicationDetail->no_ktp_penjamin)" required />
                    <x-select-input name="hubungan_penjamin" label="Hubungan dengan Pemohon" :options="[
                        'Orang Tua' => 'Orang Tua',
                        'Saudara' => 'Saudara',
                        'Teman' => 'Teman',
                    ]"
                        :value="old('hubungan_penjamin', $applicationDetail->hubungan_penjamin)" required />
                    <x-text-input name="no_hp_penjamin" label="Nomor HP Penjamin" :value="old('no_hp_penjamin', $applicationDetail->no_hp_penjamin)" required />
                    <x-text-input name="email_penjamin" label="Email Penjamin" type="email" :value="old('email_penjamin', $applicationDetail->email_penjamin)"
                        required />
                    <x-textarea-input name="alamat_penjamin" label="Alamat Penjamin" required :value="old('alamat_penjamin', $applicationDetail->alamat_penjamin)" />
                </div>
            </div>

            {{-- Navigasi --}}
            <div class="pt-8 mt-8 border-t text-right">
                <a href="{{ route('nasabah.pengajuan.back.step1') }}"
                    class="bg-gray-200 text-gray-800 px-6 py-3 rounded-xl hover:bg-gray-300 transition">
                    ← Kembali
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-6 py-3 ms-2 rounded-xl hover:bg-blue-700 transition">
                    Lanjut →
                </button>
            </div>
        </form>
    </section>

    {{-- Notifikasi NPWP --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const jumlahInput = document.querySelector('input[name="jumlah_pinjaman"]');
            const npwpNotice = document.createElement('p');
            npwpNotice.className = "text-xs text-blue-600 mt-2 hidden";
            npwpNotice.innerText =
                "* Pastikan No. NPWP diisi di formulir sebelumnya, untuk jumlah pinjaman lebih dari Rp50.000.000.";
            jumlahInput.parentNode.appendChild(npwpNotice);

            // Fungsi untuk memformat saat pertama kali load
            if (jumlahInput.value) {
                jumlahInput.value = jumlahInput.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            jumlahInput.addEventListener("input", function() {
                // Ambil angka aslinya saja untuk pengecekan NPWP
                const rawValue = parseFloat(this.value.replace(/\./g, "")) || 0;

                if (rawValue > 50000000) {
                    npwpNotice.classList.remove('hidden');
                } else {
                    npwpNotice.classList.add('hidden');
                }
            });
        });
    </script>
</x-layouts.nasabah>
