<?php
// Pastikan session dimulai di awal setiap file PHP yang menggunakan sesi
session_start();

// Mengamankan include path. __DIR__ memastikan path relatif ke file ini.
// Jadi jika file ini di 'wesco2/auth/index.php', maka '../config/koneksi.php'
// akan mencari 'wesco2/config/koneksi.php'.
include __DIR__ . '/../config/koneksi.php';

// --- Proses Validasi Login ---
$login_error = ""; // Variabel untuk menyimpan pesan error login

// Cek apakah ada data yang dikirim melalui metode POST (form disubmit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data username dan password dari form
    // Gunakan trim() untuk menghilangkan spasi di awal/akhir input
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Password tidak perlu di-trim jika nanti pakai password_verify

    // Validasi input sederhana (pastikan tidak kosong)
    if (empty($username) || empty($password)) {
        $login_error = "Username dan password tidak boleh kosong.";
    } else {
        // Gunakan prepared statements untuk keamanan (mencegah SQL Injection)
        // Memilih kolom 'id_user', 'username', dan 'password' dari tabel 'user'
        $sql = "SELECT id_user, username, password FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);

        // Cek apakah prepared statement berhasil dibuat
        if ($stmt) {
            $stmt->bind_param("s", $username); // 's' untuk tipe data string pada placeholder '?'
            $stmt->execute(); // Jalankan query
            $stmt->store_result(); // Simpan hasil query agar bisa mendapatkan jumlah baris

            // Cek apakah username ditemukan (jumlah baris = 1)
            if ($stmt->num_rows == 1) {
                // Bind hasil kolom ke variabel PHP
                $stmt->bind_result($id_user, $db_username, $hashed_password_from_db);
                $stmt->fetch(); // Ambil satu baris hasil

                // --- BAGIAN KRITIS: VERIFIKASI PASSWORD DENGAN HASH ---
                // Anda **PASTI** harus menggunakan password_verify()
                // karena password di database disimpan dalam format hash.
                if (password_verify($password, $hashed_password_from_db)) {
                    // Login berhasil!
                    $_SESSION['loggedin'] = true;       // Menandai user sudah login
                    $_SESSION['id_user'] = $id_user;   // Menyimpan ID user ke sesi
                    $_SESSION['username'] = $db_username; // Menyimpan username ke sesi

                    // Redirect ke halaman tujuan setelah login berhasil
                    // Contoh: Mengarahkan ke '../AFRN/index.php'
                   // BARU (Dengan path absolut dari root)
header("Location: /wesco/home/index.php");
                    exit; // Sangat penting: hentikan eksekusi script setelah redirect
                } else {
                    // Password salah
                    $login_error = "Password yang Anda masukkan salah.";
                }
            } else {
                // Username tidak ditemukan
                $login_error = "Username tidak ditemukan.";
            }
            $stmt->close(); // Tutup statement setelah selesai
        } else {
            // Error saat prepare statement (misal, ada masalah pada query SQL)
            $login_error = "Terjadi kesalahan sistem (Kode: " . $conn->errno . "). Silakan coba lagi nanti.";
        }
    }
}

// Tutup koneksi database setelah semua operasi PHP selesai
// Ini penting untuk membebaskan sumber daya database
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Wesco - Aplikasi Konsinyasi Avtur</title>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/img/Wesco.png" type="image/x-icon">
</head>

<body class="h-screen w-screen flex overflow-hidden font-sans">

    <div class="w-4/5 h-full relative bg-cover bg-center text-white"
        style="background-image: url('https://pertamina-pmsol.com/file/files/2024/12/pmsol-64-02-1.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 flex flex-col justify-center h-full px-12">
            <h1 class="text-5xl font-extrabold mb-6 leading-tight">WELCOME TO<br>WESCO</h1>
            <p class="text-lg max-w-lg">
                Wesco merupakan platform penginputan dan pembuatan dokumen konsinyasi avtur berbasis aplikasi web untuk
                membantu percepatan dalam proses pengiriman dan pelayanan ke pelanggan.
            </p>
        </div>
    </div>

    <div class="w-1/2 flex items-center justify-center bg-white">
        <form action="" method="POST" class="w-full max-w-sm space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800">Welcome Back to Wesco</h2>
                <p class="text-sm text-gray-500 mt-1">Please Enter Your Details</p>
            </div>

            <?php
            // Tampilkan pesan error jika ada
            if (!empty($login_error)) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
                echo '<strong class="font-bold">Login Gagal!</strong>';
                echo '<span class="block sm:inline"> ' . htmlspecialchars($login_error) . '</span>';
                echo '</div>';
            }
            ?>

            <div>
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-1 w-full border-b-2 border-gray-300 focus:outline-none focus:border-blue-500 py-2 px-1">
            </div>

            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 w-full border-b-2 border-gray-300 focus:outline-none focus:border-blue-500 py-2 px-1">
            </div>

            <button type="submit"
                class="w-full py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">Login</button>

            <div class="text-center">
                <a href="#" class="text-sm text-blue-600 hover:underline">Lupa Password</a>
            </div>
        </form>
    </div>

</body>

</html>