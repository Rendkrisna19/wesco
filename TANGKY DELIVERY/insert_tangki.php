<?php
// Koneksi ke database
include '../config/koneksi.php'; // koneksi menggunakan $conn

$errors = []; // Inisialisasi array untuk menampung error

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
    $water_contamination = $_POST['water_contamination'];

    // Upload file
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir);
    $doc_url = '';

    if (!empty($_FILES['doc_file']['name'])) {
        $filename = basename($_FILES['doc_file']['name']);
        $target_path = $upload_dir . time() . "_" . $filename;
        if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $target_path)) {
            $doc_url = $target_path;
        } else {
            $errors[] = "Gagal upload file.";
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO tangki (no_tangki, no_bacth, source, doc_url, test_report_no, test_report_let, test_report_date, density, temperature, cu, water_contamination_ter)
                VALUES ('$no_tangki', '$no_batch', '$source', '$doc_url', '$test_report_no', '$test_report_suffix', '$test_report_date', '$density', '$temperature', '$cu', '$water_contamination')";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>


<!-- Bagian HTML Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Insert Tangki</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white font-modify">
    <section class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </aside>

        <!-- Main content -->
        <main class="flex-1 flex flex-col bg-gray-50">
            <!-- Header -->
            <header class="bg-white p-6 flex justify-between items-center shadow-sm">
                <h1 class="text-2xl font-bold text-cyan-900">Selamat Datang di Wesco Hermanto Purba</h1>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600">Hermanto Purba</span>
                    <img src="user-icon.png" alt="User" class="w-8 h-8 rounded-full" />
                </div>
            </header>

            <section class="flex-1 p-10 bg-white shadow rounded-lg">
                <h1 class="text-xl font-semibold  text-gray-700 mb-2">Form Tambah Data Tangki</h1>

                <?php if (!empty($errors)): ?>
                <div class="mb-4 text-red-500">
                    <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="  grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom Kiri -->
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">No Tangki</label>
                            <input type="text" name="no_tangki" class="w-full border-gray-300 rounded-lg p-2 shadow-sm"
                                required />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">No Batch / Tumpak</label>
                            <input type="text" name="no_batch" class="w-full border-gray-300 rounded-lg p-2 shadow-sm"
                                required />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Source</label>
                            <input type="text" name="source" class="w-full border-gray-300 rounded-lg p-2 shadow-sm"
                                required />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Test Report No</label>
                            <input type="text" name="test_report_no"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" required />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Test Report Suffix</label>
                            <input type="text" name="test_report_suffix" value="PL0000"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Tanggal Test Report</label>
                            <input type="date" name="test_report_date"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" required />
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Temperature</label>
                            <input type="text" name="temperature"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Density</label>
                            <input type="text" name="density" class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">CU</label>
                            <input type="text" name="cu" class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Water Contamination</label>
                            <input type="text" name="water_contamination"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Upload Dokumen (PDF/Gambar)</label>
                            <input type="file" name="doc_file"
                                class="w-full border-gray-300 rounded-lg p-2 shadow-sm" />
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-span-1 md:col-span-2 flex justify-end gap-4 mt-6">
                        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                            Simpan
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </section>
</body>

</html>