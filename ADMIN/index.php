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

// Proses simpan data jika form pegawai disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role']) && $_POST['role'] === 'Pegawai') {
    $nama_lengkap   = $_POST['nama_lengkap'];
    $alamat         = $_POST['alamat'];
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $username       = $_POST['username'];
    $password       = $_POST['password'];
    $confirm        = $_POST['confirm_password'];
    $id_role        = 2; // Misal: 2 untuk Pegawai, sesuaikan dengan tabel role kamu

    // Validasi konfirmasi password
    if ($password !== $confirm) {
        echo "<script>alert('Password dan Confirm Password tidak sama!'); window.history.back();</script>";
        exit;
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Upload file TTD
    $gambar_ttd = '';
    if (isset($_FILES['ttd']) && $_FILES['ttd']['error'] == 0) {
        $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
        $gambar_ttd = 'ttd_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['ttd']['tmp_name'], '../uploads/' . $gambar_ttd);
    }

    // Upload file Stempel
    $gambar_stempel = '';
    if (isset($_FILES['stempel']) && $_FILES['stempel']['error'] == 0) {
        $ext2 = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
        $gambar_stempel = 'stempel_' . time() . '.' . $ext2;
        move_uploaded_file($_FILES['stempel']['tmp_name'], '../uploads/' . $gambar_stempel);
    }

    // Simpan ke database
    $sql = "INSERT INTO user (nama_lengkap, alamat, tempat_lahir, tanggal_lahir, username, password, id_role, gambar_ttd, gambar_stempel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssiss", $nama_lengkap, $alamat, $tempat_lahir, $tanggal_lahir, $username, $password_hash, $id_role, $gambar_ttd, $gambar_stempel);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo "<script>alert('Data pegawai berhasil disimpan!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>
<?php

// Tangani request AJAX
if (isset($_GET['load_form'])) {
  $role = $_GET['load_form'];

  if ($role == 'pegawai') {
    ?>
<!-- Form untuk Pegawai -->
<div class="space-y-4">
    <div>
        <label class="block font-semisemibold">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block font-semisemibold">Alamat</label>
        <textarea name="alamat" class="w-full border rounded px-3 py-2" required></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semisemibold">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semisemibold">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>
    <div>
        <select name="role" class="w-full border border-blue-200 p-2 rounded bg-blue-50 text-gray-700" readonly
            disabled>
            <option value="Pegawai" selected>Pegawai</option>
        </select>
        <input type="hidden" name="role" value="Pegawai"><!-- Tambahkan hidden agar role tetap terkirim -->
    </div>
    <div>
        <label class="block font-semisemibold">Username</label>
        <input type="text" name="username" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semisemibold">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semisemibold">Confirm Password</label>
            <input type="password" name="confirm_password" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semisemibold">Pilih File Gambar TTD (PNG, JPEG, JPG):</label>
            <input type="file" name="ttd" accept=".png,.jpg,.jpeg" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block font-semisemibold">Pilih File Gambar Stempel (PNG, JPEG, JPG):</label>
            <input type="file" name="stempel" accept=".png,.jpg,.jpeg" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="pt-2 space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
        <button type="reset" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Batal</button>
    </div>
</div>
<?php
  } elseif ($role == 'driver') {
// Proses simpan data jika form driver disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role']) && $_POST['role'] === 'Driver') {
    $nama_driver   = $_POST['nama_driver'];
    $nama_lengkap  = $_POST['nama_lengkap'];
    $alamat        = $_POST['alamat'];
    $tempat_lahir  = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    $sql = "INSERT INTO driver (nama_driver, nama_lengkap, alamat, tempat_lahir, tanggal_lahir)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $nama_driver, $nama_lengkap, $alamat, $tempat_lahir, $tanggal_lahir);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo "<script>alert('Data driver berhasil disimpan!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data driver!');</script>";
    }
}
// ...existing code...
    ?>

<!-- Form untuk Driver -->
<!-- Form untuk Driver -->
<div class="space-y-4">
    <div>
        <label class="block font-semisemibold">Nama Driver</label>
        <input type="text" name="nama_driver" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block font-semisemibold">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block font-semisemibold">Alamat</label>
        <textarea name="alamat" class="w-full border rounded px-3 py-2" required></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semisemibold">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semisemibold">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>
    <div>
        <select name="role" class="w-full border border-blue-200 p-2 rounded bg-blue-50 text-gray-700" readonly
            disabled>
            <option value="Driver" selected>Driver</option>
        </select>
        <input type="hidden" name="role" value="Driver">
    </div>
    <div class="pt-2 space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
        <button type="reset" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Batal</button>
    </div>
</div>
<?php
  }
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Dinamis Tailwind</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function loadForm(role) {
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: {
                load_form: role
            },
            success: function(data) {
                $('#dynamic-form').html(data);
            },
            error: function() {
                alert('Gagal memuat form.');
            }
        });
    }
    $(document).ready(function() {
        // Saat pertama kali halaman dibuka, tampilkan form pegawai
        loadForm('pegawai');
        $('#id_role').on('change', function() {
            let role = $(this).val();
            if (role) {
                loadForm(role);
            } else {
                $('#dynamic-form').html('');
            }
        });
    });
    </script>
    <script>
    $(document).on('submit', '#dynamic-form', function(e) {
        // Cek apakah ada field confirm_password (form pegawai)
        var password = $(this).find('input[name="password"]').val();
        var confirm = $(this).find('input[name="confirm_password"]').val();
        if (typeof confirm !== 'undefined' && password !== confirm) {
            alert('Password dan Confirm Password tidak sama!');
            e.preventDefault();
            return false;
        }
    });
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
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-4 mt-4 flex-1 overflow-auto bg-white">
                <div class="bg-white shadow-lg rounded-lg p-8 w-full" style="min-width:300px;">
                    <h2 class="text-2xl font-semibold mb-6 text-center">FORM PENDAFTARAN</h2>
                    <div class="mb-6 max-w-4xl mx-auto">
                        <label for="id_role" class="block font-semisemibold mb-2">Pilih Role</label>
                        <select id="id_role" name="id_role" class="w-full border rounded px-3 py-2">
                            <option value="pegawai" selected>Pegawai</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <form id="dynamic-form" method="post" enctype="multipart/form-data"
                        class="space-y-4 max-w-4xl mx-auto">
                        <!-- Form akan dimuat di sini -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>