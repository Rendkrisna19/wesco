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

$success_message = '';
$error_message = '';

if (isset($_POST['insert'])) {
    // Use prepared statements to prevent SQL injection
    $nama = $_POST['nama_trans'];
    $alamat = $_POST['alamat_trans'];

    $stmt = $conn->prepare("INSERT INTO transportir (nama_trans, alamat_trans) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $alamat);

    if ($stmt->execute()) {
        $success_message = "Data transportir berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambahkan data: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Tambah Transportir</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <div class="flex-1 p-10 bg-white">
                <h2 class="text-xl font-bold mb-6 text-gray-700">Tambah Transportir</h2>

                <form method="POST" class="w-full bg-white p-6 rounded shadow-md">
                    <div class="mb-4">
                        <label class="block mb-2 text-gray-700">Nama Transportir</label>
                        <input type="text" name="nama_trans" required class="block w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-gray-700">Alamat Transportir</label>
                        <textarea name="alamat_trans" required class="block w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="insert"
                            class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Submit</button>
                        <a href="index.php" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($success_message): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $success_message ?>',
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
        text: '<?= $error_message ?>',
        confirmButtonText: 'OK'
    });
    </script>
    <?php endif; ?>
</body>

</html>