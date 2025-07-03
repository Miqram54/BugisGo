<?php
// File untuk menerima callback/notifikasi dari Midtrans

// Pastikan ini dijalankan hanya ketika ada request dari Midtrans
header("Content-Type: application/json");
require_once 'koneksi.php';

// Ambil data JSON dari Midtrans
$json_result = file_get_contents('php://input');
$result = json_decode($json_result);

// Log data callback untuk debugging
file_put_contents('midtrans_callback.log', date('Y-m-d H:i:s') . " - " . $json_result . "\n", FILE_APPEND);

// Jika tidak ada data atau tidak valid
if (!$result) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid payload']);
    exit;
}

// Ambil order_id dari callback data - ini harusnya berupa pemesananID
$order_id = $result->order_id;

// Ambil status transaksi
$transaction_status = $result->transaction_status;
$payment_type = $result->payment_type;
$transaction_id = $result->transaction_id;

// Mapping status Midtrans ke status di database
$status_pembayaran = 'menunggu pembayaran';

// Tentukan status pembayaran berdasarkan status transaksi dari Midtrans
if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
    $status_pembayaran = 'selesai pembayaran';
} elseif ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
    $status_pembayaran = 'gagal';
} elseif ($transaction_status == 'pending') {
    $status_pembayaran = 'menunggu pembayaran';
}

try {
    // Persiapkan statement untuk update status pembayaran
    $sql = "UPDATE pemesanan SET 
            statusPembayaran = ?, 
            paymentType = ?, 
            transactionId = ? 
            WHERE pemesananID = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query: " . $conn->error);
    }
    
    // Bind parameter
    $stmt->bind_param("sssi", $status_pembayaran, $payment_type, $transaction_id, $order_id);
    
    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
    }
    
    // Berhasil
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Status pembayaran berhasil diperbarui']);
    
} catch (Exception $e) {
    // Log error
    file_put_contents('midtrans_error.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Kirim response error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Tutup statement dan koneksi
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>