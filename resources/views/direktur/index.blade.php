<x-layouts.app>
    <x-slot name="header">
        <h1 class="font-bold text-xl">Welcome, {{ Auth::user()->name }}!</h1>
    </x-slot>

    {{-- Konten utama --}}
    <section id="home" class="mb-8">
        @if (session('success'))
            <x-alert type="success">
                <strong>Information:</strong> {{ session('success') }}
            </x-alert>
        @endif

        @if (session('warning'))
            <x-alert type="warning">
                <strong>Peringatan:</strong> {{ session('warning') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error">
                <strong>Error:</strong> {{ session('error') }}
            </x-alert>
        @endif

        {{-- Grid Stats --}}
        {{-- Ubah grid menjadi 4 kolom di layar besar (lg) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Pengajuan</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalPengajuan }}</p>
                    </div>
                    <div class="flex items-center justify-center bg-blue-100 w-12 h-12 rounded-full">
                        <i class="fa-solid fa-file-invoice text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- 2. MENUNGGU VERIFIKASI --}}
            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all border-l-4 border-yellow-500">
                <a href="{{ route('app.persetujuan.index') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Menunggu Verifikasi</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ $menungguVerifikasi }}</p>
                        </div>
                        <div class="flex items-center justify-center bg-yellow-100 w-12 h-12 rounded-full">
                            <i class="fa-solid fa-hourglass-half text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            {{-- 3. DISETUJUI --}}
            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all border-l-4 border-green-500">
                <a href="{{ route('app.angsuran.index') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Disetujui</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ $disetujui }}</p>
                        </div>
                        <div class="flex items-center justify-center bg-green-100 w-12 h-12 rounded-full">
                            <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            {{-- 4. DITOLAK --}}
            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Ditolak</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $ditolak }}</p>
                    </div>
                    <div class="flex items-center justify-center bg-red-100 w-12 h-12 rounded-full">
                        <i class="fa-solid fa-circle-xmark text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-white p-8 rounded-2xl mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="border p-4 rounded-xl bg-blue-50">
                    <h3 class="text-sm font-bold text-blue-800 uppercase">Total Penyaluran (Disbursed)</h3>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                        {{ number_format($totalDisbursed, 0, ',', '.') }}
                    </p>
                </div>
                <div class="border p-4 rounded-xl bg-red-50">
                    <h3 class="text-sm font-bold text-red-800 uppercase">Outstanding (Sisa Pokok)</h3>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($outstanding, 0, ',', '.') }}
                    </p>
                </div>
                <div class="border p-4 rounded-xl bg-green-50">
                    <h3 class="text-sm font-bold text-green-800 uppercase">Pendapatan Bunga</h3>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($profitBunga, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
