<x-layouts.app :title="'Kartu Angsuran'">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.angsuran.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    Kartu Angsuran: {{ $application->nasabahProfile->nama_lengkap }}
                </h1>
                <p class="font-mono text-xs text-gray-500 tracking-wide">
                    No. PK: {{ $application->no_perjanjian_kredit }}
                </p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success">
            <strong>Information:</strong> {{ session('success') }}
        </x-alert>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Ke</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Tagihan</th>
                        <th class="px-4 py-3">Tgl Bayar</th>
                        <th class="px-4 py-3">Jumlah Bayar</th>
                        <th class="px-4 py-3">Denda</th>
                        <th class="px-4 py-3">Teller Notes</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Reversal Info</th>

                        {{-- KOLOM AKSI: Hanya muncul jika user punya izin Input ATAU Reversal --}}
                        @canany(['update_angsuran', 'reverse_angsuran'])
                            <th class="px-4 py-3 text-center w-32">Aksi</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($payments as $pay)
                        @php
                            // Logic pewarnaan baris
                            $rowClass = $pay->reversal_date
                                ? 'bg-red-50 text-red-800' // Jika dibatalkan
                                : ($pay->status_pembayaran == 'Paid'
                                    ? 'bg-green-50' // Jika lunas
                                    : 'hover:bg-gray-50'); // Normal
                        @endphp

                        <tr class="{{ $rowClass }} transition">
                            <td class="px-4 py-3 font-bold text-center">{{ $pay->angsuran_ke }}</td>
                            <td class="px-4 py-3">{{ $pay->jatuh_tempo->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold">
                                Rp {{ number_format($pay->jumlah_angsuran, 0, ',', '.') }}
                            </td>

                            {{-- Data Pembayaran --}}
                            <td class="px-4 py-3">
                                {{ $pay->tanggal_bayar ? $pay->tanggal_bayar->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $pay->jumlah_bayar > 0 ? 'Rp ' . number_format($pay->jumlah_bayar) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-red-600 font-medium">
                                {{ $pay->denda > 0 ? 'Rp ' . number_format($pay->denda) : '-' }}
                            </td>

                            <td class="px-4 py-3 text-xs max-w-[150px] truncate" title="{{ $pay->catatan_teller }}">
                                {{ $pay->catatan_teller ?? '-' }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3">
                                @if ($pay->reversal_date)
                                    <span
                                        class="bg-red-200 text-red-800 text-[10px] font-bold px-2 py-1 rounded-full uppercase">Dibatalkan</span>
                                @elseif($pay->status_pembayaran == 'Paid')
                                    <span
                                        class="bg-green-200 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full uppercase">Lunas</span>
                                @elseif($pay->status_pembayaran == 'Partial')
                                    <span
                                        class="bg-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded-full uppercase">Nyicil</span>
                                @else
                                    <span
                                        class="bg-gray-200 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase">Tagihan</span>
                                @endif
                            </td>

                            {{-- Info Reversal --}}
                            <td class="px-4 py-3 text-xs">
                                @if ($pay->reversal_date)
                                    <div class="leading-tight">
                                        <div class="font-bold">{{ $pay->reversal_date->format('d/m/y') }}</div>
                                        <div class="italic pb-1">{{ $pay->reversal_note }}</div>
                                        <div class="text-[10px] opacity-75">by {{ $pay->reverser->name ?? 'System' }}
                                        </div>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>


                            {{-- KOLOM AKSI (Unified) --}}
                            @canany(['update_angsuran', 'reverse_angsuran'])
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- 1. TOMBOL INPUT BAYAR (Khusus Admin/User berizin) --}}
                                        @can('update_angsuran')
                                            @if ($pay->status_pembayaran != 'Paid')
                                                <button
                                                    onclick="openPaymentModal({{ $pay->id }}, {{ $pay->angsuran_ke }}, {{ $pay->jumlah_angsuran }})"
                                                    class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition shadow-sm"
                                                    title="Input Pembayaran">
                                                    <i class="fa-solid fa-money-bill-wave"></i>
                                                </button>
                                            @else
                                                <span class="text-green-600 text-xl" title="Sudah Lunas"><i
                                                        class="fa-solid fa-check-circle"></i></span>
                                            @endif
                                        @endcan

                                        {{-- 2. TOMBOL REVERSAL (Khusus Manager/Direktur) --}}
                                        @can('reverse_angsuran')
                                            {{-- Syarat: Sudah bayar (Full/Partial) DAN Belum pernah di-reverse --}}
                                            @if (!$pay->reversal_date && ($pay->status_pembayaran == 'Paid' || $pay->status_pembayaran == 'Partial'))
                                                <button onclick="openReversalModal({{ $pay->id }})"
                                                    class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm"
                                                    title="Batalkan Transaksi (Reversal)">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </button>
                                            @endif
                                        @endcan

                                    </div>
                                </td>
                            @endcanany

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- ================= MODALS ================= --}}

    {{-- MODAL INPUT PEMBAYARAN --}}
    @can('update_angsuran')
        <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePaymentModal()"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-cash-register text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Input Angsuran Ke-<span id="modalAngsuranKe"></span>
                                </h3>

                                <form id="paymentForm" method="POST" class="mt-4 space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Tanggal
                                            Bayar</label>
                                        <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Nominal
                                            Bayar</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>

                                            {{-- 1. INPUT TAMPILAN (User mengetik di sini) --}}
                                            <input type="text" id="visibleJumlahBayar" required
                                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg font-bold text-gray-800"
                                                placeholder="0" oninput="formatRupiah(this)">

                                            {{-- 2. INPUT ASLI (Dikirim ke Controller) --}}
                                            <input type="hidden" name="jumlah_bayar" id="modalJumlahBayar">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Denda
                                            (Opsional)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" name="denda" value="0"
                                                class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg text-red-600">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Catatan
                                            Teller</label>
                                        <textarea name="catatan" rows="2"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-4">
                                        <button type="button" onclick="closePaymentModal()"
                                            class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Batal</button>
                                        <button type="submit"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-lg shadow-blue-200">Simpan
                                            Pembayaran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    {{-- MODAL REVERSAL --}}
    @can('reverse_angsuran')
        <div id="reversalModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeReversalModal()">
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">
                                    Konfirmasi Pembatalan (Reversal)
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin membatalkan transaksi ini?
                                        <br><span class="text-red-600 font-bold text-xs">*Tindakan ini akan tercatat di log
                                            audit.</span>
                                    </p>
                                </div>

                                <form id="reversalForm" method="POST" class="mt-4 space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Tanggal
                                            Reversal</label>
                                        <input type="date" name="reversal_date" value="{{ date('Y-m-d') }}" required
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Alasan Pembatalan
                                            (Wajib)</label>
                                        <textarea name="reversal_note" rows="3" required minlength="5"
                                            placeholder="Contoh: Salah input nominal, Transfer dana fiktif..."
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-4">
                                        <button type="button" onclick="closeReversalModal()"
                                            class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Batalkan</button>
                                        <button type="submit"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 shadow-lg shadow-red-200">Ya,
                                            Reverse Transaksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    @push('scripts')
        <script>
            // ROUTE PLACEHOLDER
            const BASE_UPDATE_URL = "{{ route('app.angsuran.update', ['paymentId' => '__ID__']) }}";
            const BASE_REVERSE_URL = "{{ route('app.angsuran.reverse', ['paymentId' => '__ID__']) }}";

            // --- FUNGSI FORMATTER RUPIAH (SUPPORT DECIMAL) ---
            function formatRupiah(input) {
                // 1. Ambil value asli yang diketik user
                let originalValue = input.value;

                // 2. Bersihkan karakter selain Angka dan Koma (,)
                // Kita izinkan satu koma saja
                let cleanString = originalValue.replace(/[^0-9,]/g, '');

                // 3. Pisahkan Bagian Ribuan (Integer) dan Desimal
                let parts = cleanString.split(',');
                let integerPart = parts[0];
                let decimalPart = parts.length > 1 ? parts[1] : null;

                // Pastikan integer tidak kosong (handling user hapus semua)
                if (integerPart === '') {
                    input.value = '';
                    document.getElementById('modalJumlahBayar').value = '';
                    return;
                }

                // Hapus leading zero (misal 01.000 -> 1.000), kecuali jika angkanya 0
                if (integerPart.length > 1 && integerPart.startsWith('0')) {
                    integerPart = integerPart.substring(1);
                }

                // 4. Format Ribuan menggunakan Regex (Lebih aman drpd Intl untuk string panjang)
                // Ini akan memberi titik setiap 3 digit
                let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                // 5. Susun Nilai untuk Tampilan (Visible) -> Pake Koma
                let displayValue = formattedInteger;

                if (decimalPart !== null) {
                    // Limit desimal maksimal 2 digit (misal: ,50)
                    decimalPart = decimalPart.substring(0, 2);
                    displayValue += ',' + decimalPart;
                } else if (originalValue.indexOf(',') !== -1) {
                    // Kasus User baru mengetik tanda koma "100," (belum ada angkanya)
                    // Kita biarkan komanya muncul agar UX enak
                    displayValue += ',';
                }

                input.value = displayValue;

                // 6. Update Input Hidden (Backend) -> Pake Titik
                // Format DB: 1500000.50
                let backendValue = integerPart;
                if (decimalPart !== null && decimalPart.length > 0) {
                    backendValue += '.' + decimalPart;
                }
                document.getElementById('modalJumlahBayar').value = backendValue;
            }

            // Logic Modal Input Bayar
            @can('update_angsuran')
                function openPaymentModal(id, ke, tagihan) {
                    document.getElementById('paymentModal').classList.remove('hidden');
                    document.getElementById('modalAngsuranKe').innerText = ke;

                    // SET NILAI DEFAULT
                    // 1. Set nilai asli ke hidden input (format titik: 1500.50)
                    document.getElementById('modalJumlahBayar').value = tagihan;

                    // 2. Set nilai terformat ke visible input (format koma: 1.500,50)
                    let visibleInput = document.getElementById('visibleJumlahBayar');

                    // Gunakan Intl NumberFormat bawaan JS untuk format awal load
                    visibleInput.value = new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }).format(tagihan);

                    // Set Action URL
                    document.getElementById('paymentForm').action = BASE_UPDATE_URL.replace('__ID__', id);
                }

                function closePaymentModal() {
                    document.getElementById('paymentModal').classList.add('hidden');
                }
            @endcan

            // Logic Modal Reversal
            @can('reverse_angsuran')
                function openReversalModal(id) {
                    document.getElementById('reversalModal').classList.remove('hidden');
                    document.getElementById('reversalForm').action = BASE_REVERSE_URL.replace('__ID__', id);
                }

                function closeReversalModal() {
                    document.getElementById('reversalModal').classList.add('hidden');
                }
            @endcan

            // Close on Escape Key
            document.onkeydown = function(evt) {
                evt = evt || window.event;
                if (evt.keyCode == 27) {
                    if (document.getElementById('paymentModal')) closePaymentModal();
                    if (document.getElementById('reversalModal')) closeReversalModal();
                }
            };
        </script>
    @endpush
</x-layouts.app>
