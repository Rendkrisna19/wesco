<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pertamina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

.font-modify {
    font-family: "Poppins", sans-serif;
}
</style>

<body class="flex bg-gray-100 font-modify">

    <div class="w-64 min-h-screen bg-white shadow-lg flex flex-col">
        <div>
            <div class="flex items-center gap-2 px-6 py-4 border-b">
                <img src="../image/pertamina.jpg" alt="Logo" class="h-28 w-full object-cover" />
            </div>

            <nav id="sidebar-nav" class="flex flex-col py-4 space-y-1 text-sm">
                <a href="../home/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    Dashboard
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../AFRN/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="table" class="w-5 h-5"></i>
                    AFRN
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../BON/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    Bon
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../TANGKY DELIVERY/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Tangki Delivery
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../BIRDGER/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    Bridger
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../SALIB UKUR/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="scale" class="w-5 h-5"></i>
                    Salib Ukur
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../CETAK REPORT/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    Cetak Report
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../TRASNPORTIR/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="ship" class="w-5 h-5"></i>
                    Transportir
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../DESTINASI/index.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                    Destinasi
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>

                <a href="../ADMIN/dashboard.php"
                    class="group relative flex items-center gap-3 px-6 py-3 text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    Admin
                    <span
                        class="underline-span absolute bottom-0 left-6 right-6 h-0.5 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                </a>
            </nav>
        </div>

    </div>

    <script>
    lucide.createIcons();
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Dapatkan path URL saat ini
        const currentPath = window.location.pathname;

        // Dapatkan semua link di dalam navigasi sidebar
        const navLinks = document.querySelectorAll('#sidebar-nav a');

        navLinks.forEach(link => {
            // Dapatkan path dari atribut href setiap link
            const linkPath = new URL(link.href).pathname;

            // Cek apakah path URL saat ini mengandung path dari link
            // Ini membuatnya lebih fleksibel, misal: /AFRN/ akan cocok dengan /AFRN/index.php
            if (currentPath.includes(linkPath)) {
                // Hapus kelas default dan hover
                link.classList.remove('text-gray-500', 'hover:text-blue-600', 'hover:bg-gray-50');

                // Tambahkan kelas 'active'
                link.classList.add('text-blue-600', 'bg-blue-50', 'font-semibold');

                // Dapatkan elemen span untuk garis bawah
                const underline = link.querySelector('.underline-span');
                if (underline) {
                    // Hapus kelas animasi hover dan buat garis bawah selalu terlihat
                    underline.classList.remove('scale-x-0', 'group-hover:scale-x-100');
                    underline.classList.add('scale-x-100');
                }
            }
        });
    });
    </script>

</body>

</html>