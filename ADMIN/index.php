<?php
session_start(); 

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
include '../config/koneksi.php';

// --- PHP Processing for Form Submissions via AJAX ---
// This part will run only if it's a POST request and 'role' is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    header('Content-Type: application/json'); // Set header for JSON response
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak terduga.']; // Default error response

    // Handle Pegawai Form Submission
    if ($_POST['role'] === 'Pegawai') {
        $nama_lengkap_pegawai  = $_POST['nama_lengkap'];
        $alamat_pegawai        = $_POST['alamat'];
        $tempat_lahir_pegawai  = $_POST['tempat_lahir'];
        $tanggal_lahir_pegawai = $_POST['tanggal_lahir'];
        $username_pegawai      = $_POST['username'];
        $password              = $_POST['password'];
        $confirm               = $_POST['confirm_password'];
        $id_role               = 2; // Assuming 2 for Pegawai

        if ($password !== $confirm) {
            $response = ['status' => 'warning', 'message' => 'Password dan Confirm Password tidak sama!'];
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // TTD Upload
            $gambar_ttd = '';
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            if (isset($_FILES['ttd']) && $_FILES['ttd']['error'] == 0) {
                $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
                $gambar_ttd_name = 'ttd_' . time() . '.' . $ext;
                $upload_path_ttd = $upload_dir . $gambar_ttd_name;
                if (move_uploaded_file($_FILES['ttd']['tmp_name'], $upload_path_ttd)) {
                    $gambar_ttd = $gambar_ttd_name;
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file TTD.'];
                }
            }

            // Stempel Upload
            $gambar_stempel = '';
            if (isset($_FILES['stempel']) && $_FILES['stempel']['error'] == 0) {
                $ext2 = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
                $gambar_stempel_name = 'stempel_' . time() . '.' . $ext2;
                $upload_path_stempel = $upload_dir . $gambar_stempel_name;
                if (move_uploaded_file($_FILES['stempel']['tmp_name'], $upload_path_stempel)) {
                    $gambar_stempel = $gambar_stempel_name;
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file Stempel.'];
                }
            }

            if ($response['status'] !== 'error' && $response['status'] !== 'warning') { // Only proceed if no file upload errors
                $sql = "INSERT INTO user (nama_lengkap, alamat, tempat_lahir, tanggal_lahir, username, password, id_role, gambar_ttd, gambar_stempel)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssssssiss", $nama_lengkap_pegawai, $alamat_pegawai, $tempat_lahir_pegawai, $tanggal_lahir_pegawai, $username_pegawai, $password_hash, $id_role, $gambar_ttd, $gambar_stempel);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response = ['status' => 'success', 'message' => 'Data pegawai berhasil disimpan!', 'redirect' => 'dashboard.php'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal menyimpan data pegawai: ' . mysqli_error($conn)];
                }
                mysqli_stmt_close($stmt);
            }
        }
    } 
    // Handle Driver Form Submission
    elseif ($_POST['role'] === 'Driver') {
        $id_bridger_driver    = $_POST['id_bridger'];
        $nama_driver          = $_POST['nama_driver'];
        $no_ktp_driver        = $_POST['no_ktp'];
        $nama_lengkap_driver  = $_POST['nama_lengkap'];
        $alamat_driver        = $_POST['alamat'];
        $tempat_lahir_driver  = $_POST['tempat_lahir'];
        $tanggal_lahir_driver = $_POST['tanggal_lahir'];

        // Optional: Check if `nama_driver` already exists
        $check_driver_sql = "SELECT COUNT(*) FROM driver WHERE nama_driver = ?";
        $check_stmt = mysqli_prepare($conn, $check_driver_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $nama_driver);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_bind_result($check_stmt, $count);
        mysqli_stmt_fetch($check_stmt);
        mysqli_stmt_close($check_stmt);

        if ($count > 0) {
            $response = ['status' => 'warning', 'message' => 'Nama Driver sudah ada. Gunakan nama driver lain.'];
        } else {
            $sql_driver = "INSERT INTO driver (id_bridger, nama_driver, no_ktp, nama_lengkap, alamat, tempat_lahir, tanggal_lahir)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_driver = mysqli_prepare($conn, $sql_driver);
            mysqli_stmt_bind_param($stmt_driver, "issssss", $id_bridger_driver, $nama_driver, $no_ktp_driver, $nama_lengkap_driver, $alamat_driver, $tempat_lahir_driver, $tanggal_lahir_driver);
            
            if (mysqli_stmt_execute($stmt_driver)) {
                $response = ['status' => 'success', 'message' => 'Data driver berhasil disimpan!', 'redirect' => 'dashboard.php'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal menyimpan data driver: ' . mysqli_error($conn)];
            }
            mysqli_stmt_close($stmt_driver);
        }
    }
    echo json_encode($response); // Encode the response array to JSON and send it
    exit; // Stop further script execution
}

// Fetch bridger data for the dropdown in driver form (always needed for AJAX part loading forms)
$bridger_data = [];
$bridger_query = $conn->query("SELECT id_bridger, no_polisi FROM bridger");
if ($bridger_query) {
    while ($row = $bridger_query->fetch_assoc()) {
        $bridger_data[] = $row;
    }
}
// --- END PHP Processing for Form Submissions ---


