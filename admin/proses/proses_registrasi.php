<?php
session_start();
include('../koneksi.php');

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Periksa apakah ini adalah request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Membersihkan dan memvalidasi input
    $nama = bersihkan_input($_POST['nama']);
    $email = bersihkan_input($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $role = bersihkan_input($_POST['role']);

    // Validasi input
    $error = [];

    if (empty($nama)) {
        $error[] = "Nama lengkap harus diisi";
    }

    if (empty($email)) {
        $error[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid";
    }

    if (empty($password)) {
        $error[] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $error[] = "Password minimal 6 karakter";
    }

    if ($password !== $konfirmasi_password) {
        $error[] = "Konfirmasi password tidak cocok";
    }

    // Periksa apakah email sudah terdaftar
    $stmt_cek_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt_cek_email->bind_param("s", $email);
    $stmt_cek_email->execute();
    $result_cek_email = $stmt_cek_email->get_result();
    if ($result_cek_email->num_rows > 0) {
        $error[] = "Email sudah terdaftar";
    }
    $stmt_cek_email->close();

    // Jika ada error, kembalikan ke halaman registrasi
    if (!empty($error)) {
        $_SESSION['error'] = implode('<br>', $error);
        header("Location: ../register.php");
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan statement untuk insert
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password_hash, $role);

    // Eksekusi statement
    try {
        if ($stmt->execute()) {
            // Registrasi berhasil
            $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
            header("Location: ../login.php");
            exit;
        } else {
            // Registrasi gagal
            $_SESSION['error'] = "Gagal mendaftarkan pengguna. Silakan coba lagi.";
            header("Location: ../register.php");
            exit;
        }
    } catch (Exception $e) {
        // Tangani error
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: ../register.php");
        exit;
    } finally {
        // Tutup statement
        $stmt->close();
    }
} else {
    // Bukan request POST
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: ../login.php");
    exit;
}

// Tutup koneksi
$conn->close();
?>