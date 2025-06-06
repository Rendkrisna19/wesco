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
// Ambil data AFRN dan join dengan tabel destinasi
$sql = "SELECT afrn.no_afrn, afrn.tgl_afrn, afrn.no_bpp, destinasi.nama_destinasi
        FROM afrn
        LEFT JOIN destinasi ON afrn.id_destinasi = destinasi.id_destinasi";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data AFRN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <!-- Main Content -->
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

            <!-- Page Content -->
            <div class="p-6 flex-1 overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex-1 flex flex-col">
                        <h2 class="text-xl text-gray-700 font-semibold mb-4">CETAK REPORT AFRN</h2>

                        <div class="bg-white shadow-md rounded-lg p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm text-left text-gray-700">
                                    <thead class="bg-white text-blue-700 font-bold border-b-2">
                                        <tr>
                                            <th class="px-4 py-2">No</th>
                                            <th class="px-4 py-2">AFRN</th>
                                            <th class="px-4 py-2">Tanggal AFRN</th>
                                            <th class="px-4 py-2">Location</th>
                                            <th class="px-4 py-2">Destination</th>
                                            <th class="px-4 py-2">Vehicle Type</th>
                                            <th class="px-4 py-2">QC</th>
                                            <th class="px-4 py-2">BPP/NPP</th>
                                            <th class="px-4 py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='border-b'>";
                        echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_afrn']) . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['tgl_afrn']) . "</td>";
                        echo "<td class='px-4 py-2'>Lokasi</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_destinasi']) . "</td>";
                        echo "<td class='px-4 py-2'>Bridger</td>";
                        echo "<td class='px-4 py-2'>QC</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_bpp']) . "</td>";
echo "<td class='px-4 py-2'>
    <a href='cetak_report.php?no_afrn=" . urlencode($row['no_afrn']) . "' target='_blank'>
        <button class='bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition font-semibold'>
            Cetak
        </button>
    </a>
</td>";
                        echo "</tr>";
                    }
                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
</body>

</html>