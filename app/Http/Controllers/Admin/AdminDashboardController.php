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

        $data = [
            'user' => $user,
            'totalPengajuan' => CreditApplication::whereNotNull('submitted_at')->count(),
            'menungguVerifikasi' => CreditApplication::where('status', 'Menunggu Verifikasi')->count(),
            'disetujui' => CreditApplication::where('status', 'Disetujui')->count(),
            'ditolak' => CreditApplication::where('status', 'Ditolak')->count(),
        ];

        if (Auth::user()->hasRole('Admin')) {
            return view('admin.index', $data);
        } else if (Auth::user()->hasRole('Manager')) {
            return view('manager.index', $data);
        } else if (Auth::user()->hasRole('Direktur')) {
            $data['totalDisbursed'] = CreditApplication::where('status', 'Disetujui')->sum('jumlah_pinjaman');
            $data['outstanding'] = CreditPayment::where('status_pembayaran', '!=', 'Paid')->sum('tagihan_pokok');
            $data['profitBunga'] = CreditPayment::where('status_pembayaran', 'Paid')->sum('tagihan_bunga');
            return view('direktur.index', $data);
        } else if (Auth::user()->hasRole('Superadmin')) {
            return view('superadmin.index', $data);
        } else {
            return view('admin.index', $data);
        }
    }
}
