<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;

include '../config/koneksi.php';

// Inisialisasi variabel untuk pesan SweetAlert (Jika Anda ingin menggunakannya di halaman ini juga)
$swal_icon = '';
$swal_title = '';
$swal_text = '';
$swal_redirect_url = '';

// Ambil ID dari parameter GET
if (isset($_GET['idSegel'])) {
    $id_segel_to_edit = mysqli_real_escape_string($conn, $_GET['idSegel']); // Ganti nama variabel agar tidak bentrok dengan $id_segel di POST

    // Query untuk mengambil data berdasarkan ID segel
    $query_edit_data = "SELECT
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
        bon.keluar_dppu, -- Pastikan ini diambil dari tabel bon
        bon.tgl_rekam    -- Pastikan ini diambil dari tabel bon
    FROM segel
    LEFT JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
    LEFT JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
    LEFT JOIN bon ON afrn.id_bon = bon.id_bon -- Join bon untuk mendapatkan keluar_dppu
    LEFT JOIN jarak_t1 ON salib_ukur.id_jarak_t1 = jarak_t1.id_jarak_t1
    LEFT JOIN jarak_cair_t1 ON salib_ukur.id_jarak_cair_t1 = jarak_cair_t1.id_jarak_cair_t1
    LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
    WHERE segel.id_segel = ? LIMIT 1";

    $stmt_edit = $conn->prepare($query_edit_data);
    if (!$stmt_edit) {
        die("Error preparing edit query: " . $conn->error);
    }
    $stmt_edit->bind_param("i", $id_segel_to_edit);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $data = mysqli_fetch_assoc($result_edit);
    $stmt_edit->close();

    if (!$data) {
        // Jika data tidak ditemukan, redirect atau tampilkan pesan error
        header("Location: index.php?msg=gagal&error_detail=" . urlencode("Data tidak ditemukan untuk ID Segel tersebut."));
        exit;
    }
} else {
    // Jika tidak ada ID Segel di URL, redirect atau tampilkan pesan error
    header("Location: index.php?msg=gagal&error_detail=" . urlencode("ID Segel tidak diberikan."));
    exit;
}


