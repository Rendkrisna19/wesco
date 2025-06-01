<?php
session_start();
include '../config/koneksi.php';

if (empty($_GET['idBon'])) {
    $query = "SELECT 
                bon.id_bon, bon.tgl_rekam, bon.jlh_pengisian, 
                afrn.no_afrn, bridger.no_polisi
              FROM bon
              LEFT JOIN afrn ON bon.no_afrn = afrn.no_afrn
              LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
              ORDER BY bon.tgl_rekam DESC";
              
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }
} else {
    $idBon = intval($_GET['idBon']);
    $query = "SELECT 
                bon.id_bon, bon.tgl_rekam, bon.jlh_pengisian, 
                afrn.no_afrn, bridger.no_polisi
              FROM bon
              LEFT JOIN afrn ON bon.no_afrn = afrn.no_afrn
              LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
              WHERE bon.id_bon = $idBon";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar BON</title>
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
                <h1 class="text-2xl font-bold text-cyan-900">Selamat Datang di Wesco Hermanto Purba</h1>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600">Hermanto Purba</span>
                    <img src="user-icon.png" alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <!-- Content -->
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
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Jika no_polisi NULL tampilkan tanda '-'
                                $no_polisi = !empty($row['no_polisi']) ? htmlspecialchars($row['no_polisi']) : '-';
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $no++ ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['no_afrn']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['tgl_rekam']) ?></td>
                                <td class="px-4 py-2"><?= $no_polisi ?></td>
                                <td class="px-4 py-2"><?= number_format($row['jlh_pengisian'], 0, ',', '.') ?></td>
                                <td class="px-4 py-2">
                                    <a href="edit_bon.php?idBon=<?= $row['id_bon'] ?>"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if (mysqli_num_rows($result) === 0) : ?>
                    <p class="p-4 text-center text-gray-500">Tidak ada data BON ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>