<?php
include '../config/koneksi.php';

if (isset($_POST['insert'])) {
    $nama = $conn->real_escape_string($_POST['nama_trans']);
    $alamat = $conn->real_escape_string($_POST['alamat_trans']);

    $conn->query("INSERT INTO transportir (nama_trans, alamat_trans) VALUES ('$nama', '$alamat')");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Tambah Transportir</title>
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

            <!-- Form Section -->
            <div class="flex-1 p-10 bg-white">
                <h2 class="text-xl font-bold mb-6 text-gray-700">Tambah Transportir</h2>

                <form method="POST" class="w-full bg-white p-6 rounded shadow-md">
                    <div class="mb-4">
                        <label class="block mb-2 text-gray-700">Nama Transportir</label>
                        <input type="text" name="nama_trans" required class="block w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-gray-700">Alamat Transportir</label>
                        <textarea name="alamat_trans" required class="block w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="insert"
                            class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Submit</button>
                        <a href="index.php" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>