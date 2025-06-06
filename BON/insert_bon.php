<!DOCTYPE html>
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_afrn = $_POST['no_afrn'];
    $meter_awal = $_POST['meter_awal'];
    $total_meter_akhir = $_POST['total_meter_akhir'];
    $jlh_pengisian = $_POST['jlh_pengisian'];
    $masuk_dppu = $_POST['masuk_dppu'];
    $mulai_pengisian = $_POST['mulai_pengisian'];
    $selesai_pengisian = $_POST['selesai_pengisian'];
    $water_cont_ter = $_POST['water_cont_ter'];
    $keluar_dppu = $_POST['keluar_dppu'];

    $tgl_rekam = date('Y-m-d');

    $sql = "INSERT INTO bon (no_afrn, tgl_rekam, jlh_pengisian, meter_awal, total_meter_akhir, masuk_dppu, mulai_pengisian, selesai_pengisian, water_cont_ter, keluar_dppu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiisssss", $no_afrn, $tgl_rekam, $jlh_pengisian, $meter_awal, $total_meter_akhir, $masuk_dppu, $mulai_pengisian, $selesai_pengisian, $water_cont_ter, $keluar_dppu);

    if ($stmt->execute()) {
        header("Location: index.php?msg=sukses");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $conn->error;
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form Isian BON</title>
    <script>
    function hitungJumlah() {
        const awal = parseFloat(document.getElementById("meter_awal").value) || 0;
        const akhir = parseFloat(document.getElementById("meter_akhir").value) || 0;
        const jumlah = akhir - awal;
        document.getElementById("jlh_pengisian").value = jumlah > 0 ? jumlah : 0;
    }
    </script>
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
                <h1 class="text-xl text-gray-700 font-semibold mb-6">FORM ISIAN BON</h1>
                <form action="insert_bon.php" method="POST" class="space-y-4">
                    <label>
                        No. AFRN:
                        <select name="no_afrn" required class="block w-full border px-2 py-1">
                            <?php
                $afrn = $conn->query("SELECT no_afrn FROM afrn");
                while ($row = $afrn->fetch_assoc()) {
                    echo "<option value='{$row['no_afrn']}'>{$row['no_afrn']}</option>";
                }
                ?>
                        </select>
                    </label>

                    <div class="grid grid-cols-2 gap-4">
                        <label>
                            Meter Awal:
                            <input type="number" name="meter_awal" id="meter_awal" oninput="hitungJumlah()" required
                                class="block w-full border px-2 py-1">
                        </label>
                        <label>
                            Meter Akhir:
                            <input type="number" name="total_meter_akhir" id="meter_akhir" oninput="hitungJumlah()"
                                required class="block w-full border px-2 py-1">
                        </label>
                    </div>

                    <label>
                        Jumlah Pengisian:
                        <input type="number" name="jlh_pengisian" id="jlh_pengisian" readonly
                            class="block w-full bg-gray-100 border px-2 py-1">
                    </label>

                    <div class="grid grid-cols-3 gap-4">
                        <label>
                            Masuk DPPU:
                            <input type="time" name="masuk_dppu" required class="block w-full border px-2 py-1">
                        </label>
                        <label>
                            Mulai Pengisian:
                            <input type="time" name="mulai_pengisian" required class="block w-full border px-2 py-1">
                        </label>
                        <label>
                            Selesai Pengisian:
                            <input type="time" name="selesai_pengisian" required class="block w-full border px-2 py-1">
                        </label>
                        <label>
                            Water Contamination Ter:
                            <input type="time" name="water_cont_ter" required class="block w-full border px-2 py-1">
                        </label>
                        <label>
                            Keluar DPPU:
                            <input type="time" name="keluar_dppu" required class="block w-full border px-2 py-1">
                        </label>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Submit</button>
                        <a href="index.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</a>
                    </div>
                </form>
</body>

</html>