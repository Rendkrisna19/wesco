<?php
include '../config/koneksi.php'; 

// Ambil semua data yang dibutuhkan
$bridger_result = mysqli_query($conn, "
    SELECT bridger.id_bridger, bridger.no_polisi, bridger.volume, bridger.id_trans, transportir.nama_trans AS transportir
    FROM bridger
    LEFT JOIN transportir ON bridger.id_trans = transportir.id_trans
");
$destinasi_result = mysqli_query($conn, "SELECT * FROM destinasi");
$driver_result = mysqli_query($conn, "SELECT * FROM driver");
$tangki_result = mysqli_query($conn, "SELECT * FROM tangki");

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tgl_afrn = date('Y-m-d');
    $no_afrn = 'AFRN-' . time(); // Bisa diganti UUID
    $no_bpp = mysqli_real_escape_string($conn, $_POST['no_bpp']);
    $id_bridger = $_POST['id_bridger'];
    $id_destinasi = $_POST['id_destinasi'];
    $id_driver = $_POST['id_driver'];
    $id_tangki = $_POST['id_tangki'];
    $dibuat = mysqli_real_escape_string($conn, $_POST['dibuat']);
    $diperiksa = mysqli_real_escape_string($conn, $_POST['diperiksa']);
    $disetujui = mysqli_real_escape_string($conn, $_POST['disetujui']);
    $rit = (int) $_POST['rit'];

    // Ambil id_transportir berdasarkan id_bridger
    $get_trans = mysqli_query($conn, "
        SELECT id_trans FROM bridger WHERE id_bridger = '$id_bridger'
    ");
    if (mysqli_num_rows($get_trans) == 0) {
        die("ID Bridger tidak ditemukan.");
    }
    $id_transportasi = mysqli_fetch_assoc($get_trans)['id_trans'];

    $query = "INSERT INTO afrn (
        tgl_afrn, no_afrn, no_bpp, id_bridger, id_transportir, id_destinasi, id_tangki,
        dibuat, diperiksa, disetujui, rit
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssiiiisssi",
        $tgl_afrn, $no_afrn, $no_bpp, $id_bridger, $id_transportasi, $id_destinasi,
        $id_tangki, $dibuat, $diperiksa, $disetujui, $rit
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data AFRN berhasil disimpan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data AFRN: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input AFRN</title>
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
                <h2 class="text-2xl font-semibold mb-6">Form Input Data AFRN</h2>
                <form action="" method="POST" class="space-y-6">

                    <div>
                        <label class="block font-medium">No BPP / PNBP</label>
                        <input type="text" name="no_bpp" class="w-full p-2 border rounded" required
                            placeholder="Contoh: 123/BPP/2025">
                    </div>

                    <!-- Bridger -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-medium">Nomor Polisi</label>
                            <select name="id_bridger" id="bridger" class="w-full p-2 border rounded" required
                                onchange="setBridgerData(this)">
                                <option class="text-gray-300 font-sm font-normal" value="">Pilih nomor polisi </option>
                                <?php while ($b = mysqli_fetch_assoc($bridger_result)): ?>
                                <option value="<?= $b['id_bridger'] ?>" data-transportir="<?= $b['transportir'] ?>"
                                    data-volume="<?= $b['volume'] ?>">
                                    <?= $b['no_polisi'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Transportir</label>
                            <input type="text" id="transportir" class="w-full p-2 rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-medium">Volume</label>
                            <input type="text" id="volume" class="w-full p-2 rounded bg-gray-100" readonly>
                        </div>
                    </div>

                    <!-- Destinasi & Driver -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Kepada</label>
                            <select name="id_destinasi" class="w-full p-2 border rounded" required>
                                <option value="">Pilih destinasi</option>
                                <?php while ($d = mysqli_fetch_assoc($destinasi_result)): ?>
                                <option class="text-gray-300 font-sm font-normal" value="<?= $d['id_destinasi'] ?>">
                                    <?= $d['nama_destinasi'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Pengemudi</label>
                            <select name="id_driver" class="w-full p-2 border rounded" required>
                                <option class="text-gray-300 text-sm text-normal" value="">Pilih Pengemudi </option>
                                <?php while ($p = mysqli_fetch_assoc($driver_result)): ?>
                                <option value="<?= $p['id_driver'] ?>"><?= $p['nama_driver'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- RIT -->
                    <div>
                        <label class="block font-medium">RIT</label>
                        <input type="number" name="rit" class="w-full p-2 border rounded" required
                            placeholder="Contoh: 1">
                    </div>

                    <!-- Tangki -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">No Tangki</label>
                            <select name="id_tangki" id="tangki" class="w-full p-2 border rounded" required
                                onchange="setTangkiData(this)">
                                <option value="">Pilih Nomor tangki-</option>
                                <?php while ($t = mysqli_fetch_assoc($tangki_result)): ?>
                                <option class="text-gray-300 text-sm text-normal" value="<?= $t['id_tangki'] ?>"
                                    data-no_batch="<?= $t['no_bacth'] ?>" data-source="<?= $t['source'] ?>"
                                    data-test_report="<?= $t['test_report_let'] ?>" data-density="<?= $t['density'] ?>"
                                    data-temperature="<?= $t['temperature'] ?>" data-cu="<?= $t['cu'] ?>">
                                    <?= $t['no_tangki'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" id="no_batch" class="p-2 bg-gray-100 rounded" placeholder="No Batch"
                                readonly>
                            <input type="text" id="source" class="p-2 bg-gray-100 rounded" placeholder="Source"
                                readonly>
                            <input type="text" id="test_report" class="p-2 bg-gray-100 rounded"
                                placeholder="Test Report" readonly>
                            <input type="text" id="density" class="p-2 bg-gray-100 rounded" placeholder="Density"
                                readonly>
                            <input type="text" id="temperature" class="p-2 bg-gray-100 rounded" placeholder="Temp"
                                readonly>
                            <input type="text" id="cu" class="p-2 bg-gray-100 rounded" placeholder="CU" readonly>
                        </div>
                    </div>

                    <!-- Penanggung Jawab -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="dibuat" class="p-2 border rounded" required placeholder="Dibuat Oleh">
                        <input type="text" name="diperiksa" class="p-2 border rounded" required
                            placeholder="Diperiksa Oleh">
                        <input type="text" name="disetujui" class="p-2 border rounded" required
                            placeholder="Disetujui Oleh">
                    </div>

                    <div class="text-right">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
            </main>
        </div>

        <script>
        function setBridgerData(select) {
            const option = select.options[select.selectedIndex];
            document.getElementById("transportir").value = option.dataset.transportir || '';
            document.getElementById("volume").value = option.dataset.volume || '';
        }

        function setTangkiData(select) {
            const opt = select.options[select.selectedIndex];
            document.getElementById("no_batch").value = opt.dataset.no_batch || '';
            document.getElementById("source").value = opt.dataset.source || '';
            document.getElementById("test_report").value = opt.dataset.test_report || '';
            document.getElementById("density").value = opt.dataset.density || '';
            document.getElementById("temperature").value = opt.dataset.temperature || '';
            document.getElementById("cu").value = opt.dataset.cu || '';
        }
        </script>
</body>

</html>