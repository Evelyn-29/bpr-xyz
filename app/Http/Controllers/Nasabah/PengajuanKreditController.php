<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NasabahProfile;
use App\Models\CreditApplication;
use App\Models\CreditCollateral;
use App\Models\CreditDocument;
use App\Models\CreditFacility;
use App\Models\CreditApplicationDetail;
use Illuminate\Support\Facades\Storage;

class PengajuanKreditController extends Controller
{
    /**
     * Helper untuk memulihkan session ID jika hilang tapi ada draft di DB
     */
    private function ensureApplicationSession()
    {
        if (!session()->has('application_id')) {
            $draft = CreditApplication::where('user_id', Auth::id())
                ->whereIn('status', ['draft_step1', 'draft_step2', 'draft_step3'])
                ->latest()
                ->first();

            if ($draft) {
                session(['application_id' => $draft->id]);
                return $draft;
            }
        }
        return null;
    }

    // ================= STEP 1: DATA DIRI =================
    public function createStep1()
    {
        $userId = Auth::id();

        // 1. CEK APAKAH ADA PENGAJUAN YANG SEDANG DIPROSES / AKTIF
        $activeApplication = CreditApplication::where('user_id', $userId)
            ->whereIn('status', ['Menunggu Verifikasi', 'Disetujui'])
            ->exists();

        if ($activeApplication) {
            return redirect()->route('nasabah.riwayat.index')
                ->with('error', 'Anda masih memiliki pengajuan yang sedang diproses atau pinjaman aktif.');
        }

        // 2. CEK APAKAH ADA DRAFT (Mode Lanjutkan/Edit Paksa)
        $draft = CreditApplication::where('user_id', $userId)
            ->whereIn('status', ['draft_step1', 'draft_step2', 'draft_step3'])
            ->latest()
            ->first();

        if ($draft) {
            // Set session ulang untuk jaga-jaga
            session(['application_id' => $draft->id]);

            // Redirect sesuai progres terakhir
            if ($draft->status === 'draft_step2') {
                return redirect()->route('nasabah.pengajuan.step2');
            } elseif ($draft->status === 'draft_step3') {
                // Jika draft_step3, cek apakah sudah lengkap atau belum.
                // Amannya ke step3 dulu, nanti step3 yang lempar ke review jika user mau
                return redirect()->route('nasabah.pengajuan.step3');
            }
            // Jika draft_step1, biarkan lanjut di bawah (render view step 1)
        }

        $profile = NasabahProfile::firstOrNew(['user_id' => $userId]);

        return view('nasabah.pengajuan.step1', compact('profile'));
    }

    private function generateKodeNasabah()
    {
        $prefix = 'NSB-' . date('Ym') . '-';

        $lastProfile = NasabahProfile::where('kode_nasabah', 'like', $prefix . '%')
            ->orderBy('kode_nasabah', 'desc')
            ->first();

        if (!$lastProfile) {
            $sequence = 1;
        } else {
            $lastNumber = (int) substr($lastProfile->kode_nasabah, -4);
            $sequence = $lastNumber + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function postStep1(Request $request)
    {
        $userId = Auth::id();

        // Validasi Input
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'no_ktp' => 'required|digits:16|unique:nasabah_profiles,no_ktp,' . $userId . ',user_id',
            'no_hp' => 'required|string|max:15|unique:nasabah_profiles,no_hp,' . $userId . ',user_id',
            'email' => 'required|email',
            'alamat_tinggal' => 'required|string',
            'alamat_ktp' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'agama' => 'required|string',
            'nama_ibu_kandung' => 'required|string',
            'status_perkawinan' => 'required|string',
            'no_npwp' => 'nullable|numeric|min_digits:15|max_digits:16',
            'status_rumah' => 'required|string',
        ]);

        $profile = NasabahProfile::where('user_id', $userId)->first();
        $kodeNasabah = $profile ? $profile->kode_nasabah : null;
        if (empty($kodeNasabah)) {
            $kodeNasabah = $this->generateKodeNasabah();
        }
        $validated['kode_nasabah'] = $kodeNasabah;

        // Simpan Profil
        NasabahProfile::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );

