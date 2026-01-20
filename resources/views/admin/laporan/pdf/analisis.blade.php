<!DOCTYPE html>
<html>

<head>
    <title>Laporan Analisis Kredit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            color: #555;
        }

        .sub-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .sub-header h2 {
            font-size: 14px;
            text-decoration: underline;
            margin: 0 0 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right !important;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

    {{-- KOP LAPORAN --}}
    <div class="header">
        <h1>BPR XYZ</h1>


    </div>

    {{-- JUDUL & PERIODE --}}
    <div class="sub-header">
        <h2>LAPORAN ANALISIS KREDIT</h2>
        <p>Periode pengajuan: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Awal' }} s/d
            {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Sekarang' }}</p>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tgl Masuk</th>
                <th width="10%">No. Pengajuan</th>
                <th width="10%">Nasabah</th>
                <th width="15%">Hasil SLIK</th>
                <th width="15%">Rekomendasi Manager</th>
                <th width="10%">Catatan Manager</th>
                <th width="10%">Catatan Direktur</th>
                <th width="10%">Plf Disetujui</th>
                <th width="10%">Keputusan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $index => $app)
                <tr>
                    <td style="text-align:center">{{ $index + 1 }}</td>
                    <td>{{ $app->submitted_at->format('d/m/Y') }}</td>
                    <td>{{ $app->no_pengajuan }}</td>
                    <td>{{ $app->nasabahProfile->nama_lengkap }}</td>
                    <td>
                        <strong>{{ $app->slik_status ?? 'Belum' }}</strong><br>
                        <i style="font-size: 9px">{{ Str::limit($app->slik_notes, 50) }}</i>
                    </td>
                    <td>
                        @if ($app->recommendation_status)
                            {{ $app->recommendation_status }}<br>
                            @if ($app->recommendation_status == 'Rekomendasi Disetujui')
                                <span style="color:green">Rp
                                    {{ number_format($app->manager_recommended_amount) }}</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $app->manager_note ?? '-' }}</td>
                    <td>{{ $app->direktur_note ?? '-' }}</td>
                    <td>{{ number_format($app->recommended_amount) }}</td>
                    <td style="text-align:center; font-weight:bold">{{ $app->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <div class="footer">
        {{-- Tanggal Cetak --}}
        <p>Bandung, {{ date('d F Y') }}</p>

        <p>Dicetak oleh,</p>

        {{-- Space untuk Tanda Tangan --}}
        <br><br><br>

        {{-- Nama User Login --}}
        <p style="text-decoration: underline; font-weight: bold;">
            {{ Auth::user()->name }}
        </p>

        {{-- Role User (Opsional: Biar tau jabatannya) --}}
        <p>
            ( {{ Auth::user()->getRoleNames()->first() ?? 'Admin Operasional' }} )
        </p>
    </div>

</body>

</html>
