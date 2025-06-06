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

// Hitung total data AFRN
$resultTotal = mysqli_query($conn, "SELECT COUNT(*) as total FROM afrn");
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalData = $rowTotal['total'];

// Jumlah data per halaman
$limit = 8;

// Hitung jumlah halaman
$totalPages = ceil($totalData / $limit);

// Ambil halaman sekarang dari query parameter, default 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

// Batasi halaman supaya tidak keluar dari range
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

// Hitung offset data
$offset = ($page - 1) * $limit;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data AFRN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-modify">
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
                    <h2 class="text-xl font-semibold text-gray-700">DAFTAR AFRN</h2>
                    <a href="insert_afrn.php"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">Insert AFRN</a>
                </div>

                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-white text-blue-800 font-normal">
                            <tr>
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">No DO</th>
                                <th class="px-4 py-2">No AFRN</th>
                                <th class="px-4 py-2">Tanggal AFRN</th>
                                <th class="px-4 py-2">Transportir</th>
                                <th class="px-4 py-2">No Polisi</th>
                                <th class="px-4 py-2">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $offset + 1; // nomor urut sesuai halaman

                            $query = "SELECT 
                                a.id_afrn, a.no_afrn, a.tgl_afrn, a.no_bpp, 
                                b.nama_trans, 
                                br.no_polisi, br.volume 
                            FROM afrn a
                            JOIN BRIDGER br ON a.id_bridger = br.id_bridger
                            JOIN TRANSPORTIR b ON br.id_trans = b.id_trans
                            ORDER BY a.id_afrn DESC
                            LIMIT $limit OFFSET $offset";

                            $result = mysqli_query($conn, $query);

                            if(mysqli_num_rows($result) > 0){
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr class='border-t text-center'>";
                                    echo "<td class='px-4 py-2'>{$no}</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_bpp']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_afrn']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['tgl_afrn']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_trans']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_polisi']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['volume']) . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='7' class='py-4 text-center'>Data tidak ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-4 space-x-2">
                    <!-- Prev button -->
                    <a href="?page=<?= max(1, $page - 1) ?>"
                        class="px-3 py-1 border rounded-lg bg-white text-blue-600 hover:bg-blue-600 hover:text-white <?= $page == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= $page == 1 ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                        Prev
                    </a>

                    <?php
                    // Membatasi jumlah tombol halaman yang tampil, misal max 6 tombol
                    $maxLinks = 6;
                    $start = max(1, $page - intval($maxLinks / 2));
                    $end = min($totalPages, $start + $maxLinks - 1);

                    // Adjust start jika end kurang dari maxLinks
                    if ($end - $start + 1 < $maxLinks) {
                        $start = max(1, $end - $maxLinks + 1);
                    }

                    for ($i = $start; $i <= $end; $i++) :
                    ?>
                    <a href="?page=<?= $i ?>"
                        class="px-3 py-1 border rounded-lg <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-600 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>

                    <!-- Next button -->
                    <a href="?page=<?= min($totalPages, $page + 1) ?>"
                        class="px-3 py-1 border rounded-lg bg-white text-blue-600 hover:bg-blue-600 hover:text-white <?= $page == $totalPages ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= $page == $totalPages ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                        Next
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <footer class="text-center py-4 text-sm text-gray-500 mt-auto">
                Copyright © Your Website 2024
            </footer>
        </div>
    </div>
</body>

</html>