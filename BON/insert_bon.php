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

$swal_icon = '';
$swal_title = '';
$swal_text = '';
$swal_redirect_url = '';

// MODIFIKASI: Query diubah untuk mengambil HANYA 1 data AFRN terbaru yang belum punya BON.
$afrn_result = $conn->query("SELECT id_afrn, no_afrn FROM afrn WHERE id_bon IS NULL OR id_bon = 0 ORDER BY id_afrn DESC LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_afrn_selected = $_POST['no_afrn'];
    $meter_awal = $_POST['meter_awal'];
    $total_meter_akhir = $_POST['total_meter_akhir'];
    $jlh_pengisian = $_POST['jlh_pengisian'];
    $masuk_dppu = $_POST['masuk_dppu'];
    $mulai_pengisian = $_POST['mulai_pengisian'];
    $selesai_pengisian = $_POST['selesai_pengisian'];
    $water_cont_ter = $_POST['water_cont_ter'];
    $keluar_dppu = $_POST['keluar_dppu'];
    $tgl_rekam = date('Y-m-d');

    $sql_insert_bon = "INSERT INTO bon (no_afrn, tgl_rekam, jlh_pengisian, meter_awal, total_meter_akhir, masuk_dppu, mulai_pengisian, selesai_pengisian, water_cont_ter, keluar_dppu)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_bon = $conn->prepare($sql_insert_bon);
    $stmt_bon->bind_param("ssiiisssss",
        $no_afrn_selected, $tgl_rekam, $jlh_pengisian, $meter_awal, $total_meter_akhir,
        $masuk_dppu, $mulai_pengisian, $selesai_pengisian, $water_cont_ter, $keluar_dppu
    );

    if ($stmt_bon->execute()) {
        $last_bon_id = mysqli_insert_id($conn);
        $sql_update_afrn = "UPDATE afrn SET id_bon = ? WHERE no_afrn = ?";
        $stmt_afrn = $conn->prepare($sql_update_afrn);
        $stmt_afrn->bind_param("is", $last_bon_id, $no_afrn_selected);

        if ($stmt_afrn->execute()) {
            $swal_icon = 'success';
            $swal_title = 'Berhasil!';
            $swal_text = 'Data BON berhasil disimpan dan AFRN diperbarui!';
            $swal_redirect_url = 'index.php';
        } else {
            $swal_icon = 'error';
            $swal_title = 'Gagal!';
            $swal_text = 'Data BON disimpan, tetapi gagal memperbarui AFRN: ' . $stmt_afrn->error;
        }
        $stmt_afrn->close();
    } else {
        $swal_icon = 'error';
        $swal_title = 'Gagal!';
        $swal_text = 'Gagal menyimpan data BON: ' . $stmt_bon->error;
    }
    $stmt_bon->close();
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Isian BON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    .font-modify {
        font-family: sans-serif;
    }
    </style>
    <script>
    function hitungJumlah() {
        const awal = parseFloat(document.getElementById("meter_awal").value) || 0;
        const akhir = parseFloat(document.getElementById("meter_akhir").value) || 0;
        const jumlah = akhir - awal;
        document.getElementById("jlh_pengisian").value = jumlah > 0 ? jumlah : 0;
    }
    </script>
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
                <h1 class="text-xl text-gray-700 font-semibold mb-6">FORM ISIAN BON</h1>
                <form action="" method="POST" class="space-y-4 bg-white p-8 rounded-lg shadow-md">
                    <div>
                        <label for="no_afrn_select" class="block text-sm font-medium text-gray-700 mb-1">No.
                            AFRN:</label>
                        <select name="no_afrn" id="no_afrn_select" required
                            class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih No. AFRN</option>
                            <?php
                            // Loop ini sekarang hanya akan berjalan sekali (atau tidak sama sekali jika tidak ada AFRN tersedia)
                            while ($row = $afrn_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['no_afrn']) . "' selected>" . htmlspecialchars($row['no_afrn']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="meter_awal" class="block text-sm font-medium text-gray-700 mb-1">Meter
                                Awal:</label>
                            <input type="number" name="meter_awal" id="meter_awal" oninput="hitungJumlah()" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="meter_akhir" class="block text-sm font-medium text-gray-700 mb-1">Meter
                                Akhir:</label>
                            <input type="number" name="total_meter_akhir" id="meter_akhir" oninput="hitungJumlah()"
                                required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="jlh_pengisian" class="block text-sm font-medium text-gray-700 mb-1">Jumlah
                            Pengisian:</label>
                        <input type="number" name="jlh_pengisian" id="jlh_pengisian" readonly
                            class="block w-full bg-gray-100 border border-gray-300 px-3 py-2 rounded-md shadow-sm sm:text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="masuk_dppu" class="block text-sm font-medium text-gray-700 mb-1">Masuk
                                DPPU:</label>
                            <input type="time" name="masuk_dppu" id="masuk_dppu" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="mulai_pengisian" class="block text-sm font-medium text-gray-700 mb-1">Mulai
                                Pengisian:</label>
                            <input type="time" name="mulai_pengisian" id="mulai_pengisian" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="selesai_pengisian" class="block text-sm font-medium text-gray-700 mb-1">Selesai
                                Pengisian:</label>
                            <input type="time" name="selesai_pengisian" id="selesai_pengisian" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="water_cont_ter" class="block text-sm font-medium text-gray-700 mb-1">Water
                                Contamination Ter:</label>
                            <input type="time" name="water_cont_ter" id="water_cont_ter" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="keluar_dppu" class="block text-sm font-medium text-gray-700 mb-1">Keluar
                                DPPU:</label>
                            <input type="time" name="keluar_dppu" id="keluar_dppu" required
                                class="block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Ambil semua elemen input waktu yang kita perlukan
        const selesaiPengisianInput = document.getElementById('selesai_pengisian');
        const waterContInput = document.getElementById('water_cont_ter');
        const keluarDppuInput = document.getElementById('keluar_dppu');

        // 2. Buat fungsi helper untuk menambah menit ke sebuah waktu (format "HH:mm")
        function addMinutes(timeString, minutesToAdd) {
            // Pecah string waktu menjadi jam dan menit
            const [hours, minutes] = timeString.split(':').map(Number);

            // Buat objek Date untuk mempermudah kalkulasi
            const date = new Date();
            date.setHours(hours, minutes, 0, 0); // Atur jam dan menitnya

            // Tambahkan menit
            date.setMinutes(date.getMinutes() + minutesToAdd);

            // Format kembali ke "HH:mm" dengan padding nol jika perlu
            const newHours = String(date.getHours()).padStart(2, '0');
            const newMinutes = String(date.getMinutes()).padStart(2, '0');

            return `${newHours}:${newMinutes}`;
        }

        // 3. Tambahkan "pendengar" ke input 'Selesai Pengisian'
        // Acara 'change' akan berjalan ketika pengguna selesai memilih waktu
        selesaiPengisianInput.addEventListener('change', function() {
            const selesaiTime = this.value;

            // Pastikan ada waktu yang dipilih
            if (selesaiTime) {
                // Kalkulasi pertama: Selesai Pengisian + 20 menit
                const waterTime = addMinutes(selesaiTime, 20);
                waterContInput.value = waterTime;

                // Kalkulasi kedua: Waktu Water Contamination + 20 menit
                const keluarTime = addMinutes(waterTime, 20);
                keluarDppuInput.value = keluarTime;
            }
        });
    });
    </script>

    <?php if ($swal_icon): ?>
    <script>
    Swal.fire({
        icon: '<?php echo $swal_icon; ?>',
        title: '<?php echo $swal_title; ?>',
        text: '<?php echo addslashes($swal_text); ?>',
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