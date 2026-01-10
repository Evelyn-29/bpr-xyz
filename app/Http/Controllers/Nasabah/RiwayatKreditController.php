<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditApplication;

class RiwayatKreditController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $applications = CreditApplication::with('creditFacility')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('nasabah.riwayat.index', compact('applications'));
    }

    public function show($id)
    {
        $application = CreditApplication::with([
            'nasabahProfile',
            'creditFacility',
            'detail',
            'collateral',
            'documents'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('nasabah.riwayat.show', compact('application'));
    }

    public function aktif()
    {
        $application = CreditApplication::with([
            'nasabahProfile',
            'creditFacility',
            'detail',
            'collateral',
            'documents',
            'payments'
        ])
            ->where('user_id', Auth::id())
            ->where('status', 'Disetujui')
            ->latest('approved_at')
            ->first();

        if (!$application) {
            return redirect()->route('nasabah.riwayat.index')
                ->with('error', 'Anda tidak memiliki pinjaman yang sedang aktif (Disetujui).');
        }

        return view('nasabah.riwayat.show', compact('application'));
    }
}
