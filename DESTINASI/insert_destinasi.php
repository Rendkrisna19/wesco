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

if (isset($_POST['insert'])) {
    $nama = $conn->real_escape_string($_POST['nama_destinasi']);
    $alamat = $conn->real_escape_string($_POST['alamat_destinasi']);

    $conn->query("INSERT INTO destinasi (nama_destinasi, alamat_destinasi) VALUES ('$nama', '$alamat')");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Tambah Destinasi Baru</title>
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

            <!-- Form Section -->
            <div class="flex-1 p-10 bg-white">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Tambah Destinasi Baru</h2>

                <form method="POST" class="bg-white w-full bg-white p-6 rounded shadow-md">

                    <label class="block mb-2">
                        <span class="text-gray-700">Nama Destinasi</span>
                        <input type="text" name="nama_destinasi" required
                            class="mt-1 block w-full border rounded px-3 py-2" />
                    </label>

                    <label class="block mb-4">
                        <span class="text-gray-700">Alamat Destinasi</span>
                        <textarea name="alamat_destinasi" required
                            class="mt-1 block w-full border rounded px-3 py-2"></textarea>
                    </label>

                    <button type="submit" name="insert"
                        class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Submit</button>
                    <a href="index.php" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Batal</a>
                </form>
</body>

</html>