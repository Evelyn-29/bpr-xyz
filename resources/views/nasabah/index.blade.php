<x-layouts.nasabah>
    <x-slot name="header">
        <h1 class="font-bold">Welcome, {{ Auth::user()->name }}!</h1>
    </x-slot>

    {{-- Konten utama --}}
    <section id="home" class="mb-8">
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

        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Produk Kredit Unggulan Kami</h2>
        <p class="text-gray-600 mb-8">Produk Kredit bertujuan memberikan solusi bagi anda yang membutuhkan pinjaman
            baik untuk kebutuhan konsumtif, investasi ataupun modal kerja dengan syarat yang mudah dan proses yang
            cepat.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-center bg-blue-100 w-16 h-16 rounded-full mb-4">
                    <i class="fa-solid fa-briefcase text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Kredit Modal Kerja (KMK)</h3>
                <p class="text-gray-600">Kredit untuk usaha ekonomi produktif di sektor Usaha Mikro Kecil dan Menengah
                </p>
                <ul class="list-disc list-inside text-gray-600 ml-4">
                    <li>Pembelian Bahan Baku</li>
                    <li>Pengembangan Usaha</li>
                    <li>Biaya Operasional</li>
                </ul>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-center bg-green-100 w-16 h-16 rounded-full mb-4">
                    <i class="fa-solid fa-home text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Kredit Multi Guna (KMG)</h3>
                <p class="text-gray-600">Kredit yang diberikan kepada anggota masyarakat untuk berbagai kebutuhan
                    konsumtif</p>
                <ul class="list-disc list-inside text-gray-600 ml-4">
                    <li>Biaya Pendidikan</li>
                    <li>Biaya Renovasi Rumah</li>
                    <li>Biaya Pernikahan</li>
                    <li>Biaya Konsumtif Lainnya</li>
                </ul>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-center bg-yellow-100 w-16 h-16 rounded-full mb-4">
                    <i class="fa-solid fa-chart-line text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Kredit Investasi (KI)</h3>
                <p class="text-gray-600">Untuk membiayai kebutuhan barang modal dalam rangka rehabilitasi, modernisasi,
                    perluasan, pendirian proyek baru dan atau kebutuhan khusus terkait investasi</p>
                <ul class="list-disc list-inside text-gray-600 ml-4">
                    <li>Pembelian Aset Usaha</li>
                    <li>Pembelian Mesin Usaha</li>
                    <li>Pembelian Properti Usaha</li>
                </ul>
            </div>
        </div>
        <div class="mt-8 bg-white rounded-2xl shadow-md p-8 hover:shadow-xl transition-all">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Syarat dan Ketentuan Umum Pengajuan Pinjaman Kredit :
            </h3>
            <p class="text-gray-600 mb-4">
                *Persyaratan Dokumen WAJIB dilampirkan aslinya disaat pengikatan kredit
            </p>
            <h4 class="font-semibold text-gray-800 mb-3">Karyawan</h4>
            <ul class="list-disc list-inside text-gray-600 ml-4 mb-6">
                <li>Foto Copy KTP Suami Istri</li>
                <li>Foto Copy Kartu Keluarga dan Surat Nikah</li>
                <li>Rumah Milik Sendiri (PBB & Rek Listrik / Telp / Pam) / Jika Rumah Kontrak (Surat Perjanjian Sewa
                    Rumah & Usaha)</li>
                <li>Slip Gaji / Surat Keterangan Penghasilan (Karyawan / Pegawai / PNS)</li>
                <li>Surat Keterangan Bekerja Minimal 3 (tiga) Tahun dan Tanda Pengenal Perusahaan</li>
                <li>Foto Copy Surat Ijin Praktek (Khusus Profesional)</li>
                <li>Foto Copy NPWP</li>
                <li>Foto Copy Buku Tabungan / Rek Koran 3 Bulan Terakhir</li>
            </ul>
            <h4 class="font-semibold text-gray-800 mb-3">Wiraswasta</h4>
            <ul class="list-disc list-inside text-gray-600 ml-4 mb-6">
                <li>Foto Copy KTP Suami Istri</li>
                <li>Foto Copy Kartu Keluarga dan Surat Nikah</li>
                <li>Rumah Milik Sendiri (PBB & Rek Listrik / Telp / Pam) / Jika Rumah Kontrak (Surat Perjanjian Sewa
                    Rumah & Usaha)</li>
                <li>Surat Keterangan Domisili (SKD) dan Surat Keterangan Usaha (SKU)</li>
                <li>Foto Copy NPWP Surat Keterangan Terdaftar</li>
                <li>SIUP TDP Anggaran Dasar PTV / CV</li>
                <li>Foto Copy Tabungan / Rekening Koran 3 (tiga) bulan terakhir</li>
                <li>Purchasing Order (PO) Kontrak Kerja / SPK Faktur dan bukti pendukung adanya proyek yang akan
                    dilaksanakan</li>
            </ul>
        </div>
    </section>
</x-layouts.nasabah>
