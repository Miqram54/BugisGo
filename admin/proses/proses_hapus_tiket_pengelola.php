<?php
include('../koneksi.php');

// Pastikan tiketID ada
if (isset($_GET['tiketID'])) {
    $tiketID = $_GET['tiketID'];

    // Hapus tiket dari database
    $sql = "DELETE FROM tiket WHERE tiketID = $tiketID";

    if ($conn->query($sql) === TRUE) {
        echo "Tiket berhasil dihapus!";
        header("Location: ../daftar_tiket_pengelola.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
