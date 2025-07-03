<?php
include('../koneksi.php'); // Koneksi ke database

// Memastikan form dikirim dengan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_wahana'])) {
    // Mengambil data dari form
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $persyaratanUsia = $_POST['persyaratanUsia'];
    $persyaratanTinggi = $_POST['persyaratanTinggi'];
    $rating = $_POST['rating'];

    // Menambahkan wahana ke dalam database
    $sql = "INSERT INTO wahana (nama, deskripsi, persyaratanUsia, persyaratanTinggi, rating) 
            VALUES ('$nama', '$deskripsi', '$persyaratanUsia', '$persyaratanTinggi', '$rating')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../wahana_pengelola.php"); // Redirect ke halaman dashboard setelah berhasil
        exit;
    } else {
        echo "Gagal menambahkan wahana: " . $conn->error;
    }
}
?>
