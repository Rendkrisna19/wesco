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

$response = []; // To store SweetAlert messages

// Variables to hold the ID and Role obtained from the initial GET request (URL)
$current_id_to_edit = 0;
$current_role_to_edit = '';

// --- Initial GET Request (when page is first loaded via a link from index.php) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['role'])) {
    $current_id_to_edit = (int)$_GET['id'];
    $current_role_to_edit = strtolower($_GET['role']); // Ensure lowercase for consistency

    // Basic validation: Check if ID is valid and role is known
    if ($current_id_to_edit <= 0 || !in_array($current_role_to_edit, ['pegawai', 'driver'])) {
        header("Location: index.php?error=Parameter ID atau role tidak valid.");
        exit;
    }

    // Verify if the record actually exists before proceeding
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
    // If it's a GET request without ID/role (e.g., direct access to edit_user.php), redirect to list
    header("Location: index.php");
    exit;
}

// Fetch bridger data for the dropdown in driver form (always needed for AJAX part loading forms)
$bridger_data = [];
$bridger_query = $conn->query("SELECT id_bridger, no_polisi FROM bridger");
if ($bridger_query) {
    while ($row = $bridger_query->fetch_assoc()) {
        $bridger_data[] = $row;
    }
}


// --- AJAX Request Handling (responds with HTML content for dynamic form sections) ---
// This part runs when JavaScript makes a GET request to load the form HTML, pre-filled with data.
if (isset($_GET['load_form'])) {
    header('Content-Type: text/html'); // Ensure HTML content type for AJAX response
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
    exit(); // Important: Stop script execution after sending AJAX content
}

