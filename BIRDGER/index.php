<?php
include '../config/koneksi.php'; // mysqli connection

try {
    if (empty($_GET['idBridger'])) {
        $sql = "SELECT b.id_bridger, b.no_polisi, t.nama_trans
                FROM bridger b
                JOIN transportir t ON b.id_trans = t.id_trans";
        $result = $conn->query($sql);
    } else {
        $idBridger = intval($_GET['idBridger']);
        $sql = "SELECT b.id_bridger, b.no_polisi, t.nama_trans
                FROM bridger b
                JOIN transportir t ON b.id_trans = t.id_trans
                WHERE b.id_bridger = $idBridger";
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
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-900">Selamat Datang di Wesco Hermanto Purba</h1>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600">Hermanto Purba</span>
                    <img src="user-icon.png" alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <div class="flex-1 p-10 bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-700">DAFTAR BRIDGER</h2>
                    <a href="insert_bridger.php"
                        class="bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-gray-300">
                        Insert BRIDGER
                    </a>
                </div>


                <div class="overflow-x-auto rounded-lg shadow bg-white clear-both">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-white text-blue-600 font-bold">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">No. Polisi</th>
                                <th class="py-3 px-4">Transportir</th>
                                <th class="py-3 px-4">Aksi</th> <!-- kolom aksi -->
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
                                <td class="py-2 px-4"><?= htmlspecialchars($row['no_polisi']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['nama_trans']) ?></td>
                                <td class="py-2 px-4">
                                    <a href="edit_bridger.php?idBridger=<?= $row['id_bridger'] ?>"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan=" 4" class="text-center py-4 text-gray-500">Data tidak ditemukan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>