<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditApplication;
use App\Models\CreditPayment;

class AdminAngsuranController extends Controller
{
    /**
     * List Kredit Aktif (Yang sudah disetujui dan belum lunas)
     */
    public function index(Request $request)
    {
        // Cari aplikasi yang statusnya Disetujui (Kredit Berjalan)
        $query = CreditApplication::with(['nasabahProfile', 'creditFacility'])
            ->where('status', 'Disetujui')
            ->withCount(['payments as sudah_bayar' => function ($q) {
                $q->where('status_pembayaran', 'Paid');
            }]);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('no_pengajuan', 'like', "%{$search}%")
                ->orWhereHas('nasabahProfile', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%");
                });
        }

        $credits = $query->latest('approved_at')->paginate(10);

        return view('admin.angsuran.index', compact('credits'));
    }

    /**
     * Detail Kartu Angsuran (List Tagihan Nasabah)
     */
    public function show($id)
    {
        $application = CreditApplication::with(['nasabahProfile', 'creditFacility'])->findOrFail($id);

        // Ambil jadwal pembayaran
        $payments = CreditPayment::where('credit_application_id', $id)
            ->orderBy('angsuran_ke', 'asc')
            ->get();

        return view('admin.angsuran.show', compact('application', 'payments'));
    }

    /**
     * Proses Input Pembayaran (Form Action)
     */
    public function update(Request $request, $paymentId)
    {
        //dd($request->all());
        $payment = CreditPayment::findOrFail($paymentId);

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required|numeric|min:1',
            'denda'         => 'nullable|numeric|min:0',
            'catatan'       => 'nullable|string',
        ]);

        // Logic Status
        $totalTagihan = $payment->jumlah_angsuran + ($request->denda ?? 0);
        $totalBayar = $request->jumlah_bayar;

        if ($totalBayar >= $totalTagihan) {
            $status = 'Paid'; // Lunas
        } elseif ($totalBayar > 0) {
            $status = 'Partial'; // Nyicil
        } else {
            $status = 'Unpaid';
        }

        $payment->update([
            'tanggal_bayar'     => $request->tanggal_bayar,
            'jumlah_bayar'      => $totalBayar,
            'denda'             => $request->denda ?? 0,
            'status_pembayaran' => $status,
            'catatan_teller'    => $request->catatan,
            'bukti_bayar'       => 'Manual by Admin',
            'reversal_date'     => null,
            'reversal_note'     => null,
            'reversal_user_id'  => null,
        ]);

        $application = $payment->application;
        
        // Hitung ada berapa angsuran yang BELUM LUNAS (Status != Paid)
        $sisaTagihan = $application->payments()
            ->where('status_pembayaran', '!=', 'Paid')
            ->count();

        if ($sisaTagihan == 0) {
            // Jika sisa 0, berarti SEMUA sudah Paid.
            // Ubah status aplikasi utama menjadi 'Lunas'
            $application->update([
                'status' => 'Lunas'
            ]);

            return back()->with('success', 'Pembayaran diterima. SELAMAT! Kredit nasabah ini telah LUNAS.');
        }

        return back()->with('success', 'Pembayaran angsuran ke-' . $payment->angsuran_ke . ' berhasil dicatat.');
    }
}
