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
// Koneksi ke database
include '../config/koneksi.php'; // koneksi menggunakan $conn

$errors = []; // Inisialisasi array untuk menampung error
$success_message = ''; // Inisialisasi pesan sukses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_tangki = $_POST['no_tangki'];
    $no_batch = $_POST['no_batch'];
    $source = $_POST['source'];
    $test_report_no = $_POST['test_report_no'];
    $test_report_suffix = $_POST['test_report_suffix'];
    $test_report_date = $_POST['test_report_date'];
    $temperature = $_POST['temperature'];
    $density = $_POST['density'];
    $cu = $_POST['cu'];
    // $water_contamination = $_POST['water_contamination'];

    // Upload file
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory with full permissions recursively
    }
    $doc_url = '';

    if (!empty($_FILES['doc_file']['name'])) {
        $filename = basename($_FILES['doc_file']['name']);
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $target_path = $upload_dir . time() . "_" . $filename;
            if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $target_path)) {
                $doc_url = $target_path;
            } else {
                $errors[] = "Gagal upload file. Pastikan folder 'uploads' memiliki izin tulis.";
            }
        } else {
            $errors[] = "Tipe file tidak diizinkan. Hanya PDF, JPG, JPEG, dan PNG yang diperbolehkan.";
        }
    }

    if (empty($errors)) {
        // Prepare statement for security (prevents SQL injection)
        $stmt = $conn->prepare("INSERT INTO tangki (no_tangki, no_bacth, source, doc_url, test_report_no, test_report_let, test_report_date, density, temperature, cu ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
        $stmt->bind_param("ssssssssss", $no_tangki, $no_batch, $source, $doc_url, $test_report_no, $test_report_suffix, $test_report_date, $density, $temperature, $cu);

        if ($stmt->execute()) {
            $success_message = "Data tangki berhasil disimpan!";
            // No redirect here, so SweetAlert can display
        } else {
            $errors[] = "Gagal menyimpan data: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Insert Tangki</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
    .font-modify {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-label {
        font-weight: 600;
        /* Semi-bold */
        color: #4a5568;
        /* Gray-700 */
        margin-bottom: 0.25rem;
        /* Equivalent to mb-1 */
        display: block;
        /* Ensure it takes full width */
    }

    .form-input {
        width: 100%;
        border-width: 1px;
        /* border-gray-300 */
        border-color: #d1d5db;
        /* border-gray-300 */
        border-radius: 0.5rem;
        /* rounded-lg */
        padding: 0.5rem 0.75rem;
        /* p-2 */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        /* shadow-sm */
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-input:focus {
        border-color: #3b82f6;
        /* blue-500 */
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        /* ring-blue-500/50 */
        outline: none;
    }
    </style>
</head>

<body class="bg-gray-100 font-modify">
    <section class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-lg border-r border-gray-200">
            <?php include '../components/slidebar.php'; ?>
        </aside>

        <main class="flex-1 flex flex-col bg-gray-50">
            <div class="bg-white shadow-md p-6 flex justify-between items-center border-b border-gray-200">
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

            <section class="flex-1 p-8 bg-white shadow-lg rounded-lg m-8">
                <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b-2 pb-3 border-cyan-500">Form Tambah Data
                    Tangki Baru</h1>

                <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Oops! Ada masalah:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label for="no_tangki" class="form-label">No Tangki <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="no_tangki" name="no_tangki" class="form-input"
                                placeholder="Masukkan Nomor Tangki" required />
                        </div>
                        <div>
                            <label for="no_batch" class="form-label">No Batch / Tumpak <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="no_batch" name="no_batch" class="form-input"
                                placeholder="Masukkan Nomor Batch" required />
                        </div>
                        <div>
                            <label for="source" class="form-label">Source <span class="text-red-500">*</span></label>
                            <input type="text" id="source" name="source" class="form-input"
                                placeholder="Masukkan Sumber" required />
                        </div>
                        <div>
                            <label for="test_report_no" class="form-label">Test Report No <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="test_report_no" name="test_report_no" class="form-input"
                                placeholder="Masukkan Nomor Test Report" required />
                        </div>
                        <div>
                            <label for="test_report_suffix" class="form-label"></label>
                            <input type="text" id="test_report_suffix" name="test_report_suffix" value="PL2304/"
                                class="form-input" placeholder="Contoh: PL2304/TR/202-S2" />
                        </div>

                        <div>
                            <label for="test_report_date" class="form-label">Tanggal Test Report <span
                                    class="text-red-500">*</span></label>
                            <input type="date" id="test_report_date" name="test_report_date" class="form-input"
                                required />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="temperature" class="form-label">Temperature</label>
                            <input type="text" id="temperature" name="temperature" class="form-input"
                                placeholder="Masukkan Suhu" />
                        </div>
                        <div>
                            <label for="density" class="form-label">Density</label>
                            <input type="text" id="density" name="density" class="form-input"
                                placeholder="Masukkan Kepadatan" />
                        </div>
                        <div>
                            <label for="cu" class="form-label">CU</label>
                            <input type="text" id="cu" name="cu" class="form-input" placeholder="Masukkan nilai CU" />
                        </div>
                        <!-- <div>
                            <label for="water_contamination" class="form-label">Water Contamination (%)</label>
                            <input type="text" id="water_contamination" name="water_contamination" class="form-input"
                                placeholder="Masukkan Kontaminasi Air" />
                        </div> -->
                        <div>
                            <label for="doc_file" class="form-label">Upload Dokumen </label>
                            <input type="file" id="doc_file" name="doc_file" class="form-input py-1.5" />
                            <p class="text-sm text-gray-500 mt-1">Pilih File PDF atau Gambar:</p>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex justify-end gap-4 mt-8 pt-4 border-t border-gray-200">
                        <a href="index.php"
                            class="bg-red-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg shadow-md transition duration-300 ease-in-out">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-cyan-700 text-white font-semibold px-8 py-3 rounded-lg shadow-md transition duration-300 ease-in-out">
                            Submit
                        </button>
                    </div>
                </form>
            </section>
            <?php include_once '../components/footer.php'; ?>

        </main>
    </section>

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
</body>

</html>