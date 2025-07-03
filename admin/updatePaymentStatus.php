<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('koneksi.php');

// Fungsi logging yang komprehensif
function log_payment_update($message, $data = []) {
    $logFile = 'payment_update_log.txt';
    $timestamp = date('[Y-m-d H:i:s]');
    $logMessage = $timestamp . ' ' . $message . ' ' . json_encode($data) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Log awal request
log_payment_update('Request diterima', $_SERVER);

// Validasi session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    log_payment_update('Akses ditolak', [
        'session_role' => $_SESSION['role'] ?? 'Tidak diset',
        'logged_in' => $_SESSION['logged_in'] ?? 'Tidak diset'
    ]);
    
    http_response_code(403);
    echo json_encode(["error" => "Akses tidak sah"]);
    exit;
}

// Ambil data dari request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log data masukan
log_payment_update('Data diterima', $data);

// Validasi input
if (!isset($data['orderID']) || !isset($data['status'])) {
    log_payment_update('Error: Data tidak lengkap', $data);
    
    http_response_code(400);
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

$orderID = $data['orderID'];
$status = $data['status'];

// Daftar status yang valid
$valid_status = ['menunggu pembayaran', 'selesai pembayaran', 'gagal'];
if (!in_array($status, $valid_status)) {
    log_payment_update('Error: Status tidak valid', [
        'status' => $status,
        'valid_status' => $valid_status
    ]);
    
    http_response_code(400);
    echo json_encode(["error" => "Status pembayaran tidak valid"]);
    exit;
}

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Persiapkan statement untuk update
    $stmt = $conn->prepare("UPDATE pemesanan SET statusPembayaran = ? WHERE pemesananID = ?");
    
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query: " . $conn->error);
    }

    // Bind parameter
    $stmt->bind_param("si", $status, $orderID);
    
    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
    }

    // Log keberhasilan update
    log_payment_update('Status pembayaran berhasil diupdate', [
        'orderID' => $orderID,
        'status' => $status,
        'affected_rows' => $stmt->affected_rows
    ]);

    // Commit transaksi
    $conn->commit();

    // Kirim respon sukses
    echo json_encode([
        "message" => "Status pembayaran berhasil diperbarui",
        "status" => $status,
        "order_id" => $orderID
    ]);

} catch (Exception $e) {
    // Rollback transaksi
    $conn->rollback();

    // Log error
    log_payment_update('Kesalahan update status pembayaran', [
        'error' => $e->getMessage(),
        'orderID' => $orderID,
        'status' => $status
    ]);

    // Kirim respon error
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage(),
        "order_id" => $orderID
    ]);
} finally {
    // Tutup statement
    if (isset($stmt)) {
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();
}
?>