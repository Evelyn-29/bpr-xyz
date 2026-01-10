<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditApplication;
use App\Models\CreditPayment;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $totalPengajuan = CreditApplication::whereNotNull('submitted_at')->count();
        $menungguVerifikasi = CreditApplication::where('status', 'Menunggu Verifikasi')->count();
        $disetujui = CreditApplication::where('status', 'Disetujui')->count();
        $ditolak = CreditApplication::where('status', 'Ditolak')->count();

        $totalDisbursed = CreditApplication::where('status', 'Disetujui')->sum('jumlah_pinjaman');
        $outstanding = CreditPayment::where('status_pembayaran', '!=', 'Paid')->sum('tagihan_pokok');
        $profitBunga = CreditPayment::where('status_pembayaran', 'Paid')->sum('tagihan_bunga');

        if (Auth::user()->hasRole('Admin')) {
            return view('admin.index', compact(
                'user',
                'totalPengajuan',
                'menungguVerifikasi',
                'disetujui',
                'ditolak'
            ));
        } else if (Auth::user()->hasRole('Manager')) {
            return view('manager.index', compact(
                'user',
                'totalPengajuan',
                'menungguVerifikasi',
                'disetujui',
                'ditolak'
            ));
        } else if (Auth::user()->hasRole('Direktur')) {
            return view('direktur.index', compact(
                'user',
                'totalPengajuan',
                'menungguVerifikasi',
                'disetujui',
                'ditolak',
                'totalDisbursed',
                'outstanding',
                'profitBunga'
            ));
        } else if (Auth::user()->hasRole('Superadmin')) {
            return view('superadmin.index');
        }
    }
}
