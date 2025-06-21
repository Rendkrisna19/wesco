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

// --- FORM PROCESSING LOGIC (Handle POST request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_bon = intval($_POST['id_bon']);
    $no_afrn = $_POST['no_afrn']; // Nilai ini akan datang dari input yang readonly
    $meter_awal = floatval($_POST['meter_awal']);
    $total_meter_akhir = floatval($_POST['total_meter_akhir']);
    $jlh_pengisian = $total_meter_akhir - $meter_awal;

    $masuk_dppu = $_POST['masuk_dppu'];
    $mulai_pengisian = $_POST['mulai_pengisian'];
    $selesai_pengisian = $_POST['selesai_pengisian'];
    $water_cont_ter = $_POST['water_cont_ter'];
    $keluar_dppu = $_POST['keluar_dppu'];

    // Menggunakan Prepared Statements untuk keamanan
    $query = "UPDATE bon SET 
                no_afrn = ?, 
                meter_awal = ?,
                total_meter_akhir = ?,
                jlh_pengisian = ?,
                masuk_dppu = ?,
                mulai_pengisian = ?,
                selesai_pengisian = ?,
                water_cont_ter = ?,
                keluar_dppu = ?
              WHERE id_bon = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sdddsssssi",
        $no_afrn,
        $meter_awal,
        $total_meter_akhir,
        $jlh_pengisian,
        $masuk_dppu,
        $mulai_pengisian,
        $selesai_pengisian,
        $water_cont_ter,
        $keluar_dppu,
        $id_bon
    );

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Gagal mengupdate data: " . $stmt->error;
    }
    $stmt->close();
}

// --- DATA FETCHING FOR THE FORM ---
if (!isset($_GET['idBon'])) {
    die("ID BON tidak ditemukan!");
}

$idBon = intval($_GET['idBon']);

// Ambil data BON yang akan diedit (hanya ini query yang kita perlukan)
$bonResult = mysqli_query($conn, "SELECT * FROM bon WHERE id_bon = $idBon");
if(mysqli_num_rows($bonResult) == 0) {
    die("Data BON dengan ID $idBon tidak ditemukan.");
}
$bon = mysqli_fetch_assoc($bonResult);

// Query untuk mengambil daftar AFRN sudah dihapus karena tidak diperlukan lagi.
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit BON</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <h1 class="text-xl font-semibold mb-6 text-gray-700">FORM EDIT ISIAN BON</h1>

                <form method="POST" class="space-y-6 bg-white p-8 rounded-lg shadow-md">
                    <input type="hidden" name="id_bon" value="<?= $bon['id_bon'] ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="no_afrn" class="block text-sm font-medium text-gray-700">No. AFRN
                                (Terkunci)</label>
                            <input type="text" id="no_afrn" name="no_afrn"
                                value="<?= htmlspecialchars($bon['no_afrn']) ?>" readonly
                                class="w-full mt-1 border border-gray-300 p-2 rounded bg-gray-100 text-gray-600 focus:outline-none focus:ring-0 cursor-not-allowed">
                        </div>

                        <div>
                            <label for="jlh_pengisian" class="block text-sm font-medium text-gray-700">Jumlah Pengisian
                                (otomatis)</label>
                            <input type="number" name="jlh_pengisian" id="jlh_pengisian"
                                value="<?= $bon['jlh_pengisian'] ?>" readonly
                                class="w-full mt-1 border border-gray-300 p-2 rounded bg-gray-100 text-gray-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meter_awal" class="block text-sm font-medium text-gray-700">Meter Awal</label>
                            <input type="number" step="any" name="meter_awal" id="meter_awal"
                                value="<?= $bon['meter_awal'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label for="meter_akhir" class="block text-sm font-medium text-gray-700">Meter Akhir</label>
                            <input type="number" step="any" name="total_meter_akhir" id="meter_akhir"
                                value="<?= $bon['total_meter_akhir'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="masuk_dppu" class="block text-sm font-medium text-gray-700">Masuk DPPU</label>
                            <input type="time" id="masuk_dppu" name="masuk_dppu" value="<?= $bon['masuk_dppu'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label for="mulai_pengisian" class="block text-sm font-medium text-gray-700">Mulai
                                Pengisian</label>
                            <input type="time" id="mulai_pengisian" name="mulai_pengisian"
                                value="<?= $bon['mulai_pengisian'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label for="selesai_pengisian" class="block text-sm font-medium text-gray-700">Selesai
                                Pengisian</label>
                            <input type="time" id="selesai_pengisian" name="selesai_pengisian"
                                value="<?= $bon['selesai_pengisian'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="water_cont_ter" class="block text-sm font-medium text-gray-700">Water
                                Contamination Ter</label>
                            <input type="time" id="water_cont_ter" name="water_cont_ter"
                                value="<?= $bon['water_cont_ter'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label for="keluar_dppu" class="block text-sm font-medium text-gray-700">Keluar DPPU</label>
                            <input type="time" id="keluar_dppu" name="keluar_dppu" value="<?= $bon['keluar_dppu'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <a href="index.php"
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md shadow-md">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-md">Update
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const meterAwal = document.getElementById('meter_awal');
    const meterAkhir = document.getElementById('meter_akhir');
    const jlhPengisian = document.getElementById('jlh_pengisian');

    function hitungPengisian() {
        const awal = parseFloat(meterAwal.value) || 0;
        const akhir = parseFloat(meterAkhir.value) || 0;
        const hasil = akhir - awal;
        jlhPengisian.value = hasil >= 0 ? hasil.toFixed(2) : 0; // .toFixed(2) for 2 decimal places
    }

    meterAwal.addEventListener('input', hitungPengisian);
    meterAkhir.addEventListener('input', hitungPengisian);
    </script>
</body>

</html>