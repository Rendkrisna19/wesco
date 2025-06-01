<?php
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
                bridger.tgl_serti_akhir
            FROM segel
            LEFT JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
            LEFT JOIN jarak_t1 ON salib_ukur.id_jarak_t1 = jarak_t1.id_jarak_t1
            LEFT JOIN jarak_cair_t1 ON salib_ukur.id_jarak_cair_t1 = jarak_cair_t1.id_jarak_cair_t1
            LEFT JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
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
        mysqli_stmt_bind_param($stmt1, "ddddddddd", $_POST['jarak_komp1'], $_POST['jarak_komp2'], $_POST['jarak_komp3'], $_POST['jarak_komp4'], $_POST['temp_komp1'], $_POST['temp_komp2'], $_POST['temp_komp3'], $_POST['temp_komp4'], $_POST['id_jarak_t1']);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        // Update jarak_cair_t1
        $stmt2 = mysqli_prepare($conn, "UPDATE jarak_cair_t1 SET jarak_cair_komp1 = ?, jarak_cair_komp2 = ?, jarak_cair_komp3 = ?, jarak_cair_komp4 = ?, dencity_cair_komp1 = ?, dencity_cair_komp2 = ?, dencity_cair_komp3 = ?, dencity_cair_komp4 = ?, temp_cair_komp_komp1 = ?, temp_cair_komp_komp2 = ?, temp_cair_komp_komp3 = ?, temp_cair_komp_komp4 = ? WHERE id_jarak_cair_t1 = ?");
        mysqli_stmt_bind_param($stmt2, "dddddddddddd", $_POST['jarak_cair_komp1'], $_POST['jarak_cair_komp2'], $_POST['jarak_cair_komp3'], $_POST['jarak_cair_komp4'], $_POST['dencity_cair_komp1'], $_POST['dencity_cair_komp2'], $_POST['dencity_cair_komp3'], $_POST['dencity_cair_komp4'], $_POST['temp_cair_komp_komp1'], $_POST['temp_cair_komp_komp2'], $_POST['temp_cair_komp_komp3'], $_POST['temp_cair_komp_komp4'], $_POST['id_jarak_cair_t1']);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // Update salib_ukur
        $stmt3 = mysqli_prepare($conn, "UPDATE salib_ukur SET ket_jarak_t1 = ?, ket_jarak_cair_t1 = ?, diperiksa_t1 = ?, diperiksa_segel = ? WHERE id_afrn = ?");
        mysqli_stmt_bind_param($stmt3, "ssssi", $_POST['ket_jarak_t1'], $_POST['ket_jarak_cair_t1'], $_POST['diperiksa_t1'], $_POST['diperiksa_segel'], $_POST['id_afrn']);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);

        // Update segel
        $stmt4 = mysqli_prepare($conn, "UPDATE segel SET mainhole1 = ?, mainhole2 = ?, mainhole3 = ?, mainhole4 = ?, bottom_load_cov1 = ?, bottom_load_cov2 = ?, bottom_load_cov3 = ?, bottom_load_cov4 = ?, bottom_load_cov5 = ? WHERE id_segel = ?");
        mysqli_stmt_bind_param($stmt4, "sssssssssi", $_POST['mainhole1'], $_POST['mainhole2'], $_POST['mainhole3'], $_POST['mainhole4'], $_POST['bottom_load_cov1'], $_POST['bottom_load_cov2'], $_POST['bottom_load_cov3'], $_POST['bottom_load_cov4'], $_POST['bottom_load_cov5'], $id_segel);
        mysqli_stmt_execute($stmt4);
        mysqli_stmt_close($stmt4);

        mysqli_commit($conn);
        echo "<script>alert('Data berhasil diperbarui.'); window.location.href='insert_salibukur.php';</script>";
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
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-lg font-normal text-gray-700 mb-4">Edit Data Segel</h1>
        <form action="edit_salib.php?idSegel=<?= $id_segel ?>" method="POST" class="p-4 bg-white shadow-md rounded">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mainhole 1</label>
                    <input type="text" name="mainhole1" value="<?= $data['mainhole1'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mainhole 2</label>
                    <input type="text" name="mainhole2" value="<?= $data['mainhole2'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mainhole 3</label>
                    <input type="text" name="mainhole3" value="<?= $data['mainhole3'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mainhole 4</label>
                    <input type="text" name="mainhole4" value="<?= $data['mainhole4'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bottom Load Cover 1</label>
                    <input type="text" name="bottom_load_cov1" value="<?= $data['bottom_load_cov1'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bottom Load Cover 2</label>
                    <input type="text" name="bottom_load_cov2" value="<?= $data['bottom_load_cov2'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bottom Load Cover 3</label>
                    <input type="text" name="bottom_load_cov3" value="<?= $data['bottom_load_cov3'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bottom Load Cover 4</label>
                    <input type="text" name="bottom_load_cov4" value="<?= $data['bottom_load_cov4'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bottom Load Cover 5</label>
                    <input type="text" name="bottom_load_cov5" value="<?= $data['bottom_load_cov5'] ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Jarak T1</label>
                <input type="text" name="ket_jarak_t1" value="<?= $data['ket_jarak_t1'] ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Jarak Cair T1</label>
                <input type="text" name="ket_jarak_cair_t1" value="<?= $data['ket_jarak_cair_t1'] ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Diperiksa Oleh</label>
                <input type="text" name="diperiksa_t1" value="<?= $data['diperiksa_t1'] ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Diperiksa Segel</label>
                <input type="text" name="diperiksa_segel" value="<?= $data['diperiksa_segel'] ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end gap-2 mt-4">
                <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                <button type="submit" name="update"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</body>

</html>