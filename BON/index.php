<?php
session_start();

// Cek status login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

include '../config/koneksi.php';

// =========================================================================
// MODIFIKASI DIMULAI DI SINI: MENGAMBIL INFO USER YANG LOGIN
// =========================================================================

// Ambil id_user dari session. Beri nilai default 0 jika tidak ada.
$id_user = $_SESSION['id_user'] ?? 0;

// Siapkan variabel dengan nilai default untuk mencegah error
$username = 'Tamu';
$nama_lengkap = 'Tamu';

// Lakukan query hanya jika id_user valid
if ($id_user > 0) {
    // Siapkan query untuk mengambil username DAN nama_lengkap dalam satu kali jalan
    $stmt_user = $conn->prepare("SELECT username, nama_lengkap FROM user WHERE id_user = ?");
    
    if ($stmt_user) {
        $stmt_user->bind_param("i", $id_user);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        
        if ($result_user->num_rows > 0) {
            $user_data = $result_user->fetch_assoc();
            
            // Isi kedua variabel dengan data yang benar dari database
            $username = $user_data['username'];
            $nama_lengkap = $user_data['nama_lengkap'];
        }
        $stmt_user->close();
    }
}

// --- Pagination Settings ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Query untuk menghitung total record ---
$count_query = "SELECT COUNT(*) AS total_records FROM bon";
if (!empty($_GET['idBon'])) {
    $idBonFilter = intval($_GET['idBon']);
    $count_query .= " WHERE id_bon = " . $idBonFilter;
}
$count_result = mysqli_query($conn, $count_query);
if (!$count_result) {
    die("Count Query Error: " . mysqli_error($conn));
}
$total_records = mysqli_fetch_assoc($count_result)['total_records'];
$total_pages = ceil($total_records / $limit);
// --- End Pagination Settings ---


// --- MODIFIKASI QUERY UTAMA ---
// Menambahkan afrn.id_afrn dan afrn.tgl_afrn untuk kebutuhan formatting
$main_query = "SELECT
                    bon.id_bon, bon.tgl_rekam, bon.jlh_pengisian,
                    afrn.id_afrn, afrn.tgl_afrn, bridger.no_polisi
                FROM bon
                LEFT JOIN afrn ON bon.no_afrn = afrn.no_afrn
                LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger";

if (!empty($_GET['idBon'])) {
    $idBonFilter = intval($_GET['idBon']);
    $main_query .= " WHERE bon.id_bon = " . $idBonFilter;
}

$main_query .= " ORDER BY bon.tgl_rekam DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $main_query);
if (!$result) {
    die("Main Query Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar BON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .font-modify {
        font-family: sans-serif;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-md">
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

            <div class="flex-1 p-10 bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-700">DAFTAR BON</h2>
                    <a href="insert_bon.php"
                        class="bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-gray-300">
                        Insert BON
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-white text-blue-800 font-semibold">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">No AFRN</th>
                                <th class="px-4 py-3">Tanggal Rekam</th>
                                <th class="px-4 py-3">No Polisi</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                            // --- MODIFIKASI LOGIKA TAMPILAN ---

                            // 1. Buat array helper untuk bulan Romawi
                            $romanMonths = [
                                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
                            ];

                            $no = $offset + 1;
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    
                                    // 2. Format No AFRN secara dinamis
                                    $formatted_no_afrn = '-'; // Default jika tidak ada AFRN
                                    if (!empty($row['id_afrn']) && !empty($row['tgl_afrn'])) {
                                        $id_afrn = $row['id_afrn'];
                                        $tgl_afrn = $row['tgl_afrn'];

                                        $timestamp = strtotime($tgl_afrn);
                                        $month_num = date('n', $timestamp);
                                        $year = date('Y', $timestamp);
                                        $month_roman = $romanMonths[$month_num] ?? '?';

                                        $formatted_no_afrn = "{$id_afrn}/AFRN/{$month_roman}/{$year}";
                                    }

                                    $no_polisi = !empty($row['no_polisi']) ? htmlspecialchars($row['no_polisi']) : '-';
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $no++ ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($formatted_no_afrn) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['tgl_rekam']) ?></td>
                                <td class="px-4 py-2"><?= $no_polisi ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['jlh_pengisian']) ?></td>
                                <td class="px-4 py-2">
                                    <a href="edit_bon.php?idBon=<?= $row['id_bon'] ?>"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">Tidak ada data BON ditemukan.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if ($total_pages > 1) { ?>
                    <div class="flex justify-center items-center mt-4 mb-4">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($page > 1) { ?>
                            <a href="?page=<?= $page - 1 ?><?= !empty($_GET['idBon']) ? '&idBon=' . htmlspecialchars($_GET['idBon']) : '' ?>"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-blue-700 hover:bg-gray-50">
                                Previous
                            </a>
                            <?php } ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) {
                                echo '<a href="?page=1'. (!empty($_GET['idBon']) ? '&idBon=' . htmlspecialchars($_GET['idBon']) : '') . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-blue-700 hover:bg-gray-50">1</a>';
                                if ($start_page > 2) {
                                    echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                }
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active_class = ($i == $page) ? 'z-10 bg-blue-600 text-white' : 'bg-white text-blue-700 hover:bg-blue-50';
                            ?>
                            <a href="?page=<?= $i ?><?= !empty($_GET['idBon']) ? '&idBon=' . htmlspecialchars($_GET['idBon']) : '' ?>"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium <?= $active_class ?> ">
                                <?= $i ?>
                            </a>
                            <?php } ?>

                            <?php
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                }
                                echo '<a href="?page=' . $total_pages . (!empty($_GET['idBon']) ? '&idBon=' . htmlspecialchars($_GET['idBon']) : '') . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-blue-700 hover:bg-gray-50">' . $total_pages . '</a>';
                            }
                            ?>

                            <?php if ($page < $total_pages) { ?>
                            <a href="?page=<?= $page + 1 ?><?= !empty($_GET['idBon']) ? '&idBon=' . htmlspecialchars($_GET['idBon']) : '' ?>"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-blue-700 hover:bg-gray-50">
                                Next
                            </a>
                            <?php } ?>
                        </nav>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <?php include_once '../components/footer.php'; ?>


</body>

</html>