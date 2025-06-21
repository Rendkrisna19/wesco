<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

include '../config/koneksi.php';

// --- BAGIAN BARU: PENGATURAN PAGINATION ---
$records_per_page = 10; // Data per halaman

// Tentukan halaman saat ini dari URL, default ke halaman 1 jika tidak ada
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// Hitung total data untuk mengetahui jumlah halaman
$total_records_sql = "SELECT COUNT(*) FROM afrn";
$total_result = $conn->query($total_records_sql);
$total_records = $total_result->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);

// Jika halaman yang diminta melebihi total halaman, arahkan ke halaman terakhir
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Hitung OFFSET untuk query SQL
$offset = ($current_page - 1) * $records_per_page;
// --- AKHIR BAGIAN BARU ---


$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
// Assuming nama_lengkap is also set in session from login
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;

// PERUBAHAN PADA QUERY: Ditambahkan ORDER BY, LIMIT, dan OFFSET
$sql = "SELECT afrn.no_afrn, afrn.tgl_afrn, afrn.no_bpp, destinasi.nama_destinasi
        FROM afrn
        LEFT JOIN destinasi ON afrn.id_destinasi = destinasi.id_destinasi
        ORDER BY afrn.tgl_afrn DESC, afrn.no_afrn DESC
        LIMIT ? OFFSET ?";

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
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
        <div class="w-64 bg-white shadow-lg">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <div class="flex-1 flex flex-col">
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="relative group">
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                        <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                            alt="User" class="w-8 h-8 rounded-full">
                    </div>

                    <div
                        class="absolute hidden group-hover:block right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10 ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="../auth/index.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-cyan-700 hover:text-white">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 flex-1 overflow-y-auto">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-xl text-gray-700 font-semibold mb-4">CETAK REPORT AFRN</h2>
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
                                // PERUBAHAN PADA PENOMORAN: Disesuaikan dengan halaman
                                $no = $offset + 1;
                                // KODE DI DALAM WHILE LOOP DIKEMBALIKAN SEPERTI ASLI
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

                    <?php if ($total_pages > 1): ?>
                    <div class="mt-6 flex justify-center items-center">
                        <div class="flex items-center gap-2">
                            <?php if ($current_page > 1): ?>
                            <a href="?page=<?= $current_page - 1 ?>"
                                class="px-4 py-2 bg-white border border-blue-500 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-colors duration-200">Prev</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>"
                                class="px-4 py-2 border rounded-lg transition-colors duration-200 
                                    <?= ($i == $current_page) ? 'bg-blue-500 text-white border-blue-500' : 'bg-white border-gray-300 text-gray-700 hover:bg-blue-50' ?>">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?= $current_page + 1 ?>"
                                class="px-4 py-2 bg-white border border-blue-500 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-colors duration-200">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
</body>

</html>