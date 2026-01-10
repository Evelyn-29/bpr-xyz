<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditApplication;
use App\Models\CreditFacility;
use Illuminate\Support\Facades\Auth;

class ManagerRekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditApplication::with(['nasabahProfile', 'creditFacility', 'user'])
            ->where('status', 'Menunggu Verifikasi')
            ->whereNotNull('slik_path')
            ->whereNull('recommendation_status');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                    ->orWhereHas('nasabahProfile', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->latest('submitted_at')->paginate(10);

        return view('manager.rekomendasi.index', compact('applications'));
    }

    public function show($id)
    {
        $application = CreditApplication::with([
            'nasabahProfile',
            'creditFacility',
            'detail',
            'collateral',
            'documents'
        ])->findOrFail($id);

        return view('manager.rekomendasi.show', compact('application'));
    }

    public function update(Request $request, $id)
    {
        $application = CreditApplication::findOrFail($id);
        $maxTenor = $application->creditFacility->max_jangka_waktu ?? 60;

        if ($request->filled('manager_recommended_amount')) {
            $clean = str_replace('.', '', $request->manager_recommended_amount);
            $clean = str_replace(',', '.', $clean);

            $request->merge([
                'manager_recommended_amount' => $clean,
            ]);
        }

        $request->validate([
            'recommendation_status' => 'required|in:Rekomendasi Disetujui,Rekomendasi Ditolak',
            'manager_recommended_amount' => 'required_if:recommendation_status,Rekomendasi Disetujui|numeric|min:1000000',
            'manager_recommended_tenor' => "required_if:recommendation_status,Rekomendasi Disetujui|integer|min:1|max:{$maxTenor}",
            'manager_note'          => 'required|string|min:10',
        ], [
            'manager_recommended_amount.min' => "Batas minimal plafond adalah Rp. 1000.000,00.",
        ]);

        $application->update([
            'manager_id'            => Auth::id(),
            'managed_at'            => now(),
            'recommendation_status' => $request->recommendation_status,
            'manager_recommended_amount' => $request->manager_recommended_amount,
            'manager_recommended_tenor' => $request->manager_recommended_tenor,
            'manager_note'          => $request->manager_note,
        ]);

        return redirect()->route('app.rekomendasi.index')
            ->with('success', 'Rekomendasi berhasil dikirim ke Direktur.');
    }

    public function riwayat(Request $request)
    {
        $query = CreditApplication::with(['nasabahProfile', 'creditFacility'])
            //->where('manager_id', Auth::id())
            ->whereNotNull('recommendation_status');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                    ->orWhereHas('nasabahProfile', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->latest('managed_at')->paginate(10);
        return view('manager.rekomendasi.riwayat', compact('applications'));
    }

    public function detail($id)
    {
        $application = CreditApplication::with([
            'nasabahProfile',
            'creditFacility',
            'detail',
            'collateral',
            'documents'
        ])->findOrFail($id);

        if ($application->manager_id == null) {
            abort(404);
        }

        return view('manager.rekomendasi.detail', compact('application'));
    }
}
