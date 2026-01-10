<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Nasabah' }} | BPR XYZ</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- CUSTOM SCROLLBAR STYLE --}}
    <style>
        /* Untuk Chrome, Edge, dan Safari */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 20px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #475569;
        }

        /* Untuk Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
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
        <div class="h-16 flex items-center justify-center border-b border-gray-800 px-6 gap-3 bg-[#0d1b2a] flex-shrink-0">
            <div class="text-left">
                <h2 class="text-base font-bold leading-tight tracking-wider text-yellow-500">BPR XYZ</h2>
            </div>
            <button id="closeSidebar" class="md:hidden ml-auto text-gray-400 hover:text-white">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        {{-- MENU NAVIGATION --}}
        <nav class="mt-6 px-2 space-y-1 flex-1 overflow-y-auto custom-scrollbar">
            
            {{-- 1. BERANDA --}}
            <a href="{{ route('nasabah.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('nasabah.dashboard') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-house w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Beranda</span>
            </a>

            {{-- 2. PENGAJUAN KREDIT --}}
            <a href="{{ route('nasabah.pengajuan.step1') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('nasabah.pengajuan.*') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-file-invoice-dollar w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Pengajuan Kredit</span>
            </a>

            {{-- 3. STATUS PENGAJUAN --}}
            <a href="{{ route('nasabah.riwayat.index') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('nasabah.riwayat.index', 'nasabah.riwayat.show') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-clock-rotate-left w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Status Pengajuan</span>
            </a>

            {{-- 4. PINJAMAN AKTIF --}}
            <a href="{{ route('nasabah.riwayat.aktif') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('nasabah.riwayat.aktif') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-calendar-check w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Pinjaman Aktif</span>
            </a>

            {{-- 5. SIMULASI KREDIT --}}
            <a href="{{ route('nasabah.simulasi.index') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('nasabah.simulasi.index') ? 'bg-[#1b263b] text-white border-l-4 border-yellow-500' : 'text-gray-300 hover:bg-[#1b263b] hover:text-white' }}">
                <i class="fa-solid fa-calculator w-6 text-center mr-2 text-sm"></i>
                <span class="font-medium">Simulasi Kredit</span>
            </a>

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
                    {{ $header ?? ($title ?? 'Dashboard Nasabah') }}
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
                    {{-- Ganti route ini sesuai route profile edit nasabah kamu --}}
                    <a href="{{ route('nasabah.profile-edit') }}"
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

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

        <footer class="p-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} BPR XYZ. All rights reserved.
        </footer>
    </div>

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
        });
    </script>
    @stack('scripts')

</body>

</html>