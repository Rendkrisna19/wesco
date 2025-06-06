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
include '../config/koneksi.php'; // Pastikan file ini ada dan berisi $conn = mysqli_connect(...)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_bon = intval($_POST['id_bon']);
    $no_afrn = $_POST['no_afrn'];
    $meter_awal = floatval($_POST['meter_awal']);
    $total_meter_akhir = floatval($_POST['total_meter_akhir']);
    $jlh_pengisian = $total_meter_akhir - $meter_awal;

    $masuk_dppu = $_POST['masuk_dppu'];
    $mulai_pengisian = $_POST['mulai_pengisian'];
    $selesai_pengisian = $_POST['selesai_pengisian'];
    $water_cont_ter = $_POST['water_cont_ter'];
    $keluar_dppu = $_POST['keluar_dppu'];

    $query = "UPDATE bon SET 
                no_afrn='$no_afrn', 
                meter_awal='$meter_awal',
                total_meter_akhir='$total_meter_akhir',
                jlh_pengisian='$jlh_pengisian',
                masuk_dppu='$masuk_dppu',
                mulai_pengisian='$mulai_pengisian',
                selesai_pengisian='$selesai_pengisian',
                water_cont_ter='$water_cont_ter',
                keluar_dppu='$keluar_dppu'
              WHERE id_bon = $id_bon";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

// Ambil data untuk form edit
if (!isset($_GET['idBon'])) {
    die("ID BON tidak ditemukan!");
}

$idBon = intval($_GET['idBon']);
$bonResult = mysqli_query($conn, "SELECT * FROM bon WHERE id_bon = $idBon");
$bon = mysqli_fetch_assoc($bonResult);

$afrnResult = mysqli_query($conn, "SELECT no_afrn FROM afrn");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit BON</title>
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

            <div class="flex-1 p-10 bg-white">
                <h1 class="text-xl font-semibold mb-6 text-gray-700">FORM ISIAN BON</h1>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="id_bon" value="<?= $bon['id_bon'] ?>">


                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. ARFRN</label>
                            <select name="no_afrn" class="w-full mt-1 border border-gray-300 p-2 rounded">
                                <option disabled>Pilih AFRN</option>
                                <?php while ($row = mysqli_fetch_assoc($afrnResult)) : ?>
                                <option value="<?= $row['no_afrn'] ?>"
                                    <?= $bon['no_afrn'] == $row['no_afrn'] ? 'selected' : '' ?>>
                                    <?= $row['no_afrn'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Pengisian (otomatis)</label>
                            <input type="number" name="jlh_pengisian" id="jlh_pengisian"
                                value="<?= $bon['jlh_pengisian'] ?>" readonly
                                class="w-full mt-1 border border-gray-300 p-2 rounded bg-gray-100 text-gray-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meter Awal</label>
                            <input type="number" name="meter_awal" id="meter_awal" value="<?= $bon['meter_awal'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meter Akhir</label>
                            <input type="number" name="total_meter_akhir" id="meter_akhir"
                                value="<?= $bon['total_meter_akhir'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Masuk DPPU</label>
                            <input type="time" name="masuk_dppu" value="<?= $bon['masuk_dppu'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mulai Pengisian</label>
                            <input type="time" name="mulai_pengisian" value="<?= $bon['mulai_pengisian'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selesai Pengisian</label>
                            <input type="time" name="selesai_pengisian" value="<?= $bon['selesai_pengisian'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Water Contamination Ter</label>
                            <input type="time" name="water_cont_ter" value="<?= $bon['water_cont_ter'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keluar DPPU</label>
                            <input type="time" name="keluar_dppu" value="<?= $bon['keluar_dppu'] ?>"
                                class="w-full mt-1 border border-gray-300 p-2 rounded">
                        </div>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <a href="index.php" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Edit</button>
                    </div>
                </form>
            </div>

            <script>
            const meterAwal = document.getElementById('meter_awal');
            const meterAkhir = document.getElementById('meter_akhir');
            const jlhPengisian = document.getElementById('jlh_pengisian');

            function hitungPengisian() {
                const awal = parseFloat(meterAwal.value) || 0;
                const akhir = parseFloat(meterAkhir.value) || 0;
                const hasil = akhir - awal;
                jlhPengisian.value = hasil >= 0 ? hasil : 0;
            }

            meterAwal.addEventListener('input', hitungPengisian);
            meterAkhir.addEventListener('input', hitungPengisian);
            </script>
</body>

</html>