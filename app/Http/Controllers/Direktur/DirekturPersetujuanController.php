<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditApplication;
use App\Models\CreditPayment;
use App\Models\CreditFacilityTier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DirekturPersetujuanController extends Controller
{
    public function index()
    {
        $applications = CreditApplication::with(['nasabahProfile', 'user', 'creditFacility'])
            ->where('status', 'Menunggu Verifikasi')
            ->whereNotNull('slik_path')
            ->whereNull('approved_at')
            ->latest('submitted_at')
            ->paginate(10);

        return view('direktur.persetujuan.index', compact('applications'));
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

        return view('direktur.persetujuan.show', compact('application'));
    }

    private function generateNoPK($facilityCode, $date)
    {
        $bulanInput = date('n', strtotime($date)); // Ambil bulan dari input direktur
        $tahunInput = date('Y', strtotime($date)); // Ambil tahun dari input direktur

        $bulanRomawi = $this->getRomawi($bulanInput);
        $formatTengah = "/PK-{$facilityCode}/{$bulanRomawi}/{$tahunInput}";

        $lastApp = CreditApplication::where('no_perjanjian_kredit', 'like', '%' . $formatTengah)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastApp) {
            $lastNo = (int) explode('/', $lastApp->no_perjanjian_kredit)[0];
            $nextNo = $lastNo + 1;
        } else {
            $nextNo = 1;
        }

        return str_pad($nextNo, 3, '0', STR_PAD_LEFT) . $formatTengah;
    }

    private function getRomawi($bulan)
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $map[$bulan];
    }

    public function update(Request $request, $id)
    {
        $application = CreditApplication::findOrFail($id);
        $maxTenor = $application->creditFacility->max_jangka_waktu ?? 60;

        if ($request->filled('final_amount')) {
            $clean = str_replace('.', '', $request->final_amount);
            $clean = str_replace(',', '.', $clean);
            $request->merge(['final_amount' => $clean]);
        }

        $request->validate([
            'decision' => 'required|in:approve,reject',
            'direktur_note' => 'nullable|string',
            'final_amount' => 'required_if:decision,approve|numeric|min:1000000',
            'final_tenor'  => "required_if:decision,approve|numeric|min:1|max:{$maxTenor}",
            'tgl_akad'     => 'required_if:decision,approve|date',
            'jam_akad'     => 'required_if:decision,approve',
        ]);

        DB::beginTransaction();
        try {
            if ($request->decision === 'reject') {
                $application->update([
                    'status'      => 'Ditolak',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            } else {
                $amount = $request->final_amount;
                $tenor  = $request->final_tenor;
                $tglAkad = $request->tgl_akad; // Ambil dari input
                $jamAkad = $request->jam_akad; // Ambil dari input
                $dirkturNote = $request->direktur_note;

                $facilityId = $application->credit_facility_id;

                $tier = CreditFacilityTier::where('credit_facility_id', $facilityId)
                    ->where('min_plafond', '<=', $amount)
                    ->where(function ($q) use ($amount) {
                        $q->where('max_plafond', '>=', $amount)
                            ->orWhereNull('max_plafond');
                    })
                    ->first();

                $bungaPersen = $tier ? $tier->bunga : 1.5;

                $angsuranPokok = $amount / $tenor;
                $angsuranBunga = $amount * ($bungaPersen / 100);
                $totalAngsuran = $angsuranPokok + $angsuranBunga;

                $jatuhTempoAwal = Carbon::parse($tglAkad)->addMonth();

                for ($i = 1; $i <= $tenor; $i++) {
                    CreditPayment::create([
                        'credit_application_id' => $application->id,
                        'angsuran_ke'       => $i,
                        'jatuh_tempo'       => $jatuhTempoAwal->copy()->addMonths($i - 1),
                        'tagihan_pokok'     => $angsuranPokok,
                        'tagihan_bunga'     => $angsuranBunga,
                        'jumlah_angsuran'   => $totalAngsuran,
                        'status_pembayaran' => 'Unpaid'
                    ]);
                }

                $kodeFasilitas = $application->creditFacility->kode ?? 'GEN';
                $noPK = $this->generateNoPK($kodeFasilitas, $tglAkad);

                $application->update([
                    'status'               => 'Disetujui',
                    'approved_by'          => Auth::id(),
                    'approved_at'          => now(),
                    'no_perjanjian_kredit' => $noPK,
                    'tgl_akad'             => $tglAkad, // Simpan Tanggal
                    'jam_akad'             => $jamAkad, // Simpan Jam
                    'recommended_amount'   => $amount,
                    'recommended_tenor'    => $tenor,
                    'direktur_note'        => $dirkturNote,
                ]);
            }

            DB::commit();

            $statusMsg = $request->decision === 'approve' ? 'disetujui' : 'ditolak';
            return redirect()->route('app.persetujuan.index')
                ->with('success', "Pengajuan berhasil $statusMsg");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
