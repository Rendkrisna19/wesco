<?php
include '../config/koneksi.php';

if (empty($_GET['idBridger'])) {
    header("Location: index.php");
    exit;
}

$idBridger = intval($_GET['idBridger']);

// Ambil data bridger berdasarkan idBridger
$sql = "SELECT * FROM bridger WHERE id_bridger = $idBridger LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = $result->fetch_assoc();

// Ambil data transportir untuk dropdown
$transportir = $conn->query("SELECT id_trans, nama_trans FROM transportir");

// Volume options tetap sama
$volumeOptions = [8, 16, 24, 32];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_trans = $_POST['id_trans'];
    $no_polisi = $_POST['no_polisi'];
    $tgl_akhir = $_POST['tgl_akhir'];
    $id_tipe_bridger = 1; // Tetap 1 (BRIDGER)
    $volume = $_POST['volume'];
    $tera1 = $_POST['tera1'];
    $tera2 = $_POST['tera2'];
    $tera3 = $_POST['tera3'];
    $tera4 = $_POST['tera4'];

    $updateSql = "UPDATE bridger SET 
                    id_trans = '$id_trans',
                    no_polisi = '$no_polisi',
                    tgl_serti_akhir = '$tgl_akhir',
                    id_tipe_bridger = '$id_tipe_bridger',
                    volume = '$volume',
                    tera1 = '$tera1',
                    tera2 = '$tera2',
                    tera3 = '$tera3',
                    tera4 = '$tera4'
                  WHERE id_bridger = $idBridger";

    if ($conn->query($updateSql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Bridger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            <div class="flex-1 p-10 bg-white">
                <h2 class="text-xl mb-6 text-gray-700">FORM BRIDGER</h2>

                <form method="POST">
                    <label class="block mb-2 text-gray-600">Perusahaan Transportir</label>
                    <select name="id_trans" class="w-full border px-4 py-2 rounded mb-4" required>
                        <option value="">Pilih Transportir</option>
                        <?php while ($row = $transportir->fetch_assoc()): ?>
                        <option value="<?= $row['id_trans'] ?>"
                            <?= ($row['id_trans'] == $data['id_trans']) ? 'selected' : '' ?>>
                            <?= $row['nama_trans'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>

                    <label class="block mb-2 text-gray-600">Nomor Polisi</label>
                    <input type="text" name="no_polisi" class="w-full border px-4 py-2 rounded mb-4"
                        placeholder="BRIDGER" value="<?= htmlspecialchars($data['no_polisi']) ?>" required>

                    <label class="block mb-2 text-gray-600">Tanggal Akhir Berlaku</label>
                    <input type="date" name="tgl_akhir" class="w-full border px-4 py-2 rounded mb-4"
                        value="<?= $data['tgl_serti_akhir'] ?>" required>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label class="block mb-2 text-gray-600">Vehicle Type</label>
                            <select name="id_tipe_bridger"
                                class="w-full border px-4 py-2 rounded text-gray-500 bg-gray-100 cursor-not-allowed"
                                disabled>
                                <option value="1" selected>BRIDGER</option>
                            </select>
                            <input type="hidden" name="id_tipe_bridger" value="1">
                        </div>

                        <div class="w-1/2">
                            <label class="block mb-2 text-gray-600">Volume</label>
                            <select name="volume" class="w-full border px-4 py-2 rounded" required>
                                <?php foreach ($volumeOptions as $vol): ?>
                                <option value="<?= $vol ?>" <?= ($data['volume'] == $vol) ? 'selected' : '' ?>>
                                    <?= $vol ?> KL
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <input type="text" name="tera1" placeholder="Tera 1" class="w-full border px-4 py-2 rounded"
                            value="<?= htmlspecialchars($data['tera1']) ?>">
                        <input type="text" name="tera2" placeholder="Tera 2" class="w-full border px-4 py-2 rounded"
                            value="<?= htmlspecialchars($data['tera2']) ?>">
                        <input type="text" name="tera3" placeholder="Tera 3" class="w-full border px-4 py-2 rounded"
                            value="<?= htmlspecialchars($data['tera3']) ?>">
                        <input type="text" name="tera4" placeholder="Tera 4" class="w-full border px-4 py-2 rounded"
                            value="<?= htmlspecialchars($data['tera4']) ?>">
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
</body>

</html>