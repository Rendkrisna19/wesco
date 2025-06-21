<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /auth/index.php"); // Gunakan path absolut jika pakai .htaccess
    exit;
}

// Sertakan koneksi database
include '../config/koneksi.php';

// Ambil data sesi untuk ditampilkan di halaman
$id_user_session = $_SESSION['id_user'];
$username_session = $_SESSION['username'];
$nama_lengkap_session = $_SESSION['nama_lengkap'] ?? $username_session;


// ==============================================================================
// --- BAGIAN UTAMA: PEMROSESAN SUBMIT FORM DENGAN AJAX (METHOD POST) ---
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak terduga.'];

    // --- PROSES PENDAFTARAN PEGAWAI ---
    if ($_POST['role'] === 'Pegawai') {
        // Ambil semua data dari form pegawai
        $nama_lengkap    = $_POST['nama_lengkap'];
        $alamat          = $_POST['alamat'];
        $tempat_lahir    = $_POST['tempat_lahir'];
        $tanggal_lahir   = $_POST['tanggal_lahir'];
        $username        = $_POST['username'];
        $password        = $_POST['password'];
        $confirm         = $_POST['confirm_password'];
        $id_role         = 2; // Role ID untuk Pegawai

        if ($password !== $confirm) {
            $response = ['status' => 'warning', 'message' => 'Password dan Confirm Password tidak sama!'];
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Proses upload file TTD dan Stempel
            $gambar_ttd = '';
            $gambar_stempel = '';
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            // Upload TTD
            if (isset($_FILES['ttd']) && $_FILES['ttd']['error'] == 0) {
                $ext_ttd = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
                $gambar_ttd = 'ttd_' . time() . '.' . $ext_ttd;
                if (!move_uploaded_file($_FILES['ttd']['tmp_name'], $upload_dir . $gambar_ttd)) {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file TTD.'];
                    echo json_encode($response); exit;
                }
            }
            // Upload Stempel
            if (isset($_FILES['stempel']) && $_FILES['stempel']['error'] == 0) {
                $ext_stempel = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
                $gambar_stempel = 'stempel_' . time() . '.' . $ext_stempel;
                if (!move_uploaded_file($_FILES['stempel']['tmp_name'], $upload_dir . $gambar_stempel)) {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file Stempel.'];
                    echo json_encode($response); exit;
                }
            }

            // PERBAIKAN: Semua data dimasukkan ke tabel 'user'
            $sql = "INSERT INTO user (nama_lengkap, alamat, tempat_lahir, tanggal_lahir, username, password, id_role, gambar_ttd, gambar_stempel)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            // Sesuaikan tipe data bind_param: s (string), i (integer)
            mysqli_stmt_bind_param($stmt, "ssssssiss", $nama_lengkap, $alamat, $tempat_lahir, $tanggal_lahir, $username, $password_hash, $id_role, $gambar_ttd, $gambar_stempel);
            
            if (mysqli_stmt_execute($stmt)) {
                $response = ['status' => 'success', 'message' => 'Data Pegawai berhasil disimpan!', 'redirect' => 'dashboard.php'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal menyimpan data pegawai: ' . mysqli_stmt_error($stmt)];
            }
            mysqli_stmt_close($stmt);
        }
    } 
    // --- PROSES PENDAFTARAN DRIVER ---
    elseif ($_POST['role'] === 'Driver') {
        // Ambil data dari form driver
        $nama_driver      = $_POST['nama_driver'];
        $no_ktp           = $_POST['no_ktp'];
        $nama_lengkap     = $_POST['nama_lengkap'];
        $alamat           = $_POST['alamat'];
        $tempat_lahir     = $_POST['tempat_lahir'];
        $tanggal_lahir    = $_POST['tanggal_lahir'];

        // PERBAIKAN: Hanya INSERT ke tabel 'driver' dan tanpa 'id_bridger'
        $sql_driver = "INSERT INTO driver (nama_driver, no_ktp, nama_lengkap, alamat, tempat_lahir, tanggal_lahir)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_driver = mysqli_prepare($conn, $sql_driver);
        mysqli_stmt_bind_param($stmt_driver, "ssssss", $nama_driver, $no_ktp, $nama_lengkap, $alamat, $tempat_lahir, $tanggal_lahir);
        
        if (mysqli_stmt_execute($stmt_driver)) {
            $response = ['status' => 'success', 'message' => 'Data Driver berhasil disimpan!', 'redirect' => 'dashboard.php'];
        } else {
            $response = ['status' => 'error', 'message' => 'Gagal menyimpan data driver: ' . mysqli_stmt_error($stmt_driver)];
        }
        mysqli_stmt_close($stmt_driver);
    }
    
    echo json_encode($response);
    exit;
}
// ==============================================================================
// --- AKHIR BAGIAN PEMROSESAN FORM ---
// ==============================================================================