// --- POST Request: Handle Form Submission for Update ---
// This part runs when the main form is submitted via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role']) && isset($_POST['id_to_update'])) {
    header('Content-Type: application/json'); // Respond with JSON
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak terduga.']; // Default response

    $id_to_update = (int)$_POST['id_to_update'];
    $role = strtolower($_POST['role']);

    // Handle Pegawai Update
    if ($role === 'pegawai') {
        $nama_lengkap_pegawai = $_POST['nama_lengkap'];
        $alamat_pegawai       = $_POST['alamat'];
        $tempat_lahir_pegawai = $_POST['tempat_lahir'];
        $tanggal_lahir_pegawai = $_POST['tanggal_lahir'];
        $username_pegawai     = $_POST['username'];
        $password             = $_POST['password']; // This might be empty if not changing
        $confirm              = $_POST['confirm_password']; // This might be empty if not changing
        $existing_ttd         = $_POST['existing_ttd'] ?? ''; // Current file name if not uploading new
        $existing_stempel     = $_POST['existing_stempel'] ?? ''; // Current file name if not uploading new

        if (!empty($password) && $password !== $confirm) {
            $response = ['status' => 'warning', 'message' => 'Password dan Confirm Password tidak sama!'];
        } else {
            $password_update_clause = "";
            $password_hash = null; // Will only be set if password is provided
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $password_update_clause = ", password = ?";
            }

            $gambar_ttd = $existing_ttd; // Start with existing value
            $gambar_stempel = $existing_stempel; // Start with existing value
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            // Process TTD Upload
            if (isset($_FILES['ttd']) && $_FILES['ttd']['error'] == 0 && $_FILES['ttd']['size'] > 0) {
                $ext = pathinfo($_FILES['ttd']['name'], PATHINFO_EXTENSION);
                $new_ttd_name = 'ttd_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['ttd']['tmp_name'], $upload_dir . $new_ttd_name)) {
                    $gambar_ttd = $new_ttd_name;
                    // Optional: Delete old file if it exists and a new one was uploaded successfully
                    if (!empty($existing_ttd) && file_exists($upload_dir . $existing_ttd)) {
                         unlink($upload_dir . $existing_ttd);
                    }
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file TTD.'];
                }
            }

            // Process Stempel Upload
            if (isset($_FILES['stempel']) && $_FILES['stempel']['error'] == 0 && $_FILES['stempel']['size'] > 0) {
                $ext2 = pathinfo($_FILES['stempel']['name'], PATHINFO_EXTENSION);
                $new_stempel_name = 'stempel_' . time() . '.' . $ext2;
                if (move_uploaded_file($_FILES['stempel']['tmp_name'], $upload_dir . $new_stempel_name)) {
                    $gambar_stempel = $new_stempel_name;
                    // Optional: Delete old file if it exists and a new one was uploaded successfully
                    if (!empty($existing_stempel) && file_exists($upload_dir . $existing_stempel)) {
                         unlink($upload_dir . $existing_stempel);
                    }
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengunggah file Stempel.'];
                }
            }

            // Only proceed with DB update if no file upload errors or password mismatch
            if (!isset($response['status']) || ($response['status'] !== 'error' && $response['status'] !== 'warning')) {
                $sql = "UPDATE user SET nama_lengkap = ?, alamat = ?, tempat_lahir = ?, tanggal_lahir = ?, username = ?, gambar_ttd = ?, gambar_stempel = ? $password_update_clause WHERE id_user = ?";
                
                $types = "sssssss"; // For nama, alamat, tempat_lahir, tanggal_lahir, username, ttd, stempel
                $params = [
                    &$nama_lengkap_pegawai,
                    &$alamat_pegawai,
                    &$tempat_lahir_pegawai,
                    &$tanggal_lahir_pegawai,
                    &$username_pegawai,
                    &$gambar_ttd,
                    &$gambar_stempel
                ];

                if (!empty($password_hash)) {
                    $types .= "s"; // Add string type for password
                    $params[] = &$password_hash; // Add password hash to params
                }
                $types .= "i"; // Add integer type for id_user
                $params[] = &$id_to_update; // Add id_user to params

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, $types, ...$params); // Use splat operator for params
                
                if (mysqli_stmt_execute($stmt)) {
                    $response = ['status' => 'success', 'message' => 'Data pegawai berhasil diupdate!', 'redirect' => 'dashboard.php'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Gagal mengupdate data pegawai: ' . mysqli_error($conn)];
                }
                mysqli_stmt_close($stmt);
            }
        }
    } 
    // Handle Driver Update
    elseif ($role === 'driver') {
        $id_bridger_driver    = $_POST['id_bridger'];
        $nama_driver          = $_POST['nama_driver'];
        $no_ktp_driver        = $_POST['no_ktp'];
        $nama_lengkap_driver  = $_POST['nama_lengkap'];
        $alamat_driver        = $_POST['alamat'];
        $tempat_lahir_driver  = $_POST['tempat_lahir'];
        $tanggal_lahir_driver = $_POST['tanggal_lahir'];

        // Optional: Check if `nama_driver` already exists for another driver (excluding the current one being updated)
        $check_driver_sql = "SELECT COUNT(*) FROM driver WHERE nama_driver = ? AND id_driver != ?";
        $check_stmt = mysqli_prepare($conn, $check_driver_sql);
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
            // 'issssssi' -> int (id_bridger), string (nama_driver), string (no_ktp), string (nama_lengkap), string (alamat), string (tempat_lahir), string (tanggal_lahir), int (id_driver)
            mysqli_stmt_bind_param($stmt_driver, "issssssi", $id_bridger_driver, $nama_driver, $no_ktp_driver, $nama_lengkap_driver, $alamat_driver, $tempat_lahir_driver, $tanggal_lahir_driver, $id_to_update);
            
            if (mysqli_stmt_execute($stmt_driver)) {
                $response = ['status' => 'success', 'message' => 'Data driver berhasil diupdate!', 'redirect' => 'index.php'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal mengupdate data driver: ' . mysqli_error($conn)];
            }
            mysqli_stmt_close($stmt_driver);
        }
    }
    echo json_encode($response); // Send JSON response back to AJAX
    exit; // Stop further PHP execution
}
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
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap_session) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsK modernisation-remove User"
                        class="w-8 h-8 rounded-full">
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

    <script>
    $(document).ready(function() {
        // Get the ID and role from PHP variables (set from URL)
        const currentId = <?= json_encode($current_id_to_edit) ?>;
        const currentRole = <?= json_encode($current_role_to_edit) ?>;

        // Load the correct form based on the URL parameters on page load
        if (currentId && currentRole) {
            loadForm(currentRole, currentId);
        } else {
            // Fallback or error handling if somehow ID/role are missing (though PHP should redirect)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID pengguna atau role tidak ditemukan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }

        // The role selection dropdown is disabled on edit page, so no change listener needed for it.
        // The role is determined by the URL and passed to loadForm and form submission via hidden input.

        // Handle form submission via AJAX for the main form
        // We use $(document).on because the content within #dynamic-form-container is dynamic
        $(document).on('submit', '#main-form', function(e) {
            e.preventDefault(); // Prevent default browser form submission

            var form = $(this);
            var formData = new FormData(form[0]); // Create FormData object from the form

            // Add the role and ID from the hidden inputs to the formData for submission
            formData.append('role', $('#hidden_role').val());
            formData.append('id_to_update', currentId); // Ensure ID is part of submitted data

            // Special handling for password confirmation only for Pegawai form
            var role = formData.get('role');
            if (role === 'pegawai') { // Check for 'pegawai' here, not 'Pegawai'
                var password = formData.get('password');
                var confirmPassword = formData.get('confirm_password');

                // Only validate if a new password is being set
                if (password !== '' || confirmPassword !== '') {
                    if (password !== confirmPassword) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: 'Password dan Confirm Password tidak sama.',
                            confirmButtonText: 'OK'
                        });
                        return false; // Stop AJAX submission
                    }
                }
            }

            // Submit form data using AJAX
            $.ajax({
                url: 'edit_user.php', // Submit to *this* PHP file for update processing
                type: 'POST',
                data: formData, // Send FormData object
                processData: false, // Essential for FormData to send file uploads
                contentType: false, // Essential for FormData to send file uploads
                dataType: 'json', // Expect JSON response from PHP
                success: function(response) {
                    // Handle JSON response from PHP
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
                                .redirect; // Redirect if 'redirect' URL is provided
                        } else if (result.isConfirmed && response.status ===
                            'success') {
                            // If it's a success but no specific redirect (shouldn't happen with 'index.php' redirect)
                            // Optionally, reload the form to show updated data or clear fields if staying on page
                            loadForm(role,
                                currentId); // Reload to show updated data
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Log the full response text for detailed debugging
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi. Detail: ' +
                            (xhr.responseText ? JSON.parse(xhr.responseText)
                                .message : error),
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    // Function to load dynamic forms via AJAX (GET request)
    function loadForm(role, id_to_load) {
        var requestData = {
            load_form: role,
            id_to_load: id_to_load // Pass the ID so the PHP can fetch specific data
        };

        $.ajax({
            url: 'edit_user.php', // Request HTML from *this* PHP file
            type: 'GET',
            data: requestData,
            success: function(data) {
                // Replace the content of the container inside the main form
                $('#dynamic-form-container').html(data);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Load Form Error:", status, error, xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal memuat form. Silakan coba lagi. Detail: ' + (xhr.responseText ?
                        JSON.parse(xhr.responseText).message : error),
                    confirmButtonText: 'OK'
                });
            }
        });
    }
    </script>
</body>

</html>