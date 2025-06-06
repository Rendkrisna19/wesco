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

$errors = [];

if (!isset($_GET['idTangki'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['idTangki']);
$query = mysqli_query($conn, "SELECT * FROM tangki WHERE id_tangki = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan.");
}

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

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir);
    $doc_url = $data['doc_url'];

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
        $sql = "UPDATE tangki SET 
                    no_tangki = '$no_tangki',
                    no_bacth = '$no_batch',
                    source = '$source',
                    doc_url = '$doc_url',
                    test_report_no = '$test_report_no',
                    test_report_let = '$test_report_suffix',
                    test_report_date = '$test_report_date',
                    density = '$density',
                    temperature = '$temperature',
                    cu = '$cu',
                    water_contamination_ter = '$water_contamination'
                WHERE id_tangki = $id";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal update data: " . mysqli_error($conn);
        }
    }
}
?>

<!-- ... bagian PHP tetap sama ... -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Tangki</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
            <!-- Main content -->
            <div class="flex-1 p-10 bg-white">
                <!-- Header -->
                <div class="mb-8">
                    <p class="text-gray-700  font-semibold mt-1">FORM TANGKI</p>
                </div>

                <!-- Card Form -->
                <div class="bg-white p-8 rounded-lg shadow w-full max-w-5xl">
                    <?php if (!empty($errors)): ?>
                    <div class="mb-4 text-red-600 font-semibold">
                        <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="block text-gray-700 font-semibold">No Tangki</label>
                            <input type="text" name="no_tangki" value="<?= htmlspecialchars($data['no_tangki']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">No Batch / Tumpak</label>
                            <input type="text" name="no_batch" value="<?= htmlspecialchars($data['no_bacth']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Source</label>
                            <input type="text" name="source" value="<?= htmlspecialchars($data['source']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Test Report No</label>
                            <input type="text" name="test_report_no"
                                value="<?= htmlspecialchars($data['test_report_no']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Test Report Suffix</label>
                            <input type="text" name="test_report_suffix"
                                value="<?= htmlspecialchars($data['test_report_let']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Tanggal Test Report</label>
                            <input type="date" name="test_report_date"
                                value="<?= htmlspecialchars($data['test_report_date']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">
                        </div>

                        <div class="space-y-4">
                            <label class="block text-gray-700 font-semibold">Temperature</label>
                            <input type="text" name="temperature" value="<?= htmlspecialchars($data['temperature']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Density</label>
                            <input type="text" name="density" value="<?= htmlspecialchars($data['density']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">CU</label>
                            <input type="text" name="cu" value="<?= htmlspecialchars($data['cu']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Water Contamination</label>
                            <input type="text" name="water_contamination"
                                value="<?= htmlspecialchars($data['water_contamination_ter']) ?>"
                                class="w-full border border-gray-300 p-2 rounded-md">

                            <label class="block text-gray-700 font-semibold">Upload Dokumen Baru (Opsional)</label>
                            <input type="file" name="doc_file"
                                class="w-full border border-gray-300 p-2 rounded-md bg-gray-50">
                            <?php if (!empty($data['doc_url'])): ?>
                            <p class="text-sm text-gray-600 mt-1">File lama:
                                <a href="<?= $data['doc_url'] ?>" target="_blank" class="text-blue-600 underline">Lihat
                                    Dokumen</a>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Tombol -->
                        <div class="col-span-2 flex justify-end gap-4 pt-6 border-t mt-6">
                            <a href="index.php"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md shadow">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow">Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>