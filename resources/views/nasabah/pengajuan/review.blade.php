<x-layouts.nasabah :title="'Review Pengajuan Kredit'">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-bold">Review Pengajuan Kredit</h1>
            <span class="text-sm text-gray-500 ms-4 pt-1">Konfirmasi Akhir</span>
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

    @if (session('error'))
        <x-alert type="error">
            <strong>Error:</strong> {{ session('error') }}
        </x-alert>
    @endif

    @php
        // Helper kecil untuk menampilkan data
        function reviewItem($label, $value, $default = 'Belum diisi')
        {
            echo '<div class="mb-3">';
            echo '<dt class="text-sm font-medium text-gray-500">' . $label . '</dt>';
            echo '<dd class="text-md font-semibold text-gray-900">' . ($value ?: $default) . '</dd>';
            echo '</div>';
        }

        // Helper untuk menampilkan file
        function reviewFile($label, $fileObject)
        {
            echo '<div class="mb-3">';
            echo '<dt class="text-sm font-medium text-gray-500">' . $label . '</dt>';
            if ($fileObject && $fileObject->path) {
                echo '<dd class="text-md font-semibold text-blue-600 hover:text-blue-800">';
                echo '<a href="' . Storage::url($fileObject->path) . '" target="_blank">Lihat Dokumen</a>';
                echo '</dd>';
            } else {
                echo '<dd class="text-md font-semibold text-red-600">Dokumen belum diunggah</dd>';
            }
            echo '</div>';
        }
    @endphp

    <section id="review-pengajuan" class="bg-white rounded-2xl shadow-md p-8 mx-auto">

        <div class="mb-6 pb-4">
            <h3 class="text-xl font-semibold mb-6 pb-2 text-gray-800 border-b">
                Data Pemohon (Step 1)
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                @php $profil = $application->nasabahProfile; @endphp
                {{ reviewItem('Nama Lengkap', $profil->nama_lengkap) }}
                {{ reviewItem('No. KTP', $profil->no_ktp) }}
                {{ reviewItem('No. HP', $profil->no_hp) }}
                {{ reviewItem('Email', $profil->email) }}
                {{ reviewItem('Jenis Kelamin', $profil->jenis_kelamin) }}
                {{ reviewItem('Pendidikan Terakhir', $profil->pendidikan_terakhir) }}
                {{ reviewItem('Agama', $profil->agama) }}
                {{ reviewItem('Nama Ibu Kandung', $profil->nama_ibu_kandung) }}
                {{ reviewItem('Status Perkawinan', $profil->status_perkawinan) }}
                {{ reviewItem('Status Rumah', $profil->status_rumah) }}
                {{ reviewItem('No. NPWP', $profil->no_npwp, 'Tidak Ada') }}
                {{ reviewItem('Alamat Tinggal', $profil->alamat_tinggal) }}
                {{ reviewItem('Alamat KTP', $profil->alamat_ktp) }}
            </dl>
        </div>

        <div class="mb-6 pb-4">
            <h3 class="text-xl font-semibold mb-6 pb-2 text-gray-800 border-b">
                Data Pengajuan (Step 2)
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                {{ reviewItem('Fasilitas Kredit', $application->creditFacility->nama) }}
                {{ reviewItem('Jumlah Pinjaman', 'Rp ' . number_format($application->jumlah_pinjaman, 0, ',', '.')) }}
                {{ reviewItem('Jangka Waktu', $application->jangka_waktu . ' bulan') }}
                {{ reviewItem('Tujuan Pinjaman', $application->tujuan_pinjaman) }}
                {{ reviewItem('Sumber Pendapatan', $application->sumber_pendapatan) }}
            </dl>
        </div>

        @if ($profil->status_perkawinan === 'Menikah' && $application->creditApplicationDetail)
            <div class="mb-6 pb-4">
                <h3 class="text-lg font-semibold mb-6 pb-2 text-gray-700 border-b">
                    Data Pasangan
                </h3>
                @php $detail = $application->creditApplicationDetail; @endphp
                <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                    {{ reviewItem('Nama Pasangan', $detail->nama_pasangan) }}
                    {{ reviewItem('No. KTP Pasangan', $detail->no_ktp_pasangan) }}
                    {{ reviewItem('Pekerjaan Pasangan', $detail->pekerjaan_pasangan) }}
                    {{ reviewItem('No. HP Pasangan', $detail->no_hp_pasangan) }}
                    {{ reviewItem('Email Pasangan', $detail->email_pasangan) }}
                    {{ reviewItem('Alamat Tinggal Pasangan', $detail->alamat_tinggal_pasangan) }}
                    {{ reviewItem('Alamat KTP Pasangan', $detail->alamat_ktp_pasangan) }}
                </dl>
            </div>
        @endif

        @if ($application->creditApplicationDetail)
            <div class="mb-6 pb-4">
                <h3 class="text-lg font-semibold mb-6 pb-2 text-gray-700 border-b">
                    Data Penjamin
                </h3>
                @php $detail = $application->creditApplicationDetail; @endphp
                <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                    {{ reviewItem('Nama Penjamin', $detail->nama_penjamin) }}
                    {{ reviewItem('Hubungan Penjamin', $detail->hubungan_penjamin) }}
                    {{ reviewItem('No. KTP Penjamin', $detail->no_ktp_penjamin) }}
                    {{ reviewItem('No. HP Penjamin', $detail->no_hp_penjamin) }}
                    {{ reviewItem('Email Penjamin', $detail->email_penjamin) }}
                    {{ reviewItem('Alamat Penjamin', $detail->alamat_penjamin) }}
                </dl>
            </div>
        @endif

        <div class="mb-6 pb-4">
            <h3 class="text-xl font-semibold mb-6 pb-2 text-gray-800 border-b">
                Data Agunan (Step 3)
            </h3>
            @php $agunan = $application->collateral; @endphp
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                {{ reviewItem('Jenis Agunan', $agunan->jenis_agunan) }}
                {{ reviewItem('Nomor Sertifikat', $agunan->nomor_sertifikat) }}
                {{ reviewItem('Atas Nama', $agunan->atas_nama) }}
                @if ($agunan->jenis_agunan === 'SHGB')
                    {{ reviewItem('Masa Berlaku', \Carbon\Carbon::parse($agunan->masa_berlaku)->format('d F Y')) }}
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Foto Agunan</dt>
                    <dd class="text-md font-semibold text-blue-600 hover:text-blue-800">
                        <a href="{{ Storage::url($agunan->foto_agunan) }}" target="_blank">Lihat Foto</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">File Sertifikat</dt>
                    <dd class="text-md font-semibold text-blue-600 hover:text-blue-800">
                        <a href="{{ Storage::url($agunan->file_sertifikat) }}" target="_blank">Lihat Sertifikat</a>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="mb-6 pb-4">
            <h3 class="text-xl font-semibold mb-6 pb-2 text-gray-800 border-b">
                Dokumen Terunggah (Step 3)
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6">
                {{ reviewFile('KTP Pemohon', $dokumenMap['ktp_pemohon_path'] ?? null) }}
                {{ reviewFile('Kartu Keluarga', $dokumenMap['kartu_keluarga_path'] ?? null) }}
                {{ reviewFile('Rekening Koran (3 Bln)', $dokumenMap['rekening_koran_path'] ?? null) }}

                @if ($profil->status_perkawinan === 'Menikah')
                    {{ reviewFile('KTP Pasangan', $dokumenMap['ktp_pasangan_path'] ?? null) }}
                    {{ reviewFile('Surat Nikah', $dokumenMap['surat_nikah_path'] ?? null) }}
                @endif

                @if ($profil->status_rumah === 'Milik Sendiri')
                    {{ reviewFile('PBB', $dokumenMap['pbb_path'] ?? null) }}
                    {{ reviewFile('Rek. Listrik/PAM', $dokumenMap['rek_listrik_path'] ?? null) }}
                @endif

                @if ($profil->status_rumah === 'Sewa')
                    {{ reviewFile('Surat Sewa Rumah', $dokumenMap['surat_sewa_path'] ?? null) }}
                @endif

                @if ($application->requires_npwp == 1)
                    {{ reviewFile('NPWP', $dokumenMap['npwp_path'] ?? null) }}
                @endif

                @if ($application->sumber_pendapatan === 'Gaji')
                    {{ reviewFile('Surat Ket. Bekerja', $dokumenMap['surat_keterangan_bekerja_path'] ?? null) }}
                    {{ reviewFile('Slip Gaji (3 Bln)', $dokumenMap['slip_gaji_path'] ?? null) }}
                @endif

                @if ($application->sumber_pendapatan === 'Usaha')
                    {{ reviewFile('Surat Keterangan Usaha (SKU)', $dokumenMap['sku_path'] ?? null) }}
                    {{ reviewFile('Nota/Laporan Keuangan', $dokumenMap['nota_path'] ?? null) }}
                @endif
            </dl>
        </div>


        {{-- ========== AKSI NAVIGASI ========== --}}
        <div class="pt-8 mt-8 border-t flex justify-between items-center">
            <a href="{{ route('nasabah.pengajuan.back.step3') }}"
                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-xl hover:bg-gray-300 transition">
                ← Kembali (Ubah Dokumen)
            </a>
            <form action="{{ route('nasabah.pengajuan.review.post') }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin data yang diisi sudah benar?')">
                @csrf
                <button type="submit"
                    class="bg-green-600 text-white font-semibold px-10 py-3 rounded-xl hover:bg-green-700 transition">
                    Kirim Pengajuan
                </button>
            </form>
        </div>

    </section>

</x-layouts.nasabah>
