<?php
include('../koneksi.php');

// Pastikan userID ada
if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Hapus pengguna dari database
    $sql = "DELETE FROM users WHERE userID = $userID";

    if ($conn->query($sql) === TRUE) {
        echo "Pengguna berhasil dihapus!";
        header("Location: ../user.php"); // Redirect to the pengguna management page after deletion
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
