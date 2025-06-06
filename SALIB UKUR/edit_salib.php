<?php
session_start(); 


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
// Assuming nama_lengkap is also set in session from login
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
include '../config/koneksi.php';

// Ambil ID dari parameter GET
if (isset($_GET['idSegel'])) {
    $id_segel = mysqli_real_escape_string($conn, $_GET['idSegel']);

    // Query untuk mengambil data berdasarkan ID segel
$query = "SELECT 
    segel.id_segel,
    segel.mainhole1,
    segel.mainhole2,
    segel.mainhole3,
    segel.mainhole4,
    segel.bottom_load_cov1,
    segel.bottom_load_cov2,
    segel.bottom_load_cov3,
    segel.bottom_load_cov4,
    segel.bottom_load_cov5,
    salib_ukur.ket_jarak_t1,
    salib_ukur.ket_jarak_cair_t1,
    salib_ukur.diperiksa_t1,
    salib_ukur.diperiksa_segel,
    salib_ukur.id_ukur,
    salib_ukur.id_afrn,
    salib_ukur.id_jarak_t1,
    salib_ukur.id_jarak_cair_t1,
    jarak_t1.jarak_komp1,
    jarak_t1.jarak_komp2,
    jarak_t1.jarak_komp3,
    jarak_t1.jarak_komp4,
    jarak_t1.temp_komp1,
    jarak_t1.temp_komp2,
    jarak_t1.temp_komp3,
    jarak_t1.temp_komp4,
    jarak_cair_t1.jarak_cair_komp1,
    jarak_cair_t1.jarak_cair_komp2,
    jarak_cair_t1.jarak_cair_komp3,
    jarak_cair_t1.jarak_cair_komp4,
    jarak_cair_t1.dencity_cair_komp1,
    jarak_cair_t1.dencity_cair_komp2,
    jarak_cair_t1.dencity_cair_komp3,
    jarak_cair_t1.dencity_cair_komp4,
    jarak_cair_t1.temp_cair_komp_komp1,
    jarak_cair_t1.temp_cair_komp_komp2,
    jarak_cair_t1.temp_cair_komp_komp3,
    jarak_cair_t1.temp_cair_komp_komp4,
    afrn.no_afrn,
    afrn.tgl_afrn,
    afrn.no_bpp,
    afrn.dibuat,
    afrn.diperiksa,
    afrn.disetujui,
    bridger.no_polisi,
    bridger.volume,
    bridger.tgl_serti_akhir,
    bon.tgl_rekam
FROM segel
LEFT JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
LEFT JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
LEFT JOIN bon ON afrn.id_bon = bon.id_bon
LEFT JOIN jarak_t1 ON salib_ukur.id_jarak_t1 = jarak_t1.id_jarak_t1
LEFT JOIN jarak_cair_t1 ON salib_ukur.id_jarak_cair_t1 = jarak_cair_t1.id_jarak_cair_t1
LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
WHERE segel.id_segel = '$id_segel'
LIMIT 1";


    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
}

