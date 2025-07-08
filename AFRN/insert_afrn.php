<?php
session_start();
include '../config/koneksi.php';

// Cek status login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/login.php"); // Sesuaikan dengan path login Anda
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;

// Ambil data untuk dropdowns
$bridger_result = mysqli_query($conn, "SELECT bridger.id_bridger, bridger.no_polisi, bridger.volume, transportir.nama_trans AS transportir FROM bridger LEFT JOIN transportir ON bridger.id_trans = transportir.id_trans");
$destinasi_result = mysqli_query($conn, "SELECT * FROM destinasi");
$driver_result = mysqli_query($conn, "SELECT * FROM driver");
$tangki_result = mysqli_query($conn, "SELECT * FROM tangki");

// Inisialisasi variabel untuk pesan SweetAlert
$swal_icon = '';
$swal_title = '';
$swal_text = '';
$swal_redirect_url = '';


//======================================================================
// FUNGSI UNTUK MEMFORMAT NOMOR AFRN
//======================================================================
function format_afrn_number($id, $tanggal) {
    $roman_months = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $date_obj = date_create($tanggal);
    $month = (int)date_format($date_obj, 'n');
    $year = date_format($date_obj, 'Y');
    $roman_month = $roman_months[$month];
    return sprintf('%d/AFRN/%s/%d', $id, $roman_month, $year);
}


