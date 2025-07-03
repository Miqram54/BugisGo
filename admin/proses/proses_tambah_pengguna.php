<?php
session_start();
include('../koneksi.php'); // Koneksi ke database

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Cek jika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = $_POST['role'];

    // Query untuk menambahkan pengguna baru ke database
    $query = "INSERT INTO users (nama, email, password, role) 
              VALUES ('$nama', '$email', '$password', '$role')";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "Pengguna berhasil ditambahkan!";
        // Redirect ke halaman daftar pengguna setelah berhasil
        header("Location: ../user.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
