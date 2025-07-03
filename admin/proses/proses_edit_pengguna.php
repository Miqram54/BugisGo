<?php
session_start();
include('../koneksi.php'); // Koneksi ke database

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Cek jika form disubmit
if (isset($_POST['update_pengguna'])) {
    $userID = $_POST['userID'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = '';

    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash password
        $updateQuery = "UPDATE users 
                        SET nama = '$nama', email = '$email', password = '$password', role = '$role' 
                        WHERE userID = $userID";
    } else {
        // Jika password tidak diubah, hanya update nama, email, dan role
        $updateQuery = "UPDATE users 
                        SET nama = '$nama', email = '$email', role = '$role' 
                        WHERE userID = $userID";
    }

    // Eksekusi query
    if ($conn->query($updateQuery) === TRUE) {
        header("Location: ../user.php"); // Redirect ke halaman pengguna setelah berhasil
    } else {
        echo "Error: " . $updateQuery . "<br>" . $conn->error;
    }
}
?>
