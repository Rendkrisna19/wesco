<?php
include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM destinasi WHERE id_destinasi = $id LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_destinasi = $conn->real_escape_string($_POST['nama_destinasi']);
    $alamat_destinasi = $conn->real_escape_string($_POST['alamat_destinasi']);

    $updateSql = "UPDATE destinasi SET 
                    nama_destinasi = '$nama_destinasi',
                    alamat_destinasi = '$alamat_destinasi'
                  WHERE id_destinasi = $id";

    if ($conn->query($updateSql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Destinasi</title>
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
            <div class="flex-1 p-10 bg-white">
                <h3 class="text-xl font-semibold mb-6 text-gray-700">Edit Destinasi</h3>
                <form method="POST">
                    <label class="block mb-4">
                        <span class="text-gray-700">Nama Destinasi</span>
                        <input type="text" name="nama_destinasi" required
                            value="<?= htmlspecialchars($data['nama_destinasi']) ?>"
                            class="mt-1 block w-full border rounded px-3 py-2" />
                    </label>
                    <label class="block mb-6">
                        <span class="text-gray-700">Alamat Destinasi</span>
                        <textarea name="alamat_destinasi" required
                            class="mt-1 block w-full border rounded px-3 py-2"><?= htmlspecialchars($data['alamat_destinasi']) ?></textarea>
                    </label>
                    <div class="flex justify-end gap-2">
                        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
</body>

</html>