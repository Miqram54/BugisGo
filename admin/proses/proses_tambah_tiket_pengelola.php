<?php
session_start();
include('../koneksi.php'); // Koneksi ke database

// Pastikan hanya admin atau pengelola yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'pengelola'])) {
    header("Location: login.php");
    exit;
}
// Cek jika form disubmit
if (isset($_POST['submit'])) {
    // Tidak perlu menyertakan tiketID, karena akan dihasilkan oleh AUTO_INCREMENT
    $namaWahana = $_POST['namaWahana'];
    $harga = $_POST['harga'];
    $jamOperasional = $_POST['jamOperasional'];
    $status = $_POST['status'];

    // Query untuk menambahkan tiket baru ke database
    $query = "INSERT INTO tiket (namaWahana, harga, jamOperasional, status) 
              VALUES ('$namaWahana', '$harga', '$jamOperasional', '$status')";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "Tiket berhasil ditambahkan!";
        // Redirect ke halaman daftar tiket setelah berhasil
        header("Location: ../daftar_tiket_pengelola.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