// ============================
// 2. Proses Update Data
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    mysqli_begin_transaction($conn);
    try {
        // Update jarak_t1
        $stmt1 = mysqli_prepare($conn, "UPDATE jarak_t1 SET jarak_komp1 = ?, jarak_komp2 = ?, jarak_komp3 = ?, jarak_komp4 = ?, temp_komp1 = ?, temp_komp2 = ?, temp_komp3 = ?, temp_komp4 = ? WHERE id_jarak_t1 = ?");
       mysqli_stmt_bind_param($stmt1, "ddddddddi",  $_POST['jarak_komp1'], $_POST['jarak_komp2'], $_POST['jarak_komp3'], $_POST['jarak_komp4'], $_POST['temp_komp1'], $_POST['temp_komp2'], $_POST['temp_komp3'], $_POST['temp_komp4'], $_POST['id_jarak_t1']);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        // Update jarak_cair_t1
        $stmt2 = mysqli_prepare($conn, "UPDATE jarak_cair_t1 SET jarak_cair_komp1 = ?, jarak_cair_komp2 = ?, jarak_cair_komp3 = ?, jarak_cair_komp4 = ?, dencity_cair_komp1 = ?, dencity_cair_komp2 = ?, dencity_cair_komp3 = ?, dencity_cair_komp4 = ?, temp_cair_komp_komp1 = ?, temp_cair_komp_komp2 = ?, temp_cair_komp_komp3 = ?, temp_cair_komp_komp4 = ? WHERE id_jarak_cair_t1 = ?");
        mysqli_stmt_bind_param($stmt2, "ddddddddddddd", $_POST['jarak_cair_komp1'], $_POST['jarak_cair_komp2'], $_POST['jarak_cair_komp3'], $_POST['jarak_cair_komp4'], $_POST['dencity_cair_komp1'], $_POST['dencity_cair_komp2'], $_POST['dencity_cair_komp3'], $_POST['dencity_cair_komp4'], $_POST['temp_cair_komp_komp1'], $_POST['temp_cair_komp_komp2'], $_POST['temp_cair_komp_komp3'], $_POST['temp_cair_komp_komp4'], $_POST['id_jarak_cair_t1']);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // Update salib_ukur
        $stmt3 = mysqli_prepare($conn, "UPDATE salib_ukur SET ket_jarak_t1 = ?, ket_jarak_cair_t1 = ?, diperiksa_t1 = ?, diperiksa_segel = ? WHERE id_ukur = ?");
        mysqli_stmt_bind_param($stmt3, "sssss", $_POST['ket_jarak_t1'], $_POST['ket_jarak_cair_t1'], $_POST['diperiksa_t1'], $_POST['diperiksa_segel'], $_POST['id_ukur']);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);

        // Update segel
        $stmt4 = mysqli_prepare($conn, "UPDATE segel SET mainhole1 = ?, mainhole2 = ?, mainhole3 = ?, mainhole4 = ?, bottom_load_cov1 = ?, bottom_load_cov2 = ?, bottom_load_cov3 = ?, bottom_load_cov4 = ?, bottom_load_cov5 = ? WHERE id_segel = ?");
        mysqli_stmt_bind_param($stmt4, "ssssssssss", $_POST['mainhole1'], $_POST['mainhole2'], $_POST['mainhole3'], $_POST['mainhole4'], $_POST['bottom_load_cov1'], $_POST['bottom_load_cov2'], $_POST['bottom_load_cov3'], $_POST['bottom_load_cov4'], $_POST['bottom_load_cov5'], $id_segel);
        mysqli_stmt_execute($stmt4);
        mysqli_stmt_close($stmt4);

        mysqli_commit($conn);
        echo "<script>alert('Data berhasil diperbarui.'); window.location.href='index.php';</script>";
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES);
        echo "<script>alert('Gagal memperbarui data: $msg');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Segel</title>
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
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
            <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="text-gray-700 p-4 rounded-t-lg">
                    <h1 class="text-lg font-normal">EDIT DATA PENCATATAN HASIL PEMERIKSAAN BRIDGER SEBELUM KELUAR LOKASI
                    </h1>
                </div>
                <div class="p-6">
                    <form action="edit_salib.php?idSegel=<?= $id_segel ?>" method="POST"
                        class="p-4 bg-white shadow-md rounded">
                        <input type="hidden" name="id_ukur" value="<?= $data['id_ukur'] ?>">
                        <input type="hidden" name="id_jarak_t1" value="<?= $data['id_jarak_t1'] ?>">
                        <input type="hidden" name="id_jarak_cair_t1" value="<?= $data['id_jarak_cair_t1'] ?>">
                        <!-- Info Bridger -->
                        <div class="grid grid-cols-5 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. AFRN</label>
                                <input type="text" name="no_afrn" value="<?= $data['no_afrn'] ?>" readonly
                                    class="w-full bg-blue-700 text-white px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="tgl_afrn" value="<?= $data['tgl_afrn'] ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No Polisi</label>
                                <input type="text" name="no_polisi" value="<?= $data['no_polisi'] ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Volume Bridger</label>
                                <input type="text" name="volume" value="<?= $data['volume'] ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku Tangki</label>
                                <input type="date" name="tgl_serti_akhir" value="<?= $data['tgl_serti_akhir'] ?>"
                                    readonly class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                        </div>
                        <!-- Pemeriksaan -->
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <p class="text-center text-sm text-gray-600 font-medium">
                                PEMERIKSAAN DAN PENCATATAN MINIMAL 10 MENIT SETELAH SETTLING TIME
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-8">
                            <!-- Jarak T1 -->
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-4">JARAK T1 PADA DOKUMEN</h3>
                                <?php for ($i=1; $i<=4; $i++): ?>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input name="jarak_komp<?= $i ?>" type="number" step="0.1"
                                            value="<?= $data['jarak_komp'.$i] ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp<?= $i ?>" type="number" step="0.1"
                                            value="<?= $data['temp_komp'.$i] ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">Komp <?= $i ?>.</div>
                                <?php endfor; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3" name="ket_jarak_t1"><?= $data['ket_jarak_t1'] ?></textarea>
                                </div>
                            </div>
                            <!-- Jarak Cairan -->
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-2">JARAK CAIRAN TERHADAP T1</h3>
                                <p class="text-center text-xs text-gray-500 mb-4">(ULLAGE) @ SUPPLY POINT</p>
                                <?php for ($i=1; $i<=4; $i++): ?>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" name="jarak_cair_komp<?= $i ?>"
                                            value="<?= $data['jarak_cair_komp'.$i] ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" name="dencity_cair_komp<?= $i ?>"
                                            value="<?= $data['dencity_cair_komp'.$i] ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" name="temp_cair_komp_komp<?= $i ?>"
                                            value="<?= $data['temp_cair_komp_komp'.$i] ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly>
                                    </div>
                                </div>
                                <?php endfor; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3" name="ket_jarak_cair_t1"><?= $data['ket_jarak_cair_t1'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Diperiksa & Dicatat Oleh -->
                        <div class="mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diperiksa & Dicatat
                                    Oleh</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    name="diperiksa_t1" value="<?= $data['diperiksa_t1'] ?>">
                            </div>
                        </div>
                        <!-- Pemeriksaan Segel -->
                        <div class="max-w-7xl mx-auto bg-white p-6 rounded-md shadow space-y-6 mt-6">
                            <h2 class="text-center text-md font-normal text-gray-700 uppercase">
                                Pemeriksaan dan Pencatatan Minimal 10 Menit Setelah Settling Time
                            </h2>
                            <p class="text-center text-sm font-medium text-gray-600">Nomor/Kode Segel</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for ($i=1; $i<=4; $i++): ?>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Mainhole <?= $i ?></label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="mainhole<?= $i ?>" value="<?= $data['mainhole'.$i] ?>">
                                </div>
                                <?php endfor; ?>
                                <?php for ($i=1; $i<=5; $i++): ?>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Bottom Loader Cover <?= $i ?></label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="bottom_load_cov<?= $i ?>" value="<?= $data['bottom_load_cov'.$i] ?>">
                                </div>
                                <?php endfor; ?>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-normal block mb-1">Jam Keluar</label>
                                    <input type="text" name="tgl_rekam"
                                        class="w-full bg-gray-100 border rounded px-3 py-2 text-sm cursor-not-allowed"
                                        value="" readonly disabled>
                                </div>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Diperiksa & Dicatat Oleh</label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="diperiksa_segel" value="<?= $data['diperiksa_segel'] ?>">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <a href="index.php"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                                <button type="submit" name="update"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>