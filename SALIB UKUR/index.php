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
include '../config/koneksi.php'; // koneksi mysqli

try {
    // Mengambil semua data untuk daftar Salib Ukur
    // Di sini saya asumsikan idSegel di GET hanya untuk filter detail,
    // tapi untuk daftar keseluruhan, kita selalu ambil semua (atau dengan pagination jika banyak)
    $sql = "SELECT
                segel.id_segel,
                segel.mainhole1,
                segel.mainhole2,
                segel.mainhole3,  -- Tambahkan kolom ini
                segel.mainhole4,  -- Tambahkan kolom ini
                segel.bottom_load_cov1,
                segel.bottom_load_cov2,
                segel.bottom_load_cov3, -- Tambahkan kolom ini
                segel.bottom_load_cov4, -- Tambahkan kolom ini
                segel.bottom_load_cov5, -- Tambahkan kolom ini
                afrn.no_afrn,
                afrn.tgl_afrn,
                afrn.id_afrn,
                bridger.no_polisi,
                bridger.volume
            FROM segel
            JOIN salib_ukur ON segel.id_ukur = salib_ukur.id_ukur
            JOIN afrn ON salib_ukur.id_afrn = afrn.id_afrn
            JOIN bridger ON afrn.id_bridger = bridger.id_bridger
            ORDER BY segel.id_segel DESC"; // Mengurutkan berdasarkan id_segel DESC

    $result = $conn->query($sql);

    // Jika ada idSegel di GET, ini untuk skenario tampilan detail,
    // namun form daftar ini tidak memerlukannya secara langsung untuk menampilkan tabel.
    // Jika Anda ingin tabel ini juga bisa difilter, logika ini harus diperluas.
    // Untuk saat ini, saya hanya mengabaikan $_GET['idSegel'] saat menampilkan daftar.
    // Jika $_GET['idSegel'] digunakan untuk menampilkan *hanya* satu baris di tabel ini,
    // maka Anda perlu mengubah query $sql di atas menjadi prepared statement dengan WHERE clause.
    // Tapi biasanya, halaman daftar tidak pakai GET['idSegel'] kecuali untuk modal detail.
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Salib Ukur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    .font-modify {
        font-family: sans-serif;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-lg">
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

            <div class="p-6 flex-1 overflow-y-auto">
                <div class="flex justify-between items-center mb-2">

                    <div class="p-4 mt-2 flex-1">
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">DAFTAR SALIB UKUR</h2>

                        <a href="insert_salibukur.php"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow float-right mb-4">
                            Insert Salib Ukur
                        </a>

                        <div class="overflow-x-auto rounded-lg shadow bg-white clear-both">
                            <table class="min-w-full text-sm text-left text-gray-700">
                                <thead class="bg-white text-blue-700 font-bold">
                                    <tr>
                                        <th class="py-3 px-4">No</th>
                                        <th class="py-3 px-4">No AFRN</th>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-4">No Polisi</th>
                                        <th class="py-3 px-4">Volume Bridger</th>
                                        <th class="py-3 px-4">Segel Mainhole</th>
                                        <th class="py-3 px-4">Segel Bottom Loader</th>
                                        <th class="py-3 px-8">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4"><?= $no++ ?></td>
                                        <td class="py-2 px-4">
                                            <?= htmlspecialchars($row['id_afrn']) . ' / ' . htmlspecialchars($row['no_afrn']) ?>
                                        </td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['tgl_afrn']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['no_polisi']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($row['volume']) ?></td>
                                        <td class="py-2 px-4">
                                            <?= htmlspecialchars($row['mainhole1']) ?><br>

                                        </td>
                                        <td class="py-2 px-4">
                                            <?= htmlspecialchars($row['bottom_load_cov1']) ?><br>

                                        </td>
                                        <td class="py-2 px-4 flex gap-2">
                                            <a href="edit_salib.php?idSegel=<?= htmlspecialchars($row['id_segel']) ?>"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 border border-black rounded-full transition text-xs">
                                                Edit
                                            </a>
                                            <a href="cetak_salib.php?idSegel=<?= urlencode($row['id_segel']) ?>&id_afrn=<?= urlencode($row['id_afrn']) ?>"
                                                target="_blank"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 border border-black rounded-full transition text-xs">
                                                Cetak
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-gray-500">Data tidak ditemukan.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
    // Contoh fungsi delete dengan SweetAlert2 (jika diperlukan)
    /*
    function confirmDelete(idSegel) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to a delete script or submit a form
                window.location.href = 'delete_salib.php?idSegel=' + idSegel;
            }
        });
    }
    */

    // Notifikasi dari redirect jika ada
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('msg')) {
            const msg = urlParams.get('msg');
            let title = '';
            let text = '';
            let icon = '';

            if (msg === 'sukses') {
                title = 'Berhasil!';
                text = 'Operasi berhasil dilakukan.';
                icon = 'success';
            } else if (msg === 'gagal') {
                title = 'Gagal!';
                text = urlParams.has('error_detail') ? decodeURIComponent(urlParams.get('error_detail')) :
                    'Terjadi kesalahan saat operasi.';
                icon = 'error';
            }

            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                // Bersihkan parameter URL setelah SweetAlert ditutup
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
    </script>
</body>

</html>