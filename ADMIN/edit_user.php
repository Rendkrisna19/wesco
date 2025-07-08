<?php
session_start(); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

$id_user_session = $_SESSION['id_user'];
$username_session = $_SESSION['username'];
$nama_lengkap_session = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username_session;
include '../config/koneksi.php';

$response = []; // Untuk menyimpan pesan SweetAlert

// Variabel untuk menyimpan ID dan Role yang diperoleh dari permintaan GET awal (URL)
$current_id_to_edit = 0;
$current_role_to_edit = '';

// --- Permintaan GET Awal (saat halaman pertama kali dimuat melalui tautan dari index.php) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['role'])) {
    $current_id_to_edit = (int)$_GET['id'];
    $current_role_to_edit = strtolower($_GET['role']); // Pastikan huruf kecil untuk konsistensi

    // Validasi dasar: Periksa apakah ID valid dan role diketahui
    if ($current_id_to_edit <= 0 || !in_array($current_role_to_edit, ['pegawai', 'driver'])) {
        header("Location: index.php?error=Parameter ID atau role tidak valid.");
        exit;
    }

    // Verifikasi apakah record benar-benar ada sebelum melanjutkan
    $check_sql = '';
    if ($current_role_to_edit === 'pegawai') {
        $check_sql = "SELECT COUNT(*) FROM user WHERE id_user = ?";
    } elseif ($current_role_to_edit === 'driver') {
        $check_sql = "SELECT COUNT(*) FROM driver WHERE id_driver = ?";
    }

    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "i", $current_id_to_edit);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($count === 0) {
        header("Location: index.php?error=Data pengguna tidak ditemukan.");
        exit;
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['load_form'])) {
    // Jika itu adalah permintaan GET tanpa ID/role (misalnya, akses langsung ke edit_user.php), redirect ke daftar
    header("Location: index.php");
    exit;
}

// Ambil data bridger untuk dropdown di form driver (selalu dibutuhkan untuk bagian AJAX yang memuat form)
$bridger_data = [];
$bridger_query = $conn->query("SELECT id_bridger, no_polisi FROM bridger");
if ($bridger_query) {
    while ($row = $bridger_query->fetch_assoc()) {
        $bridger_data[] = $row;
    }
}


