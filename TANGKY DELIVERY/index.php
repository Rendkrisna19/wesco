<?php
include '../config/koneksi.php';

try {
    if (empty($_GET['idTangki'])) {
        $sql = "SELECT * FROM tangki";
        $result = $conn->query($sql);
    } else {
        $idTangki = intval($_GET['idTangki']);
        $sql = "SELECT * FROM tangki WHERE id_tangki = $idTangki";
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
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-white p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-900">Selamat Datang di Wesco Hermanto Purba</h1>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600">Hermanto Purba</span>
                    <img src="user-icon.png" alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-6 mt-12">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-1xl font-extrabold text-gray-700 tracking-wide">DAFTAR TANGKI DELIVERY</h4>
                    <a href="insert_tangki.php"
                        class="bg-blue-700 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-200">
                        Insert Tangki
                    </a>
                </div>

                <!-- Tabel -->
                <div class="overflow-x-auto rounded-xl shadow bg-white">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-white border-b text-blue-700 font-semibold">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">No Tangki</th>
                                <th class="py-3 px-4">Batch/Tumpak</th>
                                <th class="py-3 px-4">Source</th>
                                <th class="py-3 px-4">Test Report</th>
                                <th class="py-3 px-4">Tanggal Test Report</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                        $no = 1;
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="py-3 px-4"><?= $no++ ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['no_tangki']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['no_bacth']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['source']) ?></td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($row['test_report_no'] . $row['test_report_let']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['test_report_date']) ?></td>
                                <td class="py-3 px-4">
                                    <a href="edit_tangki.php?idTangki=<?= $row['id_tangki'] ?>"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-1 border border-gray-400 rounded-full transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</body>