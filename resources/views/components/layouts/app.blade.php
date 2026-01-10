<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BPR System' }} | BPR XYZ</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- CUSTOM SCROLLBAR STYLE --}}
    <style>
        /* Untuk Chrome, Edge, dan Safari */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px; /* Lebar scrollbar sangat tipis */
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent; /* Latar belakang track transparan */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155; /* Warna thumb (Slate-700) - Abu gelap elegan */
            border-radius: 20px; /* Membuat ujungnya bulat */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #475569; /* Warna saat di-hover (Slate-600) - Sedikit lebih terang */
        }

        /* Untuk Firefox */
        .custom-scrollbar {
            scrollbar-width: thin; /* Ukuran tipis */
            scrollbar-color: #334155 transparent; /* Warna thumb & track */
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    {{-- OVERLAY MOBILE --}}
    <div id="sidebarOverlay"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden transition-opacity opacity-0 md:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed top-0 left-0 z-50 h-screen w-64 bg-[#0d1b2a] text-white transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 flex flex-col shadow-xl">

        {{-- HEADER SIDEBAR --}}
        <div
            class="h-16 flex items-center justify-center border-b border-gray-800 px-6 gap-3 bg-[#0d1b2a] flex-shrink-0">
            <div class="text-left">
                <h2 class="text-base font-bold leading-tight tracking-wider text-yellow-500">BPR XYZ</h2>
            </div>
            <button id="closeSidebar" class="md:hidden ml-auto text-gray-400 hover:text-white">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        {{-- MENU NAVIGATION (Ditambah class custom-scrollbar) --}}
        <nav class="mt-6 px-3 space-y-1 flex-1 overflow-y-auto custom-scrollbar mb-6">

            {{-- 1. DASHBOARD (Unified Route) --}}
            <a href="{{ route('app.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.dashboard') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-house w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Beranda</span>
            </a>

            {{-- 2. PENGAJUAN / REKOMENDASI / PERSETUJUAN --}}

            <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Transaksi</div>

            {{-- 2.A. ADMIN: Pengajuan Kredit --}}
            @can('view_pengajuan')
                <a href="{{ route('app.pengajuan.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.pengajuan.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Pengajuan Kredit</span>
                </a>
            @endcan

            {{-- 3. UPLOAD SLIK (Khusus Admin) --}}
            @can('view_slik')
                <a href="{{ route('app.slik.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.slik.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-upload w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Upload SLIK</span>
                </a>
            @endcan

            {{-- 2.B. MANAGER: Rekomendasi Kredit --}}
            @can('view_rekomendasi')
                <a href="{{ route('app.rekomendasi.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.rekomendasi.index', 'app.rekomendasi.show') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Antrian Rekomendasi</span>
                </a>
            @endcan

            @can('history_rekomendasi')
                <a href="{{ route('app.rekomendasi.riwayat') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.rekomendasi.riwayat', 'app.rekomendasi.detail') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-check w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Riwayat Rekomendasi</span>
                </a>
            @endcan

            {{-- 2.C. DIREKTUR: Persetujuan Kredit --}}
            @can('view_persetujuan')
                <a href="{{ route('app.persetujuan.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.persetujuan.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-file-signature w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Persetujuan Kredit</span>
                </a>
            @endcan

            {{-- 4. ANGSURAN (Admin, Manager, Direktur) --}}
            @can('view_angsuran')
                <a href="{{ route('app.angsuran.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('*.angsuran.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                    <i class="fa-solid fa-clock-rotate-left w-6 text-center mr-2 text-sm"></i>
                    <span class="font-medium">Kredit & Angsuran</span>
                </a>
            @endcan


            {{-- 5. LAPORAN (Dropdown) --}}
            @canany(['view_laporan_persetujuan_kredit', 'view_laporan_analisa_kredit',
                'view_laporan_monitoring_angsuran', 'view_laporan_realisasi_pinjaman', 'view_laporan_rekapitulasi'])
                @php
                    $isLaporanActive = request()->routeIs('*.laporan.*');
                @endphp
                <div>
                    <button type="button" id="laporanBtn"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors group {{ $isLaporanActive ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chart-line w-6 text-center mr-2 text-sm"></i>
                            <span class="font-medium">Laporan</span>
                        </div>
                        <i id="laporanArrow"
                            class="fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $isLaporanActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="laporanMenu" class="space-y-1 mt-1 {{ $isLaporanActive ? 'block' : 'hidden' }}">

                        @can('view_laporan_persetujuan_kredit')
                            <a href="{{ route('app.laporan.pengajuan') }}"
                                class="block pl-12 pr-4 py-2 rounded-lg transition-colors {{ request()->routeIs('*.laporan.pengajuan') ? 'text-white bg-[#26354f]' : 'text-gray-400 hover:text-white hover:bg-[#26354f]' }}">
                                Persetujuan Kredit
                            </a>
                        @endcan

                        @can('view_laporan_analisa_kredit')
                            <a href="{{ route('app.laporan.analisis') }}"
                                class="block pl-12 pr-4 py-2 rounded-lg transition-colors {{ request()->routeIs('*.laporan.analisis') ? 'text-white bg-[#26354f]' : 'text-gray-400 hover:text-white hover:bg-[#26354f]' }}">
                                Analisis Kredit
                            </a>
                        @endcan

                        @can('view_laporan_monitoring_angsuran')
                            <a href="{{ route('app.laporan.monitoring') }}"
                                class="block pl-12 pr-4 py-2 rounded-lg transition-colors {{ request()->routeIs('*.laporan.monitoring') ? 'text-white bg-[#26354f]' : 'text-gray-400 hover:text-white hover:bg-[#26354f]' }}">
                                Monitoring Angsuran
                            </a>
                        @endcan

                        @can('view_laporan_realisasi_pinjaman')
                            <a href="{{ route('app.laporan.realisasi') }}"
                                class="block pl-12 pr-4 py-2 rounded-lg transition-colors {{ request()->routeIs('*.laporan.realisasi') ? 'text-white bg-[#26354f]' : 'text-gray-400 hover:text-white hover:bg-[#26354f]' }}">
                                Realisasi Pinjaman
                            </a>
                        @endcan

                        @can('view_laporan_rekapitulasi')
                            <a href="{{ route('app.laporan.rekapitulasi') }}"
                                class="block pl-12 pr-4 py-2 rounded-lg transition-colors {{ request()->routeIs('*.laporan.rekapitulasi') ? 'text-white bg-[#26354f]' : 'text-gray-400 hover:text-white hover:bg-[#26354f]' }}">
                                Rekapitulasi
                            </a>
                        @endcan

                    </div>
                </div>
            @endcanany

            {{-- 6. MASTER DATA & SETTING --}}
            @canany(['view_master_produk', 'manage_roles'])
                <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Master Data & Setting
                </div>

                @can('view_master_produk')
                    <a href="{{ route('app.products.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.products.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                        <i class="fa-solid fa-boxes-stacked w-6 text-center mr-2 text-sm"></i>
                        <span class="font-medium">Fasilitas Kredit</span>
                    </a>
                @endcan
                @can('view_nasabah')
                    <a href="{{ route('app.nasabah.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.nasabah.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                        <i class="fa-solid fa-users w-6 text-center mr-2 text-sm"></i>
                        <span class="font-medium">Nasabah</span>
                    </a>
                @endcan
                @can('view_users')
                    <a href="{{ route('app.users.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.users.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                        <i class="fa-solid fa-user-tie w-6 text-center mr-2 text-sm"></i>
                        <span class="font-medium">Pengguna</span>
                    </a>
                @endcan
                @can('manage_roles')
                    <a href="{{ route('app.roles.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('app.roles.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                        <i class="fa-solid fa-user-shield w-6 text-center mr-2 text-sm"></i>
                        <span class="font-medium">Role & Permission</span>
                    </a>
                @endcan
            @endcanany

        </nav>

    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="flex flex-col min-h-screen md:ml-64 transition-all duration-300">

        {{-- TOP HEADER --}}
        <header class="sticky top-0 z-30 bg-white shadow-sm h-16 flex items-center justify-between px-4 sm:px-6">

            <div class="flex items-center gap-4">
                {{-- Hamburger --}}
                <button id="hamburgerBtn"
                    class="p-2 -ml-2 text-gray-600 rounded-md md:hidden hover:bg-gray-100 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Page Title --}}
                <div class="text-base md:text-lg font-semibold text-gray-800 truncate max-w-[180px] sm:max-w-none">
                    {{ $header ?? ($title ?? 'Dashboard') }}
                </div>
            </div>

            {{-- PROFILE DROPDOWN --}}
            <div class="relative">
                <button id="dropdownButton"
                    class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-full hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500">

                    <div
                        class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>

                    <span class="hidden sm:block text-sm font-medium text-gray-700 truncate max-w-[100px]">
                        {{ Auth::user()->name }}
                    </span>

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="dropdownMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl py-2 z-50 transform origin-top-right transition-all">
                    <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                        <p class="text-xs text-gray-500">Login sebagai</p>
                        <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                        <i class="fa-solid fa-user mr-2 w-4"></i> Edit Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

        {{-- FOOTER --}}
        <footer class="p-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} BPR XYZ. All rights reserved.
        </footer>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- DROPDOWN PROFIL ---
            const dropdownBtn = document.getElementById('dropdownButton');
            const dropdownMenu = document.getElementById('dropdownMenu');

            if (dropdownBtn) {
                dropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });
            }

            // --- SIDEBAR MOBILE ---
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const closeSidebarBtn = document.getElementById('closeSidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => sidebarOverlay.classList.remove('opacity-0'), 10);
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
            }

            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openSidebar();
                });
            }

            if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
            if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);

            // --- CLOSE ON OUTSIDE CLICK ---
            window.addEventListener('click', (e) => {
                if (dropdownBtn && !dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });

            // --- DROPDOWN LAPORAN ---
            const laporanBtn = document.getElementById('laporanBtn');
            const laporanMenu = document.getElementById('laporanMenu');
            const laporanArrow = document.getElementById('laporanArrow');

            if (laporanBtn) {
                laporanBtn.addEventListener('click', () => {
                    laporanMenu.classList.toggle('hidden');
                    laporanArrow.classList.toggle('rotate-180');
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>