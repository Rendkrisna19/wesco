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
include '../config/koneksi.php'; // koneksi mysqli

try {
    if (empty($_GET['idSegel'])) {
        $sql = "SELECT 
                    segel.id_segel,
                    segel.mainhole1,
                    segel.mainhole2,
                    segel.bottom_load_cov1,
                    segel.bottom_load_cov2,
                    afrn.no_afrn,
                    afrn.tgl_afrn,
                    afrn.id_afrn,
                    bridger.no_polisi,
                    bridger.volume
                FROM segel
                JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
                JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
                JOIN bridger ON afrn.id_bridger = bridger.id_bridger";
        $result = $conn->query($sql);
    } else {
        $idSegel = intval($_GET['idSegel']);
        $sql = "SELECT 
                    segel.id_segel,
                    segel.mainhole1,
                    segel.mainhole2,
                    segel.bottom_load_cov1,
                    segel.bottom_load_cov2,
                    afrn.no_afrn,
                    afrn.tgl_afrn,
                    bridger.no_polisi,
                    bridger.volume
                FROM segel
                JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
                JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
                JOIN bridger ON afrn.id_bridger = bridger.id_bridger
                WHERE segel.id_segel = $idSegel";
        $result = $conn->query($sql);
    }
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit;
}
?>

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
                <div class="flex justify-between items-center mb-2">

                    <div class="p-4 mt-2 flex-1">
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">DAFTAR SALIB UKUR</h2>

                        <a href="insert_salibukur.php"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow float-right mb-4">
                            Insert Salib Ukur
                        </a>

                        <div class="overflow-x-auto rounded-lg shadow bg-white clear-both">
                            <table class="min-w-full text-sm text-left text-gray-700">
                                <thead class="bg-white text-blue-700 font-bold">
                                    <tr>
                                        <th class="py-3 px-4">No</th>
                                        <th class="py-3 px-4">No AFRN</th>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-4">No Polisi</th>
                                        <th class="py-3 px-4">Volume Tangki</th>
                                        <th class="py-3 px-4">Segel Awal</th>
                                        <th class="py-3 px-4">Segel Akhir</th>
                                        <th class="py-3 px-8">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                        $no = 1;
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4"><?= $no++ ?></td>
                                        <td class="py-2 px-4">
                                            <?= htmlspecialchars($row['id_afrn']) . ' / ' . htmlspecialchars($row['no_afrn']) ?>
                                        </td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['tgl_afrn']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['no_polisi']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['volume']) ?></td>
                                        <td class="py-2 px-4"><?= 
                                htmlspecialchars($row['mainhole1']) 
                            ?></td>
                                        <td class="py-2 px-4"><?= 
                                htmlspecialchars($row['bottom_load_cov1'] )
                            ?></td>
                                        <td class="py-2 px-4 flex gap-2">
                                            <a href="edit_salib.php?idSegel=<?= $row['id_segel'] ?>"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 border border-black rounded-full transition text-xs">
                                                Edit
                                            </a>
                                            <a href="cetak_salib.php?idSegel=<?= urlencode($row['id_segel']) ?>&id_afrn=<?= urlencode($row['id_afrn']) ?>"
                                                target="_blank"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 border border-black rounded-full transition text-xs">
                                                Cetak
                                            </a>
                                        </td>

                                    </tr>
                                    <?php
                            endwhile;
                        else:
                        ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-gray-500">Data tidak ditemukan.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>