<?php
session_start(); 


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
// Assuming nama_lengkap is also set in session from login
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
include '../config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body class="bg-white font-modify">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
            <!-- Header -->
            <div class="p-8">
                <h1 class="text-xl font-semibold text-gray-700">DAFTAR PENGGUNA</h1>
            </div>
            <div class="mt-6 flex justify-end">
                <a href="./index.php"
                    class="px-5 py-2 bg-blue-700 text-white rounded-lg shadow hover:bg-blue-800">Insert User</a>
            </div>
            <!-- Table Section -->
            <div class="px-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full table-auto text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-700">No</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Nama</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Tanggal Lahir</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Username</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Role</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Data baris user, bisa di-generate dari backend -->
                            <tr>
                                <td class="px-6 py-4">1</td>
                                <td class="px-6 py-4">Suryaa</td>
                                <td class="px-6 py-4">1990-01-01</td>
                                <td class="px-6 py-4">-</td>
                                <td class="px-6 py-4">Driver</td>
                                <td class="px-6 py-4">
                                    <button
                                        class="px-4 py-2 border border-black rounded-full hover:bg-gray-100">Edit</button>
                                </td>
                            </tr>
                            <!-- Tambahkan baris lain sesuai data -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-4 flex justify-center items-center space-x-2">
                    <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
                    <button class="px-3 py-1 bg-white border rounded">2</button>
                    <button class="px-3 py-1 bg-white border rounded">Next</button>
                </div>
                <!-- Insert User Button -->

            </div>
            <!-- Footer -->
            <footer class="text-center text-gray-500 text-sm mt-10 mb-4">
                Copyright © Your Website 2024
            </footer>
        </div>
    </div>
</body>

</html>