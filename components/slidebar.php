<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sidebar Pertamina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

.font-modify {
    font-family: "Poppins", sans-serif;
}
</style>

<body class="flex bg-gray-100 font-modify">

    <!-- Sidebar -->
    <div class="w-64 min-h-screen bg-white shadow-lg flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 px-6 py-4 border-b">
                <img src="../image/pertamina.jpg" alt="Logo" class="h-28 w-full object-cover" />
            </div>

            <nav class="flex flex-col py-4 space-y-1 text-sm">
                <!-- Active Link -->
                <a href="../home/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-blue-600 bg-blue-50 font-semibold">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    Dashboard
                    <span class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500"></span>
                </a>

                <!-- Other Links -->
                <a href="../AFRN/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="table" class="w-5 h-5"></i>
                    AFRN
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../BON/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    Bon
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../TANGKY DELIVERY/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Tangki Delivery
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../BIRDGER/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Bridger
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../SALIB UKUR/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="scale" class="w-5 h-5"></i>
                    Salib Ukur
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../CETAK REPORT/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    Cetak Report
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../TRASNPORTIR/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="ship" class="w-5 h-5"></i>
                    Transportir
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../DESTINASI/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                    Destinasi
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../ADMIN/dashboard.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    Admin
                    <span
                        class="absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>
            </nav>
        </div>

        <!-- Footer -->
        <footer class="px-6 py-3 text-center text-xs text-gray-400 border-t">
            © 2025 Pertamina. All rights reserved.
        </footer>
    </div>

    <!-- Aktifkan Lucide Icons -->
    <script>
    lucide.createIcons();
    </script>
</body>

</html>