// --- Penanganan Permintaan AJAX (menanggapi dengan konten HTML untuk bagian form dinamis) ---
// Bagian ini berjalan ketika JavaScript membuat permintaan GET untuk memuat HTML form, diisi dengan data.
if (isset($_GET['load_form'])) {
    header('Content-Type: text/html'); // Pastikan tipe konten HTML untuk respons AJAX
    $role_to_load = strtolower($_GET['load_form']);
    $id_to_load = isset($_GET['id_to_load']) ? (int)$_GET['id_to_load'] : 0; 

    $current_data = null;
    if ($id_to_load > 0) {
        if ($role_to_load === 'pegawai') {
            $sql_load = "SELECT nama_lengkap, alamat, tempat_lahir, tanggal_lahir, username, gambar_ttd, gambar_stempel FROM user WHERE id_user = ?";
            $stmt_load = mysqli_prepare($conn, $sql_load);
            mysqli_stmt_bind_param($stmt_load, "i", $id_to_load);
            mysqli_stmt_execute($stmt_load);
            $result_load = mysqli_stmt_get_result($stmt_load);
            $current_data = mysqli_fetch_assoc($result_load);
            mysqli_stmt_close($stmt_load);
        } elseif ($role_to_load === 'driver') {
            $sql_load = "SELECT id_bridger, nama_driver, no_ktp, nama_lengkap, alamat, tempat_lahir, tanggal_lahir FROM driver WHERE id_driver = ?";
            $stmt_load = mysqli_prepare($conn, $sql_load);
            mysqli_stmt_bind_param($stmt_load, "i", $id_to_load);
            mysqli_stmt_execute($stmt_load);
            $result_load = mysqli_stmt_get_result($stmt_load);
            $current_data = mysqli_fetch_assoc($result_load);
            mysqli_stmt_close($stmt_load);
        }
    }

    if ($role_to_load == 'pegawai') {
        ?>
<div class="space-y-4">
    <input type="hidden" name="id_to_update" value="<?= htmlspecialchars($id_to_load) ?>">

    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama_lengkap"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            value="<?= htmlspecialchars($current_data['nama_lengkap'] ?? '') ?>" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Alamat</label>
        <textarea name="alamat"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required><?= htmlspecialchars($current_data['alamat'] ?? '') ?></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($current_data['tempat_lahir'] ?? '') ?>" required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($current_data['tanggal_lahir'] ?? '') ?>" required>
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
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            value="<?= htmlspecialchars($current_data['username'] ?? '') ?>" required>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Password (Kosongkan jika tidak ingin diubah)</label>
            <input type="password" name="password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="confirm_password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Current TTD:</label>
            <?php if (!empty($current_data['gambar_ttd'])): ?>
            <img src="../uploads/<?= htmlspecialchars($current_data['gambar_ttd']) ?>" alt="Current TTD"
                class="max-w-xs h-20 object-contain mb-2 border rounded">
            <input type="hidden" name="existing_ttd" value="<?= htmlspecialchars($current_data['gambar_ttd']) ?>">
            <?php else: ?>
            <p class="text-gray-500 text-sm mb-2">Belum ada TTD.</p>
            <?php endif; ?>
            <label class="block font-semibold text-gray-700 mb-1">Upload New TTD (PNG, JPEG, JPG):</label>
            <input type="file" name="ttd" accept=".png,.jpg,.jpeg"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Current Stempel:</label>
            <?php if (!empty($current_data['gambar_stempel'])): ?>
            <img src="../uploads/<?= htmlspecialchars($current_data['gambar_stempel']) ?>" alt="Current Stempel"
                class="max-w-xs h-20 object-contain mb-2 border rounded">
            <input type="hidden" name="existing_stempel"
                value="<?= htmlspecialchars($current_data['gambar_stempel']) ?>">
            <?php else: ?>
            <p class="text-gray-500 text-sm mb-2">Belum ada Stempel.</p>
            <?php endif; ?>
            <label class="block font-semibold text-gray-700 mb-1">Upload New Stempel (PNG, JPEG, JPG):</label>
            <input type="file" name="stempel" accept=".png,.jpg,.jpeg"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <div class="pt-4 flex justify-end gap-2">
        <a href="dashboard.php"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200">Batal</a>
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Update
            Pegawai</button>
    </div>
</div>
<?php
    } elseif ($role_to_load == 'driver') {
        ?>
<div class="space-y-4">
    <input type="hidden" name="id_to_update" value="<?= htmlspecialchars($id_to_load) ?>">
    <div>
        <label class="block font-semibold text-gray-700 mb-1">ID Bridger <span class="text-red-500">*</span></label>
        <select name="id_bridger"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="">Pilih Bridger</option>
            <?php foreach ($bridger_data as $bridger): ?>
            <option value="<?= htmlspecialchars($bridger['id_bridger']) ?>"
                <?= (isset($current_data['id_bridger']) && $current_data['id_bridger'] == $bridger['id_bridger']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($bridger['no_polisi']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Driver <span class="text-red-500">*</span></label>
        <input type="text" name="nama_driver"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            value="<?= htmlspecialchars($current_data['nama_driver'] ?? '') ?>" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">No. KTP <span class="text-red-500">*</span></label>
        <input type="text" name="no_ktp"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            value="<?= htmlspecialchars($current_data['no_ktp'] ?? '') ?>" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama_lengkap"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            value="<?= htmlspecialchars($current_data['nama_lengkap'] ?? '') ?>" required>
    </div>
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
        <textarea name="alamat"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required><?= htmlspecialchars($current_data['alamat'] ?? '') ?></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tempat Lahir <span
                    class="text-red-500">*</span></label>
            <input type="text" name="tempat_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($current_data['tempat_lahir'] ?? '') ?>" required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Tanggal Lahir <span
                    class="text-red-500">*</span></label>
            <input type="date" name="tanggal_lahir"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($current_data['tanggal_lahir'] ?? '') ?>" required>
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
        <a href="index.php"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200">Batal</a>
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Update
            Driver</button>
    </div>
</div>
<?php
    }
    exit(); // Penting: Hentikan eksekusi skrip setelah mengirim konten AJAX
}

// --- Permintaan POST: Menangani Pengiriman Form untuk Update ---
// Bagian ini berjalan ketika form utama disubmit melalui AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role']) && isset($_POST['id_to_update'])) {
    header('Content-Type: application/json'); // Respon dengan JSON
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak terduga.']; // Respon default

    $id_to_update = (int)$_POST['id_to_update'];
    $role = strtolower($_POST['role']);

    // Tangani Update Pegawai
    if ($role === 'pegawai') {
        $nama_lengkap_pegawai = $_POST['nama_lengkap'];
        $alamat_pegawai       = $_POST['alamat'];
        $tempat_lahir_pegawai = $_POST['tempat_lahir'];
        $tanggal_lahir_pegawai = $_POST['tanggal_lahir'];
        $username_pegawai     = $_POST['username'];
        $password             = $_POST['password']; // Ini mungkin kosong jika tidak diubah
        $confirm              = $_POST['confirm_password']; // Ini mungkin kosong jika tidak diubah
        $existing_ttd         = $_POST['existing_ttd'] ?? ''; // Nama file saat ini jika tidak mengunggah yang baru
        $existing_stempel     = $_POST['existing_stempel'] ?? ''; // Nama file saat ini jika tidak mengunggah yang baru

        // Validasi password sisi server
        if ((!empty($password) && empty($confirm)) || (empty($password) && !empty($confirm))) {
            $response = ['status' => 'warning', 'message' => 'Kedua kolom Password dan Confirm Password harus diisi atau dikosongkan bersamaan.'];
            echo json_encode($response);
            exit; // Hentikan eksekusi jika validasi gagal
        }
        if (!empty($password) && $password !== $confirm) {
            $response = ['status' => 'warning', 'message' => 'Password dan Confirm Password tidak sama!'];
            echo json_encode($response);
            exit; // Hentikan eksekusi jika password tidak cocok
        }

        $password_update_clause = "";
        $password_hash = null; // Hanya akan diatur jika password disediakan
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_update_clause = ", password = ?";
        }

        $gambar_ttd = $existing_ttd; // Mulai dengan nilai yang ada
        $gambar_stempel = $existing_stempel; // Mulai dengan nilai yang ada
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $response = ['status' => 'error', 'message' => 'Gagal membuat direktori unggahan.'];
                echo json_encode($response);
                exit;
            }
        }

        // Proses Unggah TTD
        if (isset($_FILES['ttd']) && $_FILES['ttd']['error'] == UPLOAD_ERR_OK && $_FILES['ttd']['size'] > 0) {
            $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
            $new_ttd_name = 'ttd_' . time() . '_' . uniqid() . '.' . $ext; // Tambahkan uniqid untuk keunikan yang lebih baik
            if (move_uploaded_file($_FILES['ttd']['tmp_name'], $upload_dir . $new_ttd_name)) {
                // Hapus file lama jika yang baru berhasil diunggah
                if (!empty($existing_ttd) && file_exists($upload_dir . $existing_ttd)) {
                    unlink($upload_dir . $existing_ttd);
                }
                $gambar_ttd = $new_ttd_name;
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal mengunggah file TTD.'];
                echo json_encode($response);
                exit; // Hentikan jika upload file gagal
            }
        }

        // Proses Unggah Stempel
        if (isset($_FILES['stempel']) && $_FILES['stempel']['error'] == UPLOAD_ERR_OK && $_FILES['stempel']['size'] > 0) {
            $ext2 = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
            $new_stempel_name = 'stempel_' . time() . '_' . uniqid() . '.' . $ext2; // Tambahkan uniqid untuk keunikan yang lebih baik
            if (move_uploaded_file($_FILES['stempel']['tmp_name'], $upload_dir . $new_stempel_name)) {
                // Hapus file lama jika yang baru berhasil diunggah
                if (!empty($existing_stempel) && file_exists($upload_dir . $existing_stempel)) {
                    unlink($upload_dir . $existing_stempel);
                }
                $gambar_stempel = $new_stempel_name;
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal mengunggah file Stempel.'];
                echo json_encode($response);
                exit; // Hentikan jika upload file gagal
            }
        }

        // Siapkan statement SQL untuk update user
        $sql = "UPDATE user SET nama_lengkap = ?, alamat = ?, tempat_lahir = ?, tanggal_lahir = ?, username = ?, gambar_ttd = ?, gambar_stempel = ? $password_update_clause WHERE id_user = ?";
        
        $types = "sssssss"; // Untuk nama, alamat, tempat_lahir, tanggal_lahir, username, ttd, stempel
        $params_values = [
            $nama_lengkap_pegawai,
            $alamat_pegawai,
            $tempat_lahir_pegawai,
            $tanggal_lahir_pegawai,
            $username_pegawai,
            $gambar_ttd,
            $gambar_stempel
        ];

        if (!empty($password_hash)) {
            $types .= "s"; // Tambahkan tipe string untuk password
            $params_values[] = $password_hash; // Tambahkan hash password ke parameter
        }
        $types .= "i"; // Tambahkan tipe integer untuk id_user
        $params_values[] = $id_to_update; // Tambahkan id_user ke parameter

        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Menggunakan call_user_func_array untuk mysqli_stmt_bind_param agar parameter dinamis dapat diikat
            // secara benar (karena mereka harus berupa referensi).
            // Buat array argumen untuk call_user_func_array: elemen pertama adalah $stmt, elemen kedua adalah $types,
            // lalu sisa elemen adalah nilai-nilai parameter yang akan diikat.
            $bind_params = array_merge([$stmt, $types], $params_values);
            // Setiap nilai dalam $params_values harus diubah menjadi referensi.
            // Loop melalui $bind_params dari indeks 2 (setelah $stmt dan $types)
            for ($i = 2; $i < count($bind_params); $i++) {
                $bind_params[$i] = &$bind_params[$i];
            }

            if (call_user_func_array('mysqli_stmt_bind_param', $bind_params)) {
                if (mysqli_stmt_execute($stmt)) {
                    $response = ['status' => 'success', 'message' => 'Data pegawai berhasil diupdate!', 'redirect' => 'dashboard.php'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengupdate data pegawai: ' . mysqli_error($conn)];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal mengikat parameter untuk pegawai: ' . mysqli_error($conn)];
            }
            mysqli_stmt_close($stmt);
        } else {
             $response = ['status' => 'error', 'message' => 'Gagal mempersiapkan statement SQL untuk pegawai: ' . mysqli_error($conn)];
        }
    } 
    // Tangani Update Driver
    elseif ($role === 'driver') {
        $id_bridger_driver    = $_POST['id_bridger'];
        $nama_driver          = $_POST['nama_driver'];
        $no_ktp_driver        = $_POST['no_ktp'];
        $nama_lengkap_driver  = $_POST['nama_lengkap'];
        $alamat_driver        = $_POST['alamat'];
        $tempat_lahir_driver  = $_POST['tempat_lahir'];
        $tanggal_lahir_driver = $_POST['tanggal_lahir'];

        // Periksa apakah `nama_driver` sudah ada untuk driver lain (kecuali yang sedang diupdate)
        $check_driver_sql = "SELECT COUNT(*) FROM driver WHERE nama_driver = ? AND id_driver != ?";
        $check_stmt = mysqli_prepare($conn, $check_driver_sql);
        
        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "si", $nama_driver, $id_to_update);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if ($count > 0) {
                $response = ['status' => 'warning', 'message' => 'Nama Driver sudah ada untuk driver lain. Gunakan nama driver yang berbeda.'];
            } else {
                $sql_driver = "UPDATE driver SET id_bridger = ?, nama_driver = ?, no_ktp = ?, nama_lengkap = ?, alamat = ?, tempat_lahir = ?, tanggal_lahir = ? WHERE id_driver = ?";
                $stmt_driver = mysqli_prepare($conn, $sql_driver);
                
                if ($stmt_driver) {
                    // 'issssssi' -> int (id_bridger), string (nama_driver), string (no_ktp), string (nama_lengkap), string (alamat), string (tempat_lahir), string (tanggal_lahir), int (id_driver)
                    mysqli_stmt_bind_param($stmt_driver, "issssssi", 
                        $id_bridger_driver, 
                        $nama_driver, 
                        $no_ktp_driver, 
                        $nama_lengkap_driver, 
                        $alamat_driver, 
                        $tempat_lahir_driver, 
                        $tanggal_lahir_driver, 
                        $id_to_update
                    );
                    
                    if (mysqli_stmt_execute($stmt_driver)) {
                        $response = ['status' => 'success', 'message' => 'Data driver berhasil diupdate!', 'redirect' => 'index.php'];
                    } else {
                        $response = ['status' => 'error', 'message' => 'Gagal mengupdate data driver: ' . mysqli_error($conn)];
                    }
                    mysqli_stmt_close($stmt_driver);
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mempersiapkan statement SQL untuk driver: ' . mysqli_error($conn)];
                }
            }
        } else {
             $response = ['status' => 'error', 'message' => 'Gagal mempersiapkan statement SQL untuk pengecekan driver: ' . mysqli_error($conn)];
        }
    }
    echo json_encode($response); // Kirim respons JSON kembali ke AJAX
    exit; // Hentikan eksekusi PHP selanjutnya
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
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

            <div class="p-4 mt-4 flex-1 overflow-auto bg-gray-50">
                <div class="bg-white shadow-lg rounded-lg p-8 w-full mx-auto max-w-4xl">
                    <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">EDIT DATA PENGGUNA</h2>

                    <div class="mb-6">
                        <label for="id_role_display" class="block font-semibold text-gray-700 mb-2">Role
                            Pengguna</label>
                        <select id="id_role_display" name="id_role_display"
                            class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-700 cursor-not-allowed"
                            disabled>
                            <option value="pegawai" <?= ($current_role_to_edit === 'pegawai') ? 'selected' : '' ?>>
                                Pegawai</option>
                            <option value="driver" <?= ($current_role_to_edit === 'driver') ? 'selected' : '' ?>>Driver
                            </option>
                        </select>
                        <input type="hidden" id="hidden_role" name="role"
                            value="<?= htmlspecialchars($current_role_to_edit) ?>">
                    </div>

                    <form id="main-form" method="post" enctype="multipart/form-data" class="space-y-4">
                        <div id="dynamic-form-container">
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
    <?php include_once '../components/footer.php'; ?>




    <script>
    $(document).ready(function() {
        // Ambil ID dan role dari variabel PHP (diatur dari URL)
        const currentId = <?= json_encode($current_id_to_edit) ?>;
        const currentRole = <?= json_encode($current_role_to_edit) ?>;

        // Muat form yang benar berdasarkan parameter URL saat halaman dimuat
        if (currentId && currentRole) {
            loadForm(currentRole, currentId);
        } else {
            // Fallback atau penanganan error jika ID/role entah bagaimana hilang (meskipun PHP harusnya redirect)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID pengguna atau role tidak ditemukan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }

        // Dropdown pemilihan role dinonaktifkan di halaman edit, jadi tidak perlu listener perubahan untuk itu.
        // Role ditentukan oleh URL dan diteruskan ke loadForm serta pengiriman form melalui input tersembunyi.

        // Tangani pengiriman form via AJAX untuk form utama
        // Kita menggunakan $(document).on karena konten di dalam #dynamic-form-container bersifat dinamis
        $(document).on('submit', '#main-form', function(e) {
            e.preventDefault(); // Mencegah pengiriman form default browser

            var form = $(this);
            var formData = new FormData(form[0]); // Buat objek FormData dari form

            // Tambahkan role dan ID dari input tersembunyi ke formData untuk pengiriman
            formData.append('role', $('#hidden_role').val());
            formData.append('id_to_update',
            currentId); // Pastikan ID adalah bagian dari data yang dikirim

            // Penanganan khusus untuk konfirmasi password hanya untuk form Pegawai
            var role = formData.get('role');
            if (role.toLowerCase() === 'pegawai') { // Periksa 'pegawai' di sini, bukan 'Pegawai'
                var password = formData.get('password');
                var confirmPassword = formData.get('confirm_password');

                // Validasi jika password diisi
                if ((password !== '' && confirmPassword === '') || (password === '' &&
                        confirmPassword !== '')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Kedua kolom Password dan Confirm Password harus diisi atau dikosongkan bersamaan.',
                        confirmButtonText: 'OK'
                    });
                    return false; // Hentikan pengiriman AJAX
                }

                if (password !== '' && password !== confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Password dan Confirm Password tidak sama.',
                        confirmButtonText: 'OK'
                    });
                    return false; // Hentikan pengiriman AJAX
                }
            }

            // Kirim data form menggunakan AJAX
            $.ajax({
                url: 'edit_user.php', // Kirim ke file PHP *ini* untuk pemrosesan update
                type: 'POST',
                data: formData, // Kirim objek FormData
                processData: false, // Penting untuk FormData untuk mengirim upload file
                contentType: false, // Penting untuk FormData untuk mengirim upload file
                dataType: 'json', // Harapkan respons JSON dari PHP
                success: function(response) {
                    // Tangani respons JSON dari PHP
                    Swal.fire({
                        icon: response.status, // 'success', 'error', 'warning'
                        title: response.status === 'success' ? 'Berhasil!' : (
                            response.status === 'warning' ? 'Peringatan!' :
                            'Error!'),
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed && response.redirect) {
                            window.location.href = response
                                .redirect; // Redirect jika URL 'redirect' disediakan
                        } else if (result.isConfirmed && response.status ===
                            'success') {
                            // Jika berhasil tetapi tidak ada redirect spesifik (seharusnya tidak terjadi dengan redirect ke 'index.php')
                            // Opsional, muat ulang form untuk menampilkan data yang diperbarui atau kosongkan bidang jika tetap di halaman
                            loadForm(role,
                                currentId
                                ); // Muat ulang untuk menampilkan data yang diperbarui
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Catat teks respons lengkap untuk debugging terperinci
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    let errorMessage =
                        'Terjadi kesalahan saat mengirim data. Silakan coba lagi.';
                    try {
                        const jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse && jsonResponse.message) {
                            errorMessage = jsonResponse.message;
                        }
                    } catch (e) {
                        // Bukan respons JSON, gunakan pesan umum
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    // Fungsi untuk memuat form dinamis via AJAX (permintaan GET)
    function loadForm(role, id_to_load) {
        var requestData = {
            load_form: role,
            id_to_load: id_to_load // Teruskan ID agar PHP dapat mengambil data spesifik
        };

        $.ajax({
            url: 'edit_user.php', // Minta HTML dari file PHP *ini*
            type: 'GET',
            data: requestData,
            success: function(data) {
                // Ganti konten wadah di dalam form utama
                $('#dynamic-form-container').html(data);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Load Form Error:", status, error, xhr.responseText);
                let errorMessage = 'Gagal memuat form. Silakan coba lagi.';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse && jsonResponse.message) {
                        errorMessage = jsonResponse.message;
                    }
                } catch (e) {
                    // Bukan respons JSON, gunakan pesan umum
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    }
    </script>
</body>

</html>