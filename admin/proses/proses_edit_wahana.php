<?php
session_start();
include('../koneksi.php'); // Koneksi ke database

// Pastikan hanya admin atau pengelola yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'pengelola'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['wahanaID'])) {
    $wahanaID = $_POST['wahanaID'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $persyaratanUsia = $_POST['persyaratanUsia'];
    $persyaratanTinggi = $_POST['persyaratanTinggi'];
    $rating = $_POST['rating'];
    $gambar = '';

    // Menangani upload gambar
    if ($_FILES['gambar']['name']) {
        $gambar = 'images/' . $_FILES['gambar']['name'];  // Gambar akan disimpan di folder 'images'
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../' . $gambar);  // Memindahkan file gambar ke folder yang diinginkan
    }

    // Update query untuk mengubah data wahana
    $updateQuery = "UPDATE wahana 
                    SET nama = '$nama', deskripsi = '$deskripsi', persyaratanUsia = '$persyaratanUsia', 
                        persyaratanTinggi = '$persyaratanTinggi', rating = '$rating', gambar = '$gambar' 
                    WHERE wahanaID = '$wahanaID'";

    if ($conn->query($updateQuery) === TRUE) {
        header("Location: ../wahana.php"); // Redirect ke halaman wahana setelah berhasil
    } else {
        echo "Error: " . $updateQuery . "<br>" . $conn->error;
    }
}
?>
