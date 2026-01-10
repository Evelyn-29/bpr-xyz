<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditApplication;
use Illuminate\Support\Facades\Storage;

class AdminSlikController extends Controller
{
    /**
     * Menampilkan daftar pengajuan yang PERLU di-cek SLIK-nya
     * Logic: Pengajuan status 'Menunggu Verifikasi'
     */
    public function index(Request $request)
    {
        // Base Query: Hanya yang statusnya 'Menunggu Verifikasi'
        $query = CreditApplication::with(['nasabahProfile', 'creditFacility', 'user'])
            ->where('status', 'Menunggu Verifikasi');

        // LOGIC PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Cari berdasarkan No Pengajuan
                $q->where('no_pengajuan', 'like', "%{$search}%")
                    // Atau cari di data Profile Nasabah (Nama / KTP)
                    ->orWhereHas('nasabahProfile', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('no_ktp', 'like', "%{$search}%");
                    })
                    // Fallback ke User Name jika profile belum lengkap (jarang terjadi tapi aman)
                    ->orWhereHas('user', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Eksekusi & Pagination
        $applications = $query->latest('submitted_at')
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.slik.index', compact('applications'));
    }

    /**
     * Form Upload SLIK
     */
    public function edit($id)
    {
        $application = CreditApplication::with('nasabahProfile')->findOrFail($id);
        return view('admin.slik.upload', compact('application'));
    }

    /**
     * Proses Simpan File SLIK
     */
    public function update(Request $request, $id)
    {
        $application = CreditApplication::findOrFail($id);

        $request->validate([
            'slik_file'   => 'required|mimes:pdf|max:5120', // Max 5MB, PDF only
            'slik_status' => 'required|string',
            'slik_notes'  => 'nullable|string'
        ]);

        // Upload File
        if ($request->hasFile('slik_file')) {
            // Hapus file lama jika ada (untuk update)
            if ($application->slik_path && Storage::disk('public')->exists($application->slik_path)) {
                Storage::disk('public')->delete($application->slik_path);
            }

            // Simpan file baru: slik/ID_PENGAJUAN/slik.pdf
            $path = $request->file('slik_file')->storeAs(
                "slik/{$application->id}",
                "hasil_slik_" . time() . ".pdf",
                'public'
            );

            $application->update([
                'slik_path'   => $path,
                'slik_status' => $request->slik_status,
                'slik_notes'  => $request->slik_notes,
            ]);
        }

        return redirect()->route('app.slik.index')
            ->with('success', 'Data SLIK berhasil diunggah untuk pengajuan ' . $application->no_pengajuan);
    }
}
