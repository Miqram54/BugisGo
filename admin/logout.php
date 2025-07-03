<?php
session_start(); // Memulai sesi

// Menghapus semua session yang ada
session_unset(); // Menghapus semua variabel session

// Menghancurkan sesi
session_destroy(); // Menghancurkan session

// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php");
exit;
?>
