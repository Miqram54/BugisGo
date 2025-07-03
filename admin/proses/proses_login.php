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
    $email = bersihkan_input($_POST['username']);
    $password = $_POST['password'];
    $role = bersihkan_input($_POST['role']);

    // Validasi input
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: login.php");
        exit;
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password dengan password_verify
        if (password_verify($password, $user['password'])) { 
            // Login berhasil, set session
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['userID']; 
            $_SESSION['username'] = $user['nama']; 
            $_SESSION['role'] = $user['role'];

            // Tutup statement
            $stmt->close();

            // Arahkan berdasarkan role pengguna
            switch ($role) {
                case 'admin':
                    header("Location: ../admin_dashboard.php");
                    break;
                case 'pengunjung':
                    header("Location: ../pengunjung_dashboard.php");
                    break;
                case 'pengelola':
                    header("Location: ../pengelola_dashboard.php");
                    break;
                default:
                    // Kembalikan ke login jika role tidak valid
                    $_SESSION['error'] = "Role tidak valid!";
                    header("Location: ../login.php");
            }
            exit;
        } else {
            // Password salah
            $_SESSION['error'] = "Email atau password salah!";
            header("Location: ../login.php");
            exit;
        }
    } else {
        // Pengguna tidak ditemukan
        $_SESSION['error'] = "Pengguna Tidak Ditemukan!";
        header("Location: ../login.php");
        exit;
    }
} else {
    // Bukan request POST
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: ../login.php");
    exit;
}
?>