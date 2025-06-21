<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
include '../config/koneksi.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menggunakan ?? '' agar aman jika ada field yang tidak dikirim dari form
    $id_trans = $_POST['id_trans'] ?? '';
    $no_polisi = $_POST['no_polisi'] ?? '';
    $tgl_akhir = $_POST['tgl_akhir'] ?? '';
    $id_tipe_bridger = $_POST['id_tipe_bridger'] ?? '1';
    $volume = $_POST['volume'] ?? '';

    // Ambil input Tera 1–4, jika tidak ada, isi dengan string kosong
    $tera1 = $_POST['tera1'] ?? '';
    $tera2 = $_POST['tera2'] ?? '';
    $tera3 = $_POST['tera3'] ?? '';
    $tera4 = $_POST['tera4'] ?? '';

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO bridger (id_trans, no_polisi, tgl_serti_akhir, id_tipe_bridger, volume, tera1, tera2, tera3, tera4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Tipe data disesuaikan, i untuk integer, s untuk string
    $stmt->bind_param("sssisssss", $id_trans, $no_polisi, $tgl_akhir, $id_tipe_bridger, $volume, $tera1, $tera2, $tera3, $tera4);

    if ($stmt->execute()) {
        $success_message = "Data bridger berhasil disimpan!";
    } else {
        $error_message = "Gagal menyimpan data: " . $stmt->error;
    }
    $stmt->close();
}

$transportir = $conn->query("SELECT id_trans, nama_trans FROM transportir");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Form Bridger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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

            <div class="flex-1 p-10 bg-whiterounded shadow">
                <h2 class="text-xl font-bold mb-6 text-gray-700">FORM BRIDGER</h2>
                <form method="POST">
                    <label class="block mb-2 text-gray-600">Perusahaan Transportir</label>
                    <select name="id_trans" class="w-full border px-4 py-2 rounded mb-4" required>
                        <option value="">Pilih Transportir</option>
                        <?php while ($row = $transportir->fetch_assoc()): ?>
                        <option value="<?= $row['id_trans'] ?>"><?= htmlspecialchars($row['nama_trans']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label class="block mb-2 text-gray-600">Nomor Polisi</label>
                    <input type="text" name="no_polisi" class="w-full border px-4 py-2 rounded mb-4" required>

                    <label class="block mb-2 text-gray-600">Tanggal Akhir Berlaku</label>
                    <input type="date" name="tgl_akhir" class="w-full border px-4 py-2 rounded mb-4" required>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label class="block mb-2 text-gray-600">Vehicle Type</label>
                            <select name="id_tipe_bridger"
                                class="w-full border px-4 py-2 rounded text-gray-500 bg-gray-100 cursor-not-allowed"
                                disabled>
                                <option value="1" selected>BRIDGER</option>
                            </select>
                            <input type="hidden" name="id_tipe_bridger" value="1">
                        </div>

                        <div class="w-1/2">
                            <label class="block mb-2 text-gray-600">Volume</label>
                            <select name="volume" id="volumeSelect" class="w-full border px-4 py-2 rounded" required>
                                <option value="8000">8 KL</option>
                                <option value="16000">16 KL</option>
                                <option value="24000">24 KL</option>
                                <option value="32000">32 KL</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div id="tera1-wrapper">
                            <input type="text" name="tera1" id="tera1" placeholder="Tera 1"
                                class="w-full border px-4 py-2 rounded">
                        </div>
                        <div id="tera2-wrapper">
                            <input type="text" name="tera2" id="tera2" placeholder="Tera 2"
                                class="w-full border px-4 py-2 rounded">
                        </div>
                        <div id="tera3-wrapper">
                            <input type="text" name="tera3" id="tera3" placeholder="Tera 3"
                                class="w-full border px-4 py-2 rounded">
                        </div>
                        <div id="tera4-wrapper">
                            <input type="text" name="tera4" id="tera4" placeholder="Tera 4"
                                class="w-full border px-4 py-2 rounded">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen-elemen yang diperlukan
        const volumeSelect = document.getElementById('volumeSelect');
        const teraWrappers = [
            document.getElementById('tera1-wrapper'),
            document.getElementById('tera2-wrapper'),
            document.getElementById('tera3-wrapper'),
            document.getElementById('tera4-wrapper')
        ];
        const teraInputs = [
            document.getElementById('tera1'),
            document.getElementById('tera2'),
            document.getElementById('tera3'),
            document.getElementById('tera4')
        ];

        // Fungsi untuk update tampilan input Tera
        function updateTeraFields() {
            // Ambil nilai volume terpilih (misal: 8000, 16000)
            const selectedVolume = parseInt(volumeSelect.value, 10);
            // Hitung berapa kompartemen (tera) yang harus muncul
            // 8000 -> 1, 16000 -> 2, dst.
            const compartmentsToShow = selectedVolume / 8000;

            // Loop melalui semua input tera
            teraWrappers.forEach((wrapper, index) => {
                const input = teraInputs[index];
                // nomor tera dimulai dari 1 (index + 1)
                const teraNumber = index + 1;

                // Jika nomor tera kurang dari atau sama dengan jumlah yang harus ditampilkan
                if (teraNumber <= compartmentsToShow) {
                    wrapper.style.display = 'block'; // Tampilkan
                    input.disabled = false; // Aktifkan input agar nilainya dikirim
                    input.required = true; // Jadikan wajib diisi
                } else {
                    wrapper.style.display = 'none'; // Sembunyikan
                    input.disabled = true; // Non-aktifkan input agar nilainya TIDAK dikirim
                    input.required = false; // Jadikan tidak wajib diisi
                    input.value = ''; // Kosongkan nilainya
                }
            });
        }

        // Jalankan fungsi saat dropdown Volume diubah
        volumeSelect.addEventListener('change', updateTeraFields);

        // Jalankan fungsi sekali saat halaman pertama kali dimuat, untuk mengatur tampilan awal
        updateTeraFields();
    });
    </script>


    <?php if ($success_message): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= addslashes($success_message) ?>',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php'; // Redirect after user clicks OK
        }
    });
    </script>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?= addslashes($error_message) ?>',
        confirmButtonText: 'OK'
    });
    </script>
    <?php endif; ?>
</body>

</html>