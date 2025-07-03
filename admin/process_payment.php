<?php
session_start();
include('koneksi.php');

// Pastikan hanya pengunjung yang bisa mengakses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID pemesanan yang dikirim
if (!isset($_POST['pemesananID']) || empty($_POST['pemesananID'])) {
    header("Location: riwayat_transaksi.php?error=invalid_order");
    exit;
}

$pemesananID = $_POST['pemesananID'];
$userID = $_SESSION['user_id'];
$paymentMethod = $_POST['payment_method'] ?? 'unknown';

try {
    // 1. Verifikasi pemesanan milik user ini
    $checkSql = "SELECT * FROM pemesanan WHERE pemesananID = ? AND userID = ?";
    $checkStmt = $conn->prepare($checkSql);
    
    if (!$checkStmt) {
        throw new Exception("Gagal mempersiapkan query: " . $conn->error);
    }
    
    $checkStmt->bind_param("ii", $pemesananID, $userID);
    
    if (!$checkStmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $checkStmt->error);
    }
    
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        // Pemesanan tidak ditemukan atau bukan milik user ini
        header("Location: riwayat_transaksi.php?error=invalid_order");
        exit;
    }
}
    
    $orderData