<?php
session_start();

// Cek status login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

include '../config/koneksi.php';

// =========================================================================
// MODIFIKASI DIMULAI DI SINI: MENGAMBIL INFO USER YANG LOGIN
// =========================================================================

// Ambil id_user dari session. Beri nilai default 0 jika tidak ada.
$id_user = $_SESSION['id_user'] ?? 0;

// Siapkan variabel dengan nilai default untuk mencegah error
$username = 'Tamu';
$nama_lengkap = 'Tamu';

// Lakukan query hanya jika id_user valid
if ($id_user > 0) {
    // Siapkan query untuk mengambil username DAN nama_lengkap dalam satu kali jalan
    $stmt_user = $conn->prepare("SELECT username, nama_lengkap FROM user WHERE id_user = ?");
    
    if ($stmt_user) {
        $stmt_user->bind_param("i", $id_user);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        
        if ($result_user->num_rows > 0) {
            $user_data = $result_user->fetch_assoc();
            
            // Isi kedua variabel dengan data yang benar dari database
            $username = $user_data['username'];
            $nama_lengkap = $user_data['nama_lengkap'];
        }
        $stmt_user->close();
    }
}

$sql = "SELECT id_trans, nama_trans, alamat_trans FROM transportir";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Transportir</title>
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

            <div class="p-4 mt-4 flex-1 overflow-auto bg-white">
                <div class="p-4 mt-4 flex-1">
                    <h2 class="text-xl font-semibold  text-gray-700 mb-2">DAFTAR TRANSPORTIR</h2>

                    <a href="insert_transportir.php"
                        class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow float-right mb-2">
                        Insert Transportir
                    </a>

                    <div class="overflow-x-auto rounded-lg shadow bg-white clear-both">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-white text-blue-700 font-bold">
                                <tr>
                                    <th class="py-3 px-4">No</th>
                                    <th class="py-3 px-4">Nama Transportir</th>
                                    <th class="py-3 px-4">Alamat Transportir</th>
                                    <th class="py-3 px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                        if ($result && $result->num_rows > 0):
                            $no = 1;
                            while($row = $result->fetch_assoc()):
                        ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $no++ ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_trans']) ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['alamat_trans']) ?></td>
                                    <td class="py-2 px-4">
                                        <a href="edit_transportir.php?id=<?= $row['id_trans'] ?>"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1 border border-black hover:border-transparent rounded-full transition">Edit</a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Data tidak ditemukan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include_once '../components/footer.php'; ?>

</body>

</html>