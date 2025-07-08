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
                            $no = $offset + 1;

                            // --- MODIFIKASI DIMULAI DI SINI ---
                            
                            // 1. Buat array helper untuk mengubah bulan menjadi angka Romawi
                            $romanMonths = [
                                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
                            ];

                            // Query tetap mengambil data yang diperlukan termasuk id_afrn dan tgl_afrn
                            $query = "SELECT 
                                a.id_afrn, a.tgl_afrn, a.no_bpp, 
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
                                    
                                    // 2. Ambil data yang dibutuhkan dari baris saat ini
                                    $id_afrn = $row['id_afrn'];
                                    $tgl_afrn = $row['tgl_afrn'];

                                    // 3. Proses tanggal untuk mendapatkan bulan dan tahun
                                    $timestamp = strtotime($tgl_afrn);
                                    $month_num = date('n', $timestamp);
                                    $year = date('Y', $timestamp);
                                    
                                    // 4. Ubah bulan angka menjadi Romawi
                                    $month_roman = $romanMonths[$month_num] ?? '?';

                                    // 5. Gabungkan menjadi format yang diinginkan
                                    $formatted_no_afrn = "{$id_afrn}/AFRN/{$month_roman}/{$year}";

                                    // 6. Tampilkan di dalam tabel
                                    echo "<tr class='border-t text-center'>";
                                    echo "<td class='px-4 py-2'>{$no}</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_bpp']) . "</td>";
                                    // Tampilkan No AFRN yang sudah diformat secara dinamis
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($formatted_no_afrn) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['tgl_afrn']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_trans']) . "</td>";
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['no_polisi']) . "</td>";
                                    // Tampilkan volume sesuai data asli di database
                                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['volume']) . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='7' class='py-4 text-center'>Data tidak ditemukan</td></tr>";
                            }

                            // --- MODIFIKASI SELESAI DI SINI ---
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

            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
</body>

</html>