        // LOGIKA PENTING: Create vs Update Application
        // Cari draft yang sudah ada
        $application = CreditApplication::where('user_id', $userId)
            ->whereIn('status', ['draft_step1', 'draft_step2', 'draft_step3'])
            ->first();

        if (!$application) {
            // Jika TIDAK ADA draft sama sekali, barulah kita buat baru.
            // Ini terjadi jika nasabah baru pertama kali daftar, atau pengajuan sebelumnya sudah selesai/ditolak.
            $application = CreditApplication::create([
                'user_id' => $userId,
                'status' => 'draft_step1'
            ]);
        } else {
            // Jika SUDAH ADA, kita update 'updated_at' nya saja biar sistem tahu nasabah baru aktif.
            // Kita TIDAK mengubah statusnya. Misal dia sudah di draft_step2, biarkan tetap di situ.
            $application->touch();
        }

        session(['application_id' => $application->id]);

        // Redirect logic
        if ($application->status === 'draft_step2') {
            return redirect()->route('nasabah.pengajuan.step2')->with('success', 'Data profil diperbarui.');
        } elseif ($application->status === 'draft_step3') {
            return redirect()->route('nasabah.pengajuan.step3')->with('success', 'Data profil diperbarui.');
        }

        return redirect()->route('nasabah.pengajuan.step2')->with('success', 'Data profil disimpan.');
    }

    // ================= STEP 2: FASILITAS & DETAIL =================

    public function createStep2()
    {
        $this->ensureApplicationSession();
        $applicationId = session('application_id');

        if (!$applicationId) return redirect()->route('nasabah.pengajuan.step1');

        $application = CreditApplication::with('nasabahProfile')->find($applicationId);

        if (!$application) {
            return redirect()->route('nasabah.pengajuan.step1');
        }

        // 1. Coba ambil detail dari draft saat ini
        $applicationDetail = CreditApplicationDetail::where('credit_application_id', $applicationId)->first();

        // 2. Jika draft detail kosong (nasabah baru masuk step 2),
        // Coba cari data dari pengajuan TERAKHIR milik user ini (Pre-fill data)
        if (!$applicationDetail) {
            $lastApp = CreditApplication::with('detail')
                ->where('user_id', Auth::id())
                ->where('id', '!=', $applicationId) // Jangan ambil diri sendiri
                ->whereNotNull('submitted_at') // Ambil yang sudah pernah disubmit
                ->latest()
                ->first();

            if ($lastApp && $lastApp->detail) {
                // Kita gunakan data lama hanya untuk tampilan (pre-fill)
                // Tidak disimpan ke database dulu sampai user klik Simpan
                $applicationDetail = $lastApp->detail;
            }
        }

        // Jika masih kosong juga (nasabah baru pertama kali), buat objek kosong agar view tidak error
        if (!$applicationDetail) {
            $applicationDetail = new CreditApplicationDetail();
        }

        $facilities = CreditFacility::where('aktif', 1)->get();

        return view('nasabah.pengajuan.step2', compact('facilities', 'application', 'applicationDetail'));
    }

    public function postStep2(Request $request)
    {
        // --- STEP 1: SANITASI DATA (PENTING UNTUK MASKING) ---
        // Hapus titik dari jumlah_pinjaman agar menjadi angka murni (misal: 10.000.000 -> 10000000)
        if ($request->has('jumlah_pinjaman')) {
            $cleanJumlah = str_replace('.', '', $request->jumlah_pinjaman);
            $request->merge([
                'jumlah_pinjaman' => $cleanJumlah,
            ]);
        }

        $applicationId = session('application_id');
        $application = CreditApplication::findOrFail($applicationId);
        $profile = NasabahProfile::where('user_id', Auth::id())->first();

        // Ambil data fasilitas untuk mendapatkan max_jangka_waktu
        $facility = CreditFacility::with('tiers')->find($request->credit_facility_id);

        // Jika fasilitas tidak ditemukan, kita beri default agar validasi jangka_waktu tidak error
        $maxJangkaWaktu = $facility ? $facility->max_jangka_waktu : 60;

        // Logic Validasi Max Plafond Dynamic
        $maxPlafondRule = '';

        if ($facility && $facility->tiers->count() > 0) {
            // 1. Cek apakah ada tier yang 'max_plafond'-nya NULL (Unlimited)
            $isUnlimited = $facility->tiers->contains(function ($tier) {
                return is_null($tier->max_plafond);
            });

            // 2. Jika TIDAK Unlimited, cari angka terbesar dari semua tier
            if (!$isUnlimited) {
                $maxLimit = $facility->tiers->max('max_plafond'); // Ambil nilai tertinggi
                $maxPlafondRule = '|max:' . $maxLimit;
            }
        }

        // --- STEP 2: DEFINISI RULES ---
        $rules = [
            'credit_facility_id' => 'required|exists:credit_facilities,id',
            'tujuan_pinjaman' => 'required|string',
            'jumlah_pinjaman'    => 'required|numeric|min:1000000' . $maxPlafondRule,
            'jangka_waktu' => 'required|integer|min:1|max:' . $maxJangkaWaktu,
            'sumber_pendapatan' => 'required|string',
        ];

        // Conditional Rules Pasangan
        if ($profile && $profile->status_perkawinan === 'Menikah') {
            $rules = array_merge($rules, [
                'nama_pasangan' => 'required|string|max:255',
                'no_ktp_pasangan' => 'required|string|max:20',
                'alamat_tinggal_pasangan' => 'required|string|max:255',
                'alamat_ktp_pasangan' => 'required|string|max:255',
                'pekerjaan_pasangan' => 'required|string|max:100',
                'email_pasangan' => 'required|email|max:255',
                'no_hp_pasangan' => 'required|string|max:15', // Gunakan string untuk no HP agar lebih aman
            ]);
        }

        // Rules Penjamin
        $rules = array_merge($rules, [
            'nama_penjamin' => 'required|string|max:255',
            'no_ktp_penjamin' => 'required|string|max:20',
            'hubungan_penjamin' => 'required|string|max:50',
            'alamat_penjamin' => 'required|string|max:255',
            'email_penjamin' => 'required|email|max:255',
            'no_hp_penjamin' => 'required|string|max:15',
        ]);

        // --- STEP 3: JALANKAN VALIDASI ---
        // Jika gagal, Laravel otomatis kembali ke form dengan $errors
        $validated = $request->validate($rules);

        // Validasi NPWP > 50 Juta (Gunakan $validated agar angkanya sudah bersih)
        if ($validated['jumlah_pinjaman'] > 50000000 && empty($profile->no_npwp)) {
            return redirect()->route('nasabah.pengajuan.step1') // Balik ke step 1 karena NPWP ada di sana
                ->with('warning', 'NPWP wajib diisi untuk pengajuan lebih dari 50 juta.')
                ->withInput();
        }

        $requiresNpwp = ($validated['jumlah_pinjaman'] > 50000000) ? 1 : 0;
        $newStatus = ($application->status == 'draft_step1') ? 'draft_step2' : $application->status;

        // --- STEP 4: UPDATE DATA ---
        $application->update([
            'credit_facility_id' => $validated['credit_facility_id'],
            'tujuan_pinjaman' => $validated['tujuan_pinjaman'],
            'jumlah_pinjaman' => $validated['jumlah_pinjaman'],
            'jangka_waktu' => $validated['jangka_waktu'],
            'sumber_pendapatan' => $validated['sumber_pendapatan'],
            'status' => $newStatus,
            'requires_npwp' => $requiresNpwp,
        ]);

        CreditApplicationDetail::updateOrCreate(
            ['credit_application_id' => $application->id],
            [
                // Data Pasangan
                'nama_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['nama_pasangan'] : null,
                'no_ktp_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['no_ktp_pasangan'] : null,
                'alamat_tinggal_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['alamat_tinggal_pasangan'] : null,
                'alamat_ktp_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['alamat_ktp_pasangan'] : null,
                'pekerjaan_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['pekerjaan_pasangan'] : null,
                'email_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['email_pasangan'] : null,
                'no_hp_pasangan' => ($profile->status_perkawinan === 'Menikah') ? $validated['no_hp_pasangan'] : null,

                // Data Penjamin
                'nama_penjamin' => $validated['nama_penjamin'],
                'no_ktp_penjamin' => $validated['no_ktp_penjamin'],
                'hubungan_penjamin' => $validated['hubungan_penjamin'],
                'alamat_penjamin' => $validated['alamat_penjamin'],
                'email_penjamin' => $validated['email_penjamin'],
                'no_hp_penjamin' => $validated['no_hp_penjamin'],
            ]
        );

        return redirect()->route('nasabah.pengajuan.step3')->with('success', 'Data detail pinjaman disimpan.');
    }

    public function backToStep1()
    {
        return redirect()->route('nasabah.pengajuan.step1');
    }

    // ================= STEP 3: DOKUMEN & AGUNAN =================

    public function createStep3()
    {
        $this->ensureApplicationSession();
        $applicationId = session('application_id');

        if (!$applicationId) return redirect()->route('nasabah.pengajuan.step1');

        $application = CreditApplication::with('nasabahProfile')->find($applicationId);

        // Validasi Flow
        if (!$application) return redirect()->route('nasabah.pengajuan.step1');

        // Cek detail step 2
        $applicationDetail = CreditApplicationDetail::where('credit_application_id', $applicationId)->first();
        if (!$applicationDetail) {
            return redirect()->route('nasabah.pengajuan.step2');
        }

        // --- LOGIKA AUTO-FILL AGUNAN ---
        $collateral = CreditCollateral::where('credit_application_id', $applicationId)->first();

        // Jika kosong, cari dari pengajuan terakhir
        $lastApp = null; // Simpan referensi app terakhir

        if (!$collateral) {
            $lastApp = CreditApplication::with(['collateral', 'documents'])
                ->where('user_id', Auth::id())
                ->where('id', '!=', $applicationId)
                ->whereNotNull('submitted_at')
                ->latest()
                ->first();

            if ($lastApp && $lastApp->collateral) {
                // Gunakan data agunan lama untuk tampilan (belum save ke DB)
                $collateral = $lastApp->collateral;
            } else {
                // Objek kosong agar view tidak error
                $collateral = new CreditCollateral();
            }
        }

        // --- LOGIKA AUTO-FILL DOKUMEN ---
        // 1. Ambil dokumen yang SUDAH diupload di draft ini
        $currentDocs = CreditDocument::where('credit_application_id', $applicationId)->get();
        $dokumenMap = $currentDocs->keyBy('jenis_dokumen');

        // 2. Jika draft ini masih minim dokumen, coba intip dokumen lama
        // Kita hanya ambil dokumen lama JIKA di draft sekarang belum ada
        if ($lastApp && $lastApp->documents) {
            foreach ($lastApp->documents as $oldDoc) {
                // Jika jenis dokumen ini BELUM ada di draft sekarang, masukkan ke map
                if (!isset($dokumenMap[$oldDoc->jenis_dokumen])) {
                    $dokumenMap[$oldDoc->jenis_dokumen] = $oldDoc;
                }
            }
        }

        return view('nasabah.pengajuan.step3', compact('application', 'collateral', 'dokumenMap', 'lastApp'));
    }

    public function postStep3(Request $request)
    {
        $applicationId = session('application_id');
        $application = CreditApplication::find($applicationId);
        $profile = NasabahProfile::where('user_id', Auth::id())->firstOrFail();

        if (!$application) return redirect()->route('nasabah.pengajuan.step1');

        // --- VALIDASI ---
        $rules = [
            'jenis_agunan'      => 'required|in:SHM,SHGB',
            'nomor_sertifikat'  => 'required|string|max:255',
            'atas_nama'         => 'required|string|max:255',
            'foto_agunan_path'  => 'required|string',
            'sertifikat_path'   => 'required|string',
            'dokumen.ktp_pemohon_path'     => 'required|string',
            'dokumen.kartu_keluarga_path'  => 'required|string',
            'dokumen.rekening_koran_path'  => 'required|string',
        ];

        if ($profile->status_perkawinan === 'Menikah') {
            $rules['dokumen.ktp_pasangan_path'] = 'required|string';
            $rules['dokumen.surat_nikah_path']  = 'required|string';
        }
        if ($profile->status_rumah === 'Milik Sendiri') {
            $rules['dokumen.pbb_path']         = 'required|string';
            $rules['dokumen.rek_listrik_path'] = 'required|string';
        }
        if ($profile->status_rumah === 'Sewa') {
            $rules['dokumen.surat_sewa_path'] = 'required|string';
        }
        if ($application->requires_npwp == 1) {
            $rules['dokumen.npwp_path'] = 'required|string';
        }
        if ($application->sumber_pendapatan === 'Gaji') {
            $rules['dokumen.surat_keterangan_bekerja_path'] = 'required|string';
            $rules['dokumen.slip_gaji_path']                = 'required|string';
        }
        if ($application->sumber_pendapatan === 'Usaha') {
            $rules['dokumen.sku_path']  = 'required|string';
            $rules['dokumen.nota_path'] = 'required|string';
        }
        if ($request->jenis_agunan === "SHGB") {
            $rules['masa_berlaku'] = ['required', 'date', function ($attribute, $value, $fail) {
                if ($value < now()->addYears(10)->toDateString()) {
                    $fail("Masa berlaku SHGB minimal 10 tahun dari sekarang.");
                }
            }];
        }

        $validated = $request->validate($rules);

        $processFile = function ($path, $targetFolder) use ($application) {
            if (!$path) return null;

            // KASUS 1: File Baru (dari Temp)
            if (str_starts_with($path, 'temp/')) {
                $basename = basename($path);
                $newPath = $targetFolder . '/' . $basename;
                Storage::disk('public')->makeDirectory($targetFolder);

                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('public')->put($newPath, Storage::disk('local')->get($path));
                    Storage::disk('local')->delete($path);
                    return $newPath;
                }
            }

            // KASUS 2: File Lama (Sudah ada di public storage)
            if (Storage::disk('public')->exists($path)) {
                // Cek apakah file ini milik aplikasi ini?
                // Jika path mengandung ID aplikasi saat ini, berarti sudah aman
                if (str_contains($path, "/{$application->id}/")) {
                    return $path; // Tidak perlu diapa-apakan
                }

                // KASUS 3: File Milik Pengajuan LAMA (Reuse)
                // Kita harus COPY file ini ke folder aplikasi baru
                // Agar jika pengajuan lama dihapus, pengajuan ini tidak rusak.
                $basename = basename($path);
                $newPath = $targetFolder . '/' . $basename;

                // Hindari copy file ke dirinya sendiri
                if ($path !== $newPath) {
                    Storage::disk('public')->makeDirectory($targetFolder);
                    Storage::disk('public')->copy($path, $newPath);
                    return $newPath;
                }
            }

            return $path; // Fallback
        };

        // Simpan Dokumen
        foreach ($validated['dokumen'] ?? [] as $key => $path) {
            // Tentukan folder target
            $newPath = $processFile($path, "dokumen/{$application->id}");

            if ($newPath) {
                CreditDocument::updateOrCreate(
                    ['credit_application_id' => $application->id, 'jenis_dokumen' => $key],
                    ['nama_file' => $key . ".file", 'path' => $newPath, 'status_verifikasi' => 'Belum Diverifikasi']
                );
            }
        }

        // Simpan Agunan
        $fotoAgunan = $processFile($validated['foto_agunan_path'], "agunan/{$application->id}");
        $sertifikat = $processFile($validated['sertifikat_path'], "agunan/{$application->id}");

        CreditCollateral::updateOrCreate(
            ['credit_application_id' => $application->id],
            [
                'jenis_agunan'     => $validated['jenis_agunan'],
                'nomor_sertifikat' => $validated['nomor_sertifikat'],
                'atas_nama'        => $validated['atas_nama'],
                'masa_berlaku'     => $validated['masa_berlaku'] ?? null,
                'foto_agunan'      => $fotoAgunan,
                'file_sertifikat'  => $sertifikat,
            ]
        );

        $application->update(['status' => 'draft_step3']);

        return redirect()->route('nasabah.pengajuan.review')
            ->with('success', 'Dokumen berhasil disimpan.');
    }

    public function uploadTemp(Request $request)
    {
        $request->validate(['file' => 'nullable|max:1024', 'field' => 'required|string']);
        $folder = 'temp/' . (session('application_id') ?? Auth::id()); // Fallback ke Auth ID jika session null
        $path = $request->file('file')->store($folder);
        return response()->json(['status' => 'success', 'path' => $path]);
    }

    public function backToStep2()
    {
        return redirect()->route('nasabah.pengajuan.step2');
    }

    // ================= REVIEW & SUBMIT =================

    public function createReview()
    {
        $this->ensureApplicationSession();
        $applicationId = session('application_id');

        if (!$applicationId) return redirect()->route('nasabah.pengajuan.step1');

        $application = CreditApplication::with([
            'nasabahProfile',
            'creditFacility',
            'detail',
            'collateral',
            'documents'
        ])->find($applicationId);

        if (!$application) return redirect()->route('nasabah.pengajuan.step1');

        // Pastikan status sudah draft_step3
        if ($application->status !== 'draft_step3') {
            return redirect()->route('nasabah.pengajuan.step3');
        }

        $dokumenMap = $application->documents->keyBy('jenis_dokumen');

        return view('nasabah.pengajuan.review', compact('application', 'dokumenMap'));
    }

    private function generateNoPengajuan()
    {
        $prefix = 'PK-' . date('Ymd') . '-'; // Contoh: PK-20251121-

        // Cari pengajuan terakhir HARI INI yang sudah ada nomornya
        $lastApp = CreditApplication::where('no_pengajuan', 'like', $prefix . '%')
            ->orderBy('no_pengajuan', 'desc')
            ->first();

        if (!$lastApp) {
            $sequence = 1;
        } else {
            // Ambil 4 digit terakhir, ubah jadi integer, tambah 1
            $lastNumber = (int) substr($lastApp->no_pengajuan, -4);
            $sequence = $lastNumber + 1;
        }

        // Format jadi 4 digit (0001, 0002, dst)
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function postReview(Request $request)
    {
        $applicationId = session('application_id');
        $application = CreditApplication::find($applicationId);

        // Final Security Check
        if (!$application || $application->status !== 'draft_step3') {
            return redirect()->route('nasabah.dashboard')
                ->with('error', 'Gagal mengirim pengajuan. Status tidak valid.');
        }

        // --- GENERATE NOMOR UNIK DI SINI ---
        // Cek dulu biar gak double generate kalau user refresh page
        if (empty($application->no_pengajuan)) {
            $noPengajuan = $this->generateNoPengajuan();
        } else {
            $noPengajuan = $application->no_pengajuan;
        }

        // SUBMIT FINAL
        $application->update([
            'no_pengajuan'  => $noPengajuan, // Simpan nomor
            'status'        => 'Menunggu Verifikasi',
            'submitted_at'  => now(),
        ]);

        // Hapus session agar user bersih saat kembali ke dashboard
        session()->forget('application_id');

        return redirect()->route('nasabah.dashboard')
            ->with('success', 'Pengajuan kredit berhasil dikirim! Kami akan segera memverifikasi data Anda.');
    }

    public function backToStep3()
    {
        return redirect()->route('nasabah.pengajuan.step3');
    }
}
