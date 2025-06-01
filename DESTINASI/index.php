<?php
include '../config/koneksi.php';

// Ambil data destinasi
$sql = "SELECT * FROM destinasi ORDER BY id_destinasi ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Daftar Destinasi</title>
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
            <div class="bg-white p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-900">Selamat Datang di Wesco Hermanto Purba</h1>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600">Hermanto Purba</span>
                    <img src="user-icon.png" alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <!-- Form Section -->
            <!-- Form Section -->
            <div class="p-4 mt-4 flex-1 overflow-auto bg-white">
                <div class="p-4 mt-4 flex-1">
                    <!-- Tombol Insert dipindah ke atas tabel -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">DAFTAR DESTINASI</h2>
                        <a href="insert_destinasi.php"
                            class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow">
                            Insert Destinasi
                        </a>
                    </div>
                    <!-- Tabel Data -->
                    <div class="overflow-x-auto rounded-lg shadow bg-white">
                        <table class="min-w-full text-left text-gray-700 text-sm">
                            <thead class="bg-white text-blue-700 font-bold">
                                <tr>
                                    <th class="py-3 px-4">No</th>
                                    <th class="py-3 px-4">Nama Destinasi</th>
                                    <th class="py-3 px-4">Alamat Destinasi</th>
                                    <th class="py-3 px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $no = 1;
                    if ($result && $result->num_rows > 0) :
                        while ($row = $result->fetch_assoc()) :
                    ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $no++ ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_destinasi']) ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['alamat_destinasi']) ?></td>
                                    <td class="py-2 px-4">
                                        <a href="edit_destinasi.php?id=<?= $row['id_destinasi'] ?>"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Data tidak ditemukan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

</body>

</html>