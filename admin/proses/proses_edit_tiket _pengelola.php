<?php
session_start();
include('../koneksi.php'); // Koneksi ke database

// Pastikan hanya admin atau pengelola yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'pengelola'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['tiketID'])) {
    $tiketID = $_POST['tiketID'];
    $namaWahana = $_POST['namaWahana'];
    $harga = $_POST['harga'];
    $jamOperasional = $_POST['jamOperasional'];
    $status = $_POST['status'];
    $gambar = ''; // Variabel gambar default kosong

    // Update query untuk mengubah data tiket
    $updateQuery = "UPDATE tiket 
                    SET namaWahana = '$namaWahana', harga = '$harga', jamOperasional = '$jamOperasional', 
                        status = '$status'
                    WHERE tiketID = '$tiketID'";

    if ($conn->query($updateQuery) === TRUE) {
        header("Location: ../daftar_tiket_pengelola.php"); // Redirect ke halaman daftar tiket setelah berhasil
    } else {
        echo "Error: " . $updateQuery . "<br>" . $conn->error;
    }
}
?>
