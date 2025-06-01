<?php
include '../config/koneksi.php';

$sql = "SELECT id_trans, nama_trans, alamat_trans FROM transportir";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Transportir</title>
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

            <div class="p-4 mt-4 flex-1 overflow-auto bg-white">
                <div class="p-4 mt-4 flex-1">
                    <h2 class="text-xl font-semibold  text-gray-700 mb-2">DAFTAR TRANSPORTIR</h2>

                    <a href="insert_transportir.php"
                        class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow float-right mb-2">
                        Insert Transportir
                    </a>

                    <div class="overflow-x-auto rounded-lg shadow bg-white clear-both">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-white text-blue-700 font-bold">
                                <tr>
                                    <th class="py-3 px-4">No</th>
                                    <th class="py-3 px-4">Nama Transportir</th>
                                    <th class="py-3 px-4">Alamat Transportir</th>
                                    <th class="py-3 px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                        if ($result && $result->num_rows > 0):
                            $no = 1;
                            while($row = $result->fetch_assoc()):
                        ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $no++ ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_trans']) ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['alamat_trans']) ?></td>
                                    <td class="py-2 px-4">
                                        <a href="edit_transportir.php?id=<?= $row['id_trans'] ?>"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">Edit</a>
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