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

include '../config/koneksi.php'; // Pastikan path ini benar

// Inisialisasi variabel untuk pesan SweetAlert
$swal_icon = '';
$swal_title = '';
$swal_text = '';
$swal_redirect_url = '';

// Ambil semua no_afrn yang belum punya id_bon
// Tambahan: Hanya ambil AFRN yang id_bon-nya masih NULL atau 0
$afrn_result = $conn->query("SELECT id_afrn, no_afrn FROM afrn WHERE id_bon IS NULL OR id_bon = 0");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_afrn_selected = $_POST['no_afrn']; // Ini adalah no_afrn yang dipilih dari dropdown
    $meter_awal = $_POST['meter_awal'];
    $total_meter_akhir = $_POST['total_meter_akhir'];
    $jlh_pengisian = $_POST['jlh_pengisian'];
    $masuk_dppu = $_POST['masuk_dppu'];
    $mulai_pengisian = $_POST['mulai_pengisian'];
    $selesai_pengisian = $_POST['selesai_pengisian'];
    $water_cont_ter = $_POST['water_cont_ter'];
    $keluar_dppu = $_POST['keluar_dppu'];

    $tgl_rekam = date('Y-m-d');

    // 1. Insert data ke tabel bon
    $sql_insert_bon = "INSERT INTO bon (no_afrn, tgl_rekam, jlh_pengisian, meter_awal, total_meter_akhir, masuk_dppu, mulai_pengisian, selesai_pengisian, water_cont_ter, keluar_dppu)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_bon = $conn->prepare($sql_insert_bon);
    // Perhatikan: tipe data untuk water_cont_ter dan keluar_dppu biasanya 'time' atau 'datetime', jadi 's' (string) sudah benar.
    // jlh_pengisian, meter_awal, total_meter_akhir adalah integer
    $stmt_bon->bind_param("ssiiisssss",
        $no_afrn_selected, $tgl_rekam, $jlh_pengisian, $meter_awal, $total_meter_akhir,
        $masuk_dppu, $mulai_pengisian, $selesai_pengisian, $water_cont_ter, $keluar_dppu
    );

    if ($stmt_bon->execute()) {
        $last_bon_id = mysqli_insert_id($conn); // Ambil ID terakhir yang di-insert ke tabel bon

        // 2. Update tabel afrn dengan id_bon yang baru saja dibuat
        // Cari id_afrn berdasarkan no_afrn yang dipilih
        $sql_update_afrn = "UPDATE afrn SET id_bon = ? WHERE no_afrn = ?";
        $stmt_afrn = $conn->prepare($sql_update_afrn);
        $stmt_afrn->bind_param("is", $last_bon_id, $no_afrn_selected);

        if ($stmt_afrn->execute()) {
            $swal_icon = 'success';
            $swal_title = 'Berhasil!';
            $swal_text = 'Data BON berhasil disimpan dan AFRN diperbarui!';
            $swal_redirect_url = 'index.php'; // Kembali ke halaman index atau daftar BON
        } else {
            // Jika update AFRN gagal, mungkin Anda ingin menghapus BON yang baru saja dibuat
            // Atau setidaknya beri tahu pengguna ada masalah
            $swal_icon = 'error';
            $swal_title = 'Gagal!';
            $swal_text = 'Data BON disimpan, tetapi gagal memperbarui AFRN: ' . mysqli_error($conn);
            // Optional: Hapus bon yang baru diinsert jika update AFRN gagal
            // $conn->query("DELETE FROM bon WHERE id_bon = $last_bon_id");
        }
        $stmt_afrn->close();
    } else {
        $swal_icon = 'error';
        $swal_title = 'Gagal!';
        $swal_text = 'Gagal menyimpan data BON: ' . mysqli_error($conn);
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
    /* Custom font if 'font-modify' isn't a standard Tailwind font */
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
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
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
                            // Reset pointer for $afrn_result if it was used elsewhere
                            // mysqli_data_seek($afrn_result, 0); // Only needed if you re-use $afrn_result after a loop
                            while ($row = $afrn_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['no_afrn']) . "'>" . htmlspecialchars($row['no_afrn']) . "</option>";
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <?php if ($swal_icon): // Tampilkan SweetAlert jika ada pesan ?>
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