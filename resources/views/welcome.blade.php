<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPR XYZ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="bg-[#0d1b2a] text-white shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <div class="flex items-center space-x-3">
                <h1 class="text-lg md:text-xl font-semibold tracking-wide">
                    BPR XYZ
                </h1>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}"
                    class="bg-yellow-400 text-[#0d1b2a] px-4 py-2 rounded-lg font-medium hover:bg-yellow-300 transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="border border-yellow-400 text-yellow-400 px-4 py-2 rounded-lg font-medium hover:bg-yellow-400 hover:text-[#0d1b2a] transition">
                    Register
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-[#0d1b2a] via-[#1b263b] to-[#415a77] text-white py-20">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                    Selamat Datang di <span class="text-yellow-400">BPR XYZ</span>
                </h2>
                <p class="text-lg text-gray-300 mb-6">
                    Sahabat Keuangan Terpercaya untuk Mewujudkan Impian Anda. Kami hadir membantu Anda dengan layanan
                    tabungan, deposito, dan kredit terpercaya. Nikmati kemudahan <span class="font-semibold">pengajuan
                        kredit dan pemantauan angsuran</span> secara digital dengan cepat, aman, dan transparan.
                </p>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-3xl font-bold text-[#0d1b2a] mb-8">Layanan Kami</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h4 class="text-xl font-semibold mb-2 text-[#0d1b2a]">Deposito</h4>
                    <p class="text-gray-600">Investasikan dana Anda dengan bunga tinggi dan jaminan keamanan terbaik.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h4 class="text-xl font-semibold mb-2 text-[#0d1b2a]">Tabungan</h4>
                    <p class="text-gray-600">Nikmati kemudahan menabung dengan bunga kompetitif dan akses cepat.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h4 class="text-xl font-semibold mb-2 text-[#0d1b2a]">Kredit & Angsuran</h4>
                    <p class="text-gray-600">Ajukan kredit dan pantau angsuran Anda secara real-time melalui platform
                        digital kami.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0d1b2a] text-white py-6 mt-12">
        <div class="container mx-auto text-center text-sm">
            <p>&copy; {{ date('Y') }}
                <span class="text-yellow-400 font-semibold">BPR XYZ</span>.
            </p>
            <p class="text-gray-400 text-xs mt-2 italic">
                “Melayani dengan Integritas dan Kepercayaan.”
            </p>
        </div>
    </footer>

</body>

</html>