//======================================================================
// PROSES FORM SUBMISSION
//======================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $tgl_afrn = date('Y-m-d');
    $no_bpp = $_POST['no_bpp'];
    $id_bridger = $_POST['id_bridger'];
    $id_destinasi = $_POST['id_destinasi'];
    $id_driver = $_POST['id_driver'];
    $id_tangki = $_POST['id_tangki'];
    $dibuat = $username;
    $diperiksa = $_POST['diperiksa'];
    $disetujui = $_POST['disetujui'];
    $rit = (int)$_POST['rit'];

    // Ambil id_transportir berdasarkan id_bridger
    $stmt_trans = $conn->prepare("SELECT id_trans FROM bridger WHERE id_bridger = ?");
    $stmt_trans->bind_param("i", $id_bridger);
    $stmt_trans->execute();
    $trans_result = $stmt_trans->get_result();
    
    if ($trans_result->num_rows == 0) {
        $swal_icon = 'error';
        $swal_title = 'Gagal!';
        $swal_text = 'ID Bridger tidak ditemukan.';
    } else {
        $id_transportasi = $trans_result->fetch_assoc()['id_trans'];
        
        // =========================================================================
        // == MODIFIKASI UTAMA: MENGGUNAKAN TRANSAKSI DATABASE ==
        // =========================================================================
        
        $conn->begin_transaction(); // Mulai transaksi

        try {
            // LANGKAH 1: INSERT data ke 'afrn' tanpa 'no_afrn'
            // Kolom 'no_afrn' sengaja dikosongkan untuk diisi nanti
            $query_insert = "INSERT INTO afrn (
                tgl_afrn, no_bpp, id_bridger, id_transportir, id_destinasi, 
                id_tangki, id_driver, dibuat, diperiksa, disetujui, rit
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $conn->prepare($query_insert);
            // Tipe data disesuaikan (11 placeholder), 's' untuk no_bpp dihilangkan sementara
            $stmt_insert->bind_param("ssiiisissii",
                $tgl_afrn, $no_bpp, $id_bridger, $id_transportasi, $id_destinasi,
                $id_tangki, $id_driver, $dibuat, $diperiksa, $disetujui, $rit
            );
            $stmt_insert->execute();

            // LANGKAH 2: Dapatkan ID dari baris yang baru saja di-insert
            $last_id_afrn = $conn->insert_id;

            // LANGKAH 3: Buat no_afrn yang sudah diformat menggunakan ID tersebut
            $no_afrn_formatted = format_afrn_number($last_id_afrn, $tgl_afrn);

            // LANGKAH 4: UPDATE baris yang sama untuk mengisi kolom 'no_afrn'
            $query_update = "UPDATE afrn SET no_afrn = ? WHERE id_afrn = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("si", $no_afrn_formatted, $last_id_afrn);
            $stmt_update->execute();
            
            // Jika semua query berhasil, simpan perubahan
            $conn->commit();

            // Siapkan pesan sukses
            $swal_icon = 'success';
            $swal_title = 'Berhasil!';
            $swal_text = 'Data AFRN dengan No. ' . $no_afrn_formatted . ' berhasil disimpan!';
            $swal_redirect_url = 'index.php';

        } catch (mysqli_sql_exception $exception) {
            // Jika ada satu saja query yang gagal, batalkan semua perubahan
            $conn->rollback();

            // Siapkan pesan error
            $swal_icon = 'error';
            $swal_title = 'Gagal!';
            $swal_text = 'Gagal menyimpan data AFRN: ' . $exception->getMessage();
        } finally {
            // Tutup semua statement yang sudah dibuka
            if (isset($stmt_insert)) $stmt_insert->close();
            if (isset($stmt_update)) $stmt_update->close();
        }
    }
    $stmt_trans->close();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Isian AFRN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    .font-modify {
        font-family: sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 font-modify">
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
                <h2 class="text-xl font-semibold mb-6 text-gray-800">FORM ISIAN AFRN (AVIATION FUEL DELIVERY RELEASE
                    NOTE)</h2>
                <form action="" method="POST" class="space-y-6 bg-white p-8 rounded-lg shadow-md">

                    <div>
                        <label for="no_bpp" class="block text-sm font-medium text-gray-700 mb-1">No. BPP/PNBP</label>
                        <input type="text" name="no_bpp" id="no_bpp"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required placeholder="Contoh: 8106590006">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="bridger" class="block text-sm font-medium text-gray-700 mb-1">Pilih Nomor Polisi
                                Kendaraan</label>
                            <select name="id_bridger" id="bridger"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required onchange="setBridgerData(this)">
                                <option class="text-gray-400" value="">Pilih Nomor Polisi</option>
                                <?php
                                mysqli_data_seek($bridger_result, 0); 
                                while ($b = mysqli_fetch_assoc($bridger_result)): ?>
                                <option value="<?= htmlspecialchars($b['id_bridger']) ?>"
                                    data-transportir="<?= htmlspecialchars($b['transportir']) ?>"
                                    data-volume="<?= htmlspecialchars($b['volume']) ?>">
                                    <?= htmlspecialchars($b['no_polisi']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label for="transportir"
                                class="block text-sm font-medium text-gray-700 mb-1">Transportir</label>
                            <input type="text" id="transportir"
                                class="w-full p-2 rounded bg-gray-100 border border-gray-300" readonly>
                        </div>
                        <div>
                            <label for="volume" class="block text-sm font-medium text-gray-700 mb-1">Volume</label>
                            <input type="text" id="volume" class="w-full p-2 rounded bg-gray-100 border border-gray-300"
                                readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="id_destinasi"
                                class="block text-sm font-medium text-gray-700 mb-1">Kepada</label>
                            <select name="id_destinasi" id="id_destinasi"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Pilih Destinasi</option>
                                <?php
                                mysqli_data_seek($destinasi_result, 0);
                                while ($d = mysqli_fetch_assoc($destinasi_result)): ?>
                                <option value="<?= htmlspecialchars($d['id_destinasi']) ?>">
                                    <?= htmlspecialchars($d['nama_destinasi']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label for="id_driver"
                                class="block text-sm font-medium text-gray-700 mb-1">Pengemudi</label>
                            <select name="id_driver" id="id_driver"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Pilih Driver</option>
                                <?php
                                mysqli_data_seek($driver_result, 0);
                                while ($p = mysqli_fetch_assoc($driver_result)): ?>
                                <option value="<?= htmlspecialchars($p['id_driver']) ?>">
                                    <?= htmlspecialchars($p['nama_driver']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label for="rit" class="block text-sm font-medium text-gray-700 mb-1">RIT</label>
                            <input type="number" name="rit" id="rit"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required placeholder="Contoh: 1">
                        </div>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tangki" class="block text-sm font-medium text-gray-700 mb-1">Nomor
                                Tangki</label>
                            <select name="id_tangki" id="tangki"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required onchange="setTangkiData(this)">
                                <option value="">Pilih Tangki</option>
                                <?php
                                mysqli_data_seek($tangki_result, 0);
                                while ($t = mysqli_fetch_assoc($tangki_result)): ?>
                                <option value="<?= htmlspecialchars($t['id_tangki']) ?>"
                                    data-no_batch="<?= htmlspecialchars($t['no_bacth']) ?>"
                                    data-source="<?= htmlspecialchars($t['source']) ?>"
                                    data-test_report="<?= htmlspecialchars($t['test_report_let']) ?>"
                                    data-density="<?= htmlspecialchars($t['density']) ?>"
                                    data-temperature="<?= htmlspecialchars($t['temperature']) ?>"
                                    data-cu="<?= htmlspecialchars($t['cu']) ?>">
                                    <?= htmlspecialchars($t['no_tangki']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-3 gap-4"> <input type="text" id="no_batch"
                                class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1" placeholder="No Batch"
                                readonly>
                            <input type="text" id="source"
                                class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1" placeholder="Source"
                                readonly>
                            <input type="text" id="test_report"
                                class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1"
                                placeholder="Test Report" readonly>
                            <input type="text" id="density"
                                class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1" placeholder="Density"
                                readonly>
                            <input type="text" id="temperature"
                                class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1"
                                placeholder="Temperature" readonly>
                            <input type="text" id="cu" class="p-2 bg-gray-100 rounded border border-gray-300 col-span-1"
                                placeholder="CU" readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="dibuat" class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                            <input type="text" name="dibuat" id="dibuat"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed"
                                value="<?= htmlspecialchars($username) ?>" readonly>
                        </div>
                        <div>
                            <label for="diperiksa" class="block text-sm font-medium text-gray-700 mb-1">Diperiksa
                                Oleh</label>
                            <input type="text" name="diperiksa" id="diperiksa"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required placeholder="Diperiksa Oleh">
                        </div>
                        <div>
                            <label for="disetujui" class="block text-sm font-medium text-gray-700 mb-1">Disetujui
                                Oleh</label>
                            <input type="text" name="disetujui" id="disetujui"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required placeholder="Disetujui Oleh">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="window.location.href='index.php'"
                            class="bg-red-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md shadow-md">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-md">Submit</button>
                    </div>
                </form>
            </div>
            <?php include_once '../components/footer.php'; ?>
        </div>

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