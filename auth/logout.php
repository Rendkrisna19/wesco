<?php
/**
 * logout.php - Skrip Aman untuk Menghancurkan Sesi Pengguna
 *
 * Proses ini melakukan empat hal penting:
 * 1. Memulai sesi untuk mengaksesnya.
 * 2. Mengosongkan semua data di dalam array $_SESSION.
 * 3. Menghancurkan sesi sepenuhnya di sisi server.
 * 4. Mengarahkan pengguna kembali ke halaman login.
 */

// Selalu mulai sesi di awal untuk bisa memanipulasinya
session_start();

// Hapus semua variabel sesi dengan menimpanya menjadi array kosong.
// Ini adalah cara yang paling pasti untuk membersihkan semua data sesi.
$_SESSION = array();

// Hancurkan sesi. Ini akan menghapus file sesi di server.
session_destroy();

// Redirect pengguna kembali ke halaman login.
// Path '/wesco/auth/' adalah path absolut dari root domain Anda,
// ini adalah cara yang paling aman dan tidak akan salah.
header("Location: ../auth/index.php");
exit; // Wajib ada setelah redirect untuk menghentikan eksekusi skrip lebih lanjut.

?>