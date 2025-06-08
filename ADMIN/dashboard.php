<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

$id_user_session = $_SESSION['id_user'];
$username_session = $_SESSION['username'];
// Assuming nama_lengkap is also set in session from login
$nama_lengkap_session = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username_session;

include '../config/koneksi.php'; // koneksi menggunakan $conn

// --- Pagination Logic ---
$records_per_page = 5; // Number of records to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// --- Unified SQL Query for both Pegawai and Driver with UNION ALL ---
// Query for Pegawai (from 'user' table)
$sql_pegawai = "SELECT
                    u.id_user AS id,
                    u.nama_lengkap AS nama,
                    u.tanggal_lahir,
                    u.username,
                    r.nama_role AS role_name
                FROM
                    user u
                JOIN
                    role r ON u.id_role = r.id_role
                WHERE
                    r.nama_role = 'Pegawai'";

// Query for Driver (from 'driver' table)
// 'NULL AS username' because drivers don't have a username field in the 'driver' table.
// 'Driver' AS role_name to explicitly set their role for display.
$sql_driver = "SELECT
                    d.id_driver AS id,
                    d.nama_lengkap AS nama,
                    d.tanggal_lahir,
                    NULL AS username,
                    'Driver' AS role_name
                FROM
                    driver d";

// Combine both queries using UNION ALL
$sql_combined = "($sql_pegawai) UNION ALL ($sql_driver)";

// --- Get Total Records for Pagination ---
$sql_total_records = "SELECT COUNT(*) AS total FROM ($sql_combined) AS combined_users";
$result_total = $conn->query($sql_total_records);
$total_records = 0;
if ($result_total) {
    $row_total = $result_total->fetch_assoc();
    $total_records = $row_total['total'];
}

$total_pages = ceil($total_records / $records_per_page);

// --- Fetch Paginated Data ---
// Order by ID to ensure consistent pagination (adjust ORDER BY as needed, e.g., by nama)
$sql_paginated_data = "SELECT * FROM ($sql_combined) AS final_combined_users
                       ORDER BY id, role_name -- Order by ID first, then role name for stable sorting
                       LIMIT $records_per_page OFFSET $offset";

$result_paginated = $conn->query($sql_paginated_data);
$paginated_data = [];
if ($result_paginated) {
    while ($row = $result_paginated->fetch_assoc()) {
        $paginated_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .font-modify {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 font-modify">
    <div class="flex min-h-screen">

        <div class="w-64 bg-white shadow-md border-r border-gray-200">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <div class="flex-1 flex flex-col">
            <div class="bg-white shadow p-6 flex justify-between items-center border-b border-gray-200">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap_session) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap_session) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>

            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">DAFTAR PENGGUNA</h1>
                    <a href="index.php"
                        class="px-5 py-2 bg-blue-700 text-white rounded-lg shadow-md hover:bg-blue-800 transition duration-200 ease-in-out">
                        Insert User
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                    <table class="min-w-full table-auto text-left">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-700">No</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Nama</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Tanggal Lahir</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Username</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Role</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($paginated_data)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengguna.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php $counter = $offset + 1; // Start counter from current page's offset ?>
                            <?php foreach ($paginated_data as $user): ?>
                            <tr>
                                <td class="px-6 py-4"><?= $counter++ ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['nama']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['tanggal_lahir']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['username'] ?? '-') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['role_name']) ?></td>
                                <td class="px-6 py-4">
                                    <a href="edit_user.php?id=<?= htmlspecialchars($user['id']) ?>&role=<?= htmlspecialchars(strtolower($user['role_name'])) ?>"
                                        class="px-4 py-2 border border-blue-500 text-blue-600 rounded-full hover:bg-blue-50 hover:text-blue-700 transition duration-200">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-center items-center space-x-2">
                    <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition duration-200">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-4 py-2 rounded-lg transition duration-200
                            <?= ($i == $current_page) ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition duration-200">Next</a>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="text-center text-gray-500 text-sm mt-10 mb-4 p-4">
                Copyright © Your Website 2024
            </footer>
        </div>
    </div>
</body>

</html>