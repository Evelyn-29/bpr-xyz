<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\NasabahProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $query = NasabahProfile::with('user')->latest();

        // Search logic
        $query->when($request->search, function ($q) use ($request) {
            $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
              ->orWhere('no_ktp', 'like', '%' . $request->search . '%')
              ->orWhere('kode_nasabah', 'like', '%' . $request->search . '%');
        });

        $nasabahs = $query->paginate(10)->withQueryString();

        return view('superadmin.master.nasabah.index', compact('nasabahs'));
    }

    // Method CREATE dan STORE dihapus karena tidak diperlukan.

    public function show($id)
    {
        $nasabah = NasabahProfile::with(['user', 'creditApplications'])->findOrFail($id);
        return view('superadmin.master.nasabah.show', compact('nasabah'));
    }

    public function edit($id)
    {
        $nasabah = NasabahProfile::findOrFail($id);
        return view('superadmin.master.nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, $id)
    {
        $nasabah = NasabahProfile::findOrFail($id);

        // 1. Validasi HANYA untuk field yang diizinkan edit
        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'no_ktp'           => ['required', 'digits:16', Rule::unique('nasabah_profiles')->ignore($id)],
            'nama_ibu_kandung' => 'required|string|max:255',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
        ]);

        try {
            DB::transaction(function () use ($request, $nasabah) {
                // 2. Update Profile (Hanya kolom tertentu)
                $nasabah->update([
                    'nama_lengkap'     => $request->nama_lengkap,
                    'no_ktp'           => $request->no_ktp,
                    'nama_ibu_kandung' => $request->nama_ibu_kandung,
                    'jenis_kelamin'    => $request->jenis_kelamin,
                ]);

                // 3. Sinkronisasi Nama ke Tabel User (Penting agar login name sesuai)
                if ($nasabah->user) {
                    $nasabah->user->update(['name' => $request->nama_lengkap]);
                }
            });

            return redirect()->route('app.nasabah.index')->with('success', 'Data krusial nasabah berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $nasabah = NasabahProfile::findOrFail($id);
        
        // Hapus User Login-nya juga (Soft Delete)
        if ($nasabah->user) {
            $nasabah->user->delete();
        }
        
        // Hapus Profil
        $nasabah->delete();

        return redirect()->route('app.nasabah.index')->with('success', 'Data nasabah berhasil dihapus.');
    }
}