// --- AJAX Request Handling for Dynamic Forms (Always `GET` requests) ---
// This part only runs when an AJAX GET request is made to load the form HTML
if (isset($_GET['load_form'])) {
    $role = $_GET['load_form'];

    if ($role == 'pegawai') {
        ?>
<div class="space-y-4">
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama_lengkap"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Alamat</label>
        <textarea name="alamat"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Role</label>
        <select name="role_display"
            class="w-full border border-blue-200 p-2 rounded bg-blue-50 text-gray-700 cursor-not-allowed" disabled>
            <option value="Pegawai" selected>Pegawai</option>
        </select>
        <input type="hidden" name="role" value="Pegawai">
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Username</label>
        <input type="text" name="username"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="confirm_password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Pilih File Gambar TTD (PNG, JPEG, JPG):</label>
            <input type="file" name="ttd" accept=".png,.jpg,.jpeg"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Pilih File Gambar Stempel (PNG, JPEG, JPG):</label>
            <input type="file" name="stempel" accept=".png,.jpg,.jpeg"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <div class="pt-4 flex justify-end gap-2">
        <button type="reset"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200">Batal</button>
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Submit</button>
    </div>
</div>
<?php
    } elseif ($role == 'driver') {
        ?>
<div class="space-y-4">
    <div>
        <label class="block font-semibold text-gray-700 mb-1">ID Bridger <span class="text-red-500">*</span></label>
        <select name="id_bridger"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="">Pilih Bridger</option>
            <?php foreach ($bridger_data as $bridger): ?>
            <option value="<?= htmlspecialchars($bridger['id_bridger']) ?>">
                <?= htmlspecialchars($bridger['no_polisi']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Driver <span class="text-red-500">*</span></label>
        <input type="text" name="nama_driver"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">No. KTP <span class="text-red-500">*</span></label>
        <input type="text" name="no_ktp"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama_lengkap"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
        <textarea name="alamat"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tempat Lahir <span
                    class="text-red-500">*</span></label>
            <input type="text" name="tempat_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir <span
                    class="text-red-500">*</span></label>
            <input type="date" name="tanggal_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Role</label>
        <select name="role_display"
            class="w-full border border-blue-200 p-2 rounded bg-blue-50 text-gray-700 cursor-not-allowed" disabled>
            <option value="Driver" selected>Driver</option>
        </select>
        <input type="hidden" name="role" value="Driver">
    </div>
    <div class="pt-4 flex justify-end gap-2">
        <button type="reset"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200">Batal</button>
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Submit</button>
    </div>
</div>
<?php
    }
    exit(); // Important to exit after AJAX content is served
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
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <div class="p-4 mt-4 flex-1 overflow-auto bg-gray-50">
                <div class="bg-white shadow-lg rounded-lg p-8 w-full mx-auto max-w-4xl">
                    <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">FORM PENDAFTARAN PENGGUNA</h2>

                    <div class="mb-6">
                        <label for="id_role" class="block font-semibold text-gray-700 mb-2">Pilih Role Pengguna</label>
                        <select id="id_role" name="id_role"
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
        // Initial form load on page ready (load Pegawai form by default)
        loadForm('pegawai');

        // Handle role selection change
        $('#id_role').on('change', function() {
            let role = $(this).val();
            loadForm(role);
        });

        // Handle form submission via AJAX for the main form
        // We use $(document).on for event delegation because the content within #dynamic-form-container is dynamic
        $(document).on('submit', '#main-form', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this);
            var formData = new FormData(form[0]);

            // Special handling for password confirmation only for Pegawai form
            var role = formData.get('role');
            if (role === 'Pegawai') {
                var password = formData.get('password');
                var confirmPassword = formData.get('confirm_password');
                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Password dan Confirm Password tidak sama.',
                        confirmButtonText: 'OK'
                    });
                    return false; // Stop form submission
                }
            }

            // Submit form data using AJAX
            $.ajax({
                url: 'index.php', // Submit to the same PHP file
                type: 'POST',
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                dataType: 'json', // Expect JSON response from PHP
                success: function(response) {
                    // Handle response from PHP (JSON)
                    Swal.fire({
                        icon: response.status, // 'success', 'error', 'warning'
                        title: response.status === 'success' ? 'Berhasil!' :
                            'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed && response.redirect) {
                            window.location.href = response
                            .redirect; // Redirect if 'redirect' URL is provided
                        } else if (result.isConfirmed && response.status ===
                            'success') {
                            // If it's a success but no specific redirect, maybe reload the form or clear it
                            loadForm(role); // Reload the current form type
                            // Or to clear all inputs: form[0].reset();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi. ' +
                            xhr.responseText, // Show full error for debugging
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    // Function to load dynamic forms via AJAX (GET request)
    function loadForm(role) {
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: {
                load_form: role
            },
            success: function(data) {
                $('#dynamic-form-container').html(data);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal memuat form. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
    </script>
</body>

</html>