// --- Bagian untuk memuat HTML form secara dinamis via AJAX (METHOD GET) ---
if (isset($_GET['load_form'])) {
    $role = $_GET['load_form'];

    if ($role == 'pegawai') {
        // Form untuk Pegawai (Sesuai desain asli Anda)
?>
<div class="space-y-4">
    <input type="hidden" name="role" value="Pegawai">
    <div><label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label><input type="text"
            name="nama_lengkap" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block font-semibold text-gray-700 mb-1">Alamat</label><textarea name="alamat"
            class="w-full border rounded px-3 py-2" required></textarea></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="block font-semibold text-gray-700 mb-1">Tempat Lahir</label><input type="text"
                name="tempat_lahir" class="w-full border rounded px-3 py-2" required></div>
        <div><label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir</label><input type="date"
                name="tanggal_lahir" class="w-full border rounded px-3 py-2" required></div>
    </div>
    <div><label class="block font-semibold text-gray-700 mb-1">Username</label><input type="text" name="username"
            class="w-full border rounded px-3 py-2" required></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="block font-semibold text-gray-700 mb-1">Password</label><input type="password"
                name="password" class="w-full border rounded px-3 py-2" required></div>
        <div><label class="block font-semibold text-gray-700 mb-1">Confirm Password</label><input type="password"
                name="confirm_password" class="w-full border rounded px-3 py-2" required></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="block font-semibold text-gray-700 mb-1">Pilih File Gambar TTD (PNG, JPEG, JPG):</label><input
                type="file" name="ttd" accept=".png,.jpg,.jpeg" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block font-semibold text-gray-700 mb-1">Pilih File Gambar Stempel (PNG, JPEG,
                JPG):</label><input type="file" name="stempel" accept=".png,.jpg,.jpeg"
                class="w-full border rounded px-3 py-2"></div>
    </div>
    <div class="flex justify-end gap-2 pt-4">
        <a href="dashboard.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    </div>
</div>
<?php
    } elseif ($role == 'driver') {
        // Form untuk Driver (PERBAIKAN: Tanpa id_bridger dan tanpa username/password)
?>
<div class="space-y-4">
    <input type="hidden" name="role" value="Driver">
    <div><label class="block font-semibold text-gray-700 mb-1">Nama Driver <span
                class="text-red-500">*</span></label><input type="text" name="nama_driver"
            class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block font-semibold text-gray-700 mb-1">No. KTP <span class="text-red-500">*</span></label><input
            type="text" name="no_ktp" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block font-semibold text-gray-700 mb-1">Nama Lengkap <span
                class="text-red-500">*</span></label><input type="text" name="nama_lengkap"
            class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block font-semibold text-gray-700 mb-1">Alamat <span
                class="text-red-500">*</span></label><textarea name="alamat" class="w-full border rounded px-3 py-2"
            required></textarea></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="block font-semibold text-gray-700 mb-1">Tempat Lahir <span
                    class="text-red-500">*</span></label><input type="text" name="tempat_lahir"
                class="w-full border rounded px-3 py-2" required></div>
        <div><label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir <span
                    class="text-red-500">*</span></label><input type="date" name="tanggal_lahir"
                class="w-full border rounded px-3 py-2" required></div>
    </div>
    <div class="flex justify-end gap-2 pt-4">
        <a href="dashboard.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    </div>
</div>
<?php
    }
    exit(); // Penting
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pendaftaran Pengguna</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
    .font-modify {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <div class="flex-1 flex flex-col">
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap_session) ?>!</h1>
                <div class="relative group">
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap_session) ?></span>
                        <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                            alt="User" class="w-8 h-8 rounded-full">
                    </div>
                    <div
                        class="absolute hidden group-hover:block right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-10 ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="/auth/logout.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-cyan-700 hover:text-white">Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 mt-4 flex-1 overflow-auto bg-gray-50">
                <div class="bg-white shadow-lg rounded-lg p-8 w-full mx-auto max-w-4xl">
                    <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">FORM PENDAFTARAN PENGGUNA</h2>

                    <div class="mb-6">
                        <label for="role_selector" class="block font-semibold text-gray-700 mb-2">Pilih Role
                            Pengguna</label>
                        <select id="role_selector"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pegawai" selected>Pegawai</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>

                    <form id="main-form" method="post" enctype="multipart/form-data" class="space-y-4">
                        <div id="dynamic-form-container">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Fungsi untuk memuat form dinamis
        function loadForm(role) {
            $.ajax({
                url: '', // Request ke halaman ini sendiri
                type: 'GET',
                data: {
                    load_form: role
                },
                success: function(data) {
                    $('#dynamic-form-container').html(data);
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat form.', 'error');
                }
            });
        }

        // Muat form default (Pegawai)
        loadForm('pegawai');

        // Ganti form saat dropdown berubah
        $('#role_selector').on('change', function() {
            loadForm($(this).val());
        });

        // Handler untuk submit form (event delegation)
        $(document).on('submit', '#main-form', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(form[0]);
            var role = formData.get('role');

            // Validasi password hanya jika mendaftarkan pegawai
            if (role === 'Pegawai') {
                var password = formData.get('password');
                var confirmPassword = formData.get('confirm_password');
                if (password !== confirmPassword) {
                    Swal.fire('Peringatan', 'Password dan Confirm Password tidak sama!', 'warning');
                    return; // Hentikan eksekusi
                }
            }

            // Kirim data via AJAX
            $.ajax({
                url: '', // Submit ke halaman ini sendiri
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        icon: response.status,
                        title: response.status.charAt(0).toUpperCase() + response
                            .status.slice(1),
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed && response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan AJAX: ' + xhr.responseText,
                        'error');
                }
            });
        });
    });
    </script>
</body>

</html>