// ============================
// 2. Proses Update Data
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    mysqli_begin_transaction($conn);
    try {
        // Ambil ID dari hidden input form
        $id_segel_post = $_POST['id_segel']; // Pastikan ini dikirim dari form hidden input
        $id_ukur_post = $_POST['id_ukur'];
        $id_jarak_t1_post = $_POST['id_jarak_t1'];
        $id_jarak_cair_t1_post = $_POST['id_jarak_cair_t1'];

        // Update jarak_t1
        $stmt1 = mysqli_prepare($conn, "UPDATE jarak_t1 SET jarak_komp1 = ?, jarak_komp2 = ?, jarak_komp3 = ?, jarak_komp4 = ?, temp_komp1 = ?, temp_komp2 = ?, temp_komp3 = ?, temp_komp4 = ? WHERE id_jarak_t1 = ?");
        if (!$stmt1) throw new Exception("Query update jarak_t1 gagal: " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt1, "ddddddddi", $_POST['jarak_komp1'], $_POST['jarak_komp2'], $_POST['jarak_komp3'], $_POST['jarak_komp4'], $_POST['temp_komp1'], $_POST['temp_komp2'], $_POST['temp_komp3'], $_POST['temp_komp4'], $id_jarak_t1_post);
        if (!mysqli_stmt_execute($stmt1)) throw new Exception("Eksekusi jarak_t1 gagal: " . mysqli_stmt_error($stmt1));
        mysqli_stmt_close($stmt1);

        // Update jarak_cair_t1
        $stmt2 = mysqli_prepare($conn, "UPDATE jarak_cair_t1 SET jarak_cair_komp1 = ?, jarak_cair_komp2 = ?, jarak_cair_komp3 = ?, jarak_cair_komp4 = ?, dencity_cair_komp1 = ?, dencity_cair_komp2 = ?, dencity_cair_komp3 = ?, dencity_cair_komp4 = ?, temp_cair_komp_komp1 = ?, temp_cair_komp_komp2 = ?, temp_cair_komp_komp3 = ?, temp_cair_komp_komp4 = ? WHERE id_jarak_cair_t1 = ?");
        if (!$stmt2) throw new Exception("Query update jarak_cair_t1 gagal: " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt2, "ddddddddddddd", $_POST['jarak_cair_komp1'], $_POST['jarak_cair_komp2'], $_POST['jarak_cair_komp3'], $_POST['jarak_cair_komp4'], $_POST['dencity_cair_komp1'], $_POST['dencity_cair_komp2'], $_POST['dencity_cair_komp3'], $_POST['dencity_cair_komp4'], $_POST['temp_cair_komp_komp1'], $_POST['temp_cair_komp_komp2'], $_POST['temp_cair_komp_komp3'], $_POST['temp_cair_komp_komp4'], $id_jarak_cair_t1_post);
        if (!mysqli_stmt_execute($stmt2)) throw new Exception("Eksekusi jarak_cair_t1 gagal: " . mysqli_stmt_error($stmt2));
        mysqli_stmt_close($stmt2);

        // Update salib_ukur
        $stmt3 = mysqli_prepare($conn, "UPDATE salib_ukur SET ket_jarak_t1 = ?, ket_jarak_cair_t1 = ?, diperiksa_t1 = ?, diperiksa_segel = ? WHERE id_ukur = ?");
        if (!$stmt3) throw new Exception("Query update salib_ukur gagal: " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt3, "ssssi", $_POST['ket_jarak_t1'], $_POST['ket_jarak_cair_t1'], $_POST['diperiksa_t1'], $_POST['diperiksa_segel'], $id_ukur_post);
        if (!mysqli_stmt_execute($stmt3)) throw new Exception("Eksekusi salib_ukur gagal: " . mysqli_stmt_error($stmt3));
        mysqli_stmt_close($stmt3);

        // Update segel
        $stmt4 = mysqli_prepare($conn, "UPDATE segel SET mainhole1 = ?, mainhole2 = ?, mainhole3 = ?, mainhole4 = ?, bottom_load_cov1 = ?, bottom_load_cov2 = ?, bottom_load_cov3 = ?, bottom_load_cov4 = ?, bottom_load_cov5 = ? WHERE id_segel = ?");
        if (!$stmt4) throw new Exception("Query update segel gagal: " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt4, "sssssssssi", $_POST['mainhole1'], $_POST['mainhole2'], $_POST['mainhole3'], $_POST['mainhole4'], $_POST['bottom_load_cov1'], $_POST['bottom_load_cov2'], $_POST['bottom_load_cov3'], $_POST['bottom_load_cov4'], $_POST['bottom_load_cov5'], $id_segel_post);
        if (!mysqli_stmt_execute($stmt4)) throw new Exception("Eksekusi segel gagal: " . mysqli_stmt_error($stmt4));
        mysqli_stmt_close($stmt4);

        mysqli_commit($conn);
        $swal_icon = 'success';
        $swal_title = 'Berhasil!';
        $swal_text = 'Data berhasil diperbarui.';
        $swal_redirect_url = 'index.php'; // Redirect ke halaman utama setelah update
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $swal_icon = 'error';
        $swal_title = 'Gagal!';
        $swal_text = 'Gagal memperbarui data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
        $swal_redirect_url = 'edit_salib.php?idSegel=' . $id_segel_post; // Kembali ke halaman edit dengan ID yang sama
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    .font-modify {
        font-family: sans-serif;
    }

    .input-disabled {
        background-color: #e2e8f0;
        /* bg-gray-200 */
        cursor: not-allowed;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>
        <div class="flex-1 flex flex-col">
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="relative group">
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                        <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                            alt="User" class="w-8 h-8 rounded-full">
                    </div>

                    <div
                        class="absolute hidden group-hover:block right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10 ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="../auth/index.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-cyan-700 hover:text-white">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 p-10 bg-gray-50">
                <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6">
                    <div class="text-gray-700 mb-4">
                        <h1 class="text-lg font-normal">EDIT DATA PENCATATAN HASIL PEMERIKSAAN BRIDGER SEBELUM KELUAR
                            LOKASI</h1>
                    </div>
                    <form action="edit_salib.php?idSegel=<?= htmlspecialchars($id_segel_to_edit) ?>" method="POST"
                        class="p-4 bg-white shadow-md rounded space-y-6">
                        <input type="hidden" name="id_segel" value="<?= htmlspecialchars($data['id_segel'] ?? '') ?>">
                        <input type="hidden" name="id_ukur" value="<?= htmlspecialchars($data['id_ukur'] ?? '') ?>">
                        <input type="hidden" name="id_jarak_t1"
                            value="<?= htmlspecialchars($data['id_jarak_t1'] ?? '') ?>">
                        <input type="hidden" name="id_jarak_cair_t1"
                            value="<?= htmlspecialchars($data['id_jarak_cair_t1'] ?? '') ?>">

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. AFRN</label>
                                <input type="text" name="no_afrn"
                                    value="<?= htmlspecialchars($data['no_afrn'] ?? '') ?>" readonly
                                    class="w-full bg-blue-700 text-white px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="tgl_afrn"
                                    value="<?= htmlspecialchars($data['tgl_afrn'] ?? '') ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No Polisi</label>
                                <input type="text" name="no_polisi"
                                    value="<?= htmlspecialchars($data['no_polisi'] ?? '') ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Volume Bridger</label>
                                <input type="text" name="volume" value="<?= htmlspecialchars($data['volume'] ?? '') ?>"
                                    readonly class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku Tangki</label>
                                <input type="date" name="tgl_serti_akhir"
                                    value="<?= htmlspecialchars($data['tgl_serti_akhir'] ?? '') ?>" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled">
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <p class="text-center text-sm text-gray-600 font-medium">
                                PEMERIKSAAN DAN PENCATATAN MINIMAL 10 MENIT SETELAH SETTLING TIME
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-4">JARAK T1 PADA DOKUMEN</h3>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input name="jarak_komp<?= $i ?>" type="number" step="0.1"
                                            value="<?= htmlspecialchars($data['jarak_komp' . $i] ?? '') ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp<?= $i ?>" type="number" step="0.1"
                                            value="<?= htmlspecialchars($data['temp_komp' . $i] ?? '') ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">Komp <?= $i ?>.</div>
                                <?php endfor; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3"
                                        name="ket_jarak_t1"><?= htmlspecialchars($data['ket_jarak_t1'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-2">JARAK CAIRAN TERHADAP T1</h3>
                                <p class="text-center text-xs text-gray-500 mb-4">(ULLAGE) @ SUPPLY POINT</p>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" name="jarak_cair_komp<?= $i ?>"
                                            value="<?= htmlspecialchars($data['jarak_cair_komp' . $i] ?? '') ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" name="dencity_cair_komp<?= $i ?>"
                                            value="<?= htmlspecialchars($data['dencity_cair_komp' . $i] ?? '') ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" name="temp_cair_komp_komp<?= $i ?>"
                                            value="<?= htmlspecialchars($data['temp_cair_komp_komp' . $i] ?? '') ?>"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                                <?php endfor; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3"
                                        name="ket_jarak_cair_t1"><?= htmlspecialchars($data['ket_jarak_cair_t1'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="max-w-7xl mx-auto bg-white p-6 rounded-md shadow space-y-6 mt-6">
                            <h2 class="text-center text-md font-normal text-gray-700 uppercase">
                                Pemeriksaan dan Pencatatan Minimal 10 Menit Setelah Settling Time
                            </h2>
                            <p class="text-center text-sm font-medium text-gray-600">Nomor/Kode Segel</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Mainhole <?= $i ?></label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="mainhole<?= $i ?>"
                                        value="<?= htmlspecialchars($data['mainhole' . $i] ?? '') ?>">
                                </div>
                                <?php endfor; ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Bottom Loader Cover <?= $i ?></label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="bottom_load_cov<?= $i ?>"
                                        value="<?= htmlspecialchars($data['bottom_load_cov' . $i] ?? '') ?>">
                                </div>
                                <?php endfor; ?>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-normal block mb-1">Jam Keluar</label>
                                    <input type="time" name="keluar_dppu" id="keluar_dppu"
                                        class="w-full bg-gray-100 border rounded px-3 py-2 text-sm input-disabled"
                                        value="<?= htmlspecialchars($data['keluar_dppu'] ?? '') ?>" readonly>
                                </div>
                                <div>
                                    <label class="text-sm font-normal block mb-1">Diperiksa & Dicatat Oleh</label>
                                    <input type="text" class="w-full border rounded px-3 py-2 text-sm"
                                        name="diperiksa_segel"
                                        value="<?= htmlspecialchars($data['diperiksa_segel'] ?? '') ?>">
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
    <?php include_once '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <?php if ($swal_icon): ?>
    <script>
    Swal.fire({
        icon: '<?php echo $swal_icon; ?>',
        title: '<?php echo $swal_title; ?>',
        text: '<?php echo $swal_text; ?>',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    }).then((result) => {
        <?php if ($swal_redirect_url): ?>
        if (result.isConfirmed) {
            window.location.href = '<?php echo $swal_redirect_url; ?>';
        }
        <?php endif; ?>
    });
    </script>
    <?php endif; ?>
</body>

</html>