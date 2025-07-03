<?php
include('../koneksi.php'); // Koneksi ke database

// Memastikan ID wahana diterima
if (isset($_GET['id'])) {
    $wahanaID = $_GET['id'];

    // Menghapus wahana berdasarkan ID
    $sql = "DELETE FROM wahana WHERE wahanaID='$wahanaID'";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../wahana_pengelola.php"); // Redirect ke halaman dashboard setelah berhasil
        exit;
    } else {
        echo "Gagal menghapus wahana: " . $conn->error;
    }
}
?>
