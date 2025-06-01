<?php
include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_trans = intval($_GET['id']);
$query = $conn->query("SELECT * FROM transportir WHERE id_trans = $id_trans");

if ($query->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = $query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_trans = $_POST['nama_trans'];
    $alamat_trans = $_POST['alamat_trans'];

    $stmt = $conn->prepare("UPDATE transportir SET nama_trans = ?, alamat_trans = ? WHERE id_trans = ?");
    $stmt->bind_param("ssi", $nama_trans, $alamat_trans, $id_trans);

    if ($stmt->execute()) {
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
    <title>Edit Transportir</title>
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
                <h1 class="text-xl mb-4 p-2 font-semibold mb-2 text-gray-700">FORM TRANSPORTIR</h1>

                <div class="w-full bg-white p-6 rounded shadow-md">

                    <form method="POST" class="w-full">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Nama Transportir</label>
                            <input type="text" name="nama_trans" required
                                value="<?= htmlspecialchars($data['nama_trans']) ?>"
                                class="w-full border px-4 py-2 rounded" />
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Alamat Transportir</label>
                            <textarea name="alamat_trans" required
                                class="w-full border px-4 py-2 rounded"><?= htmlspecialchars($data['alamat_trans']) ?></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="index.php"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>