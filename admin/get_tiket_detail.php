<?php
session_start();
include('koneksi.php');

// Pastikan hanya pengunjung yang bisa mengakses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit;
}

// Periksa apakah ID pemesanan diberikan
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID Pemesanan tidak diberikan']);
    exit;
}

$pemesananID = intval($_GET['id']);
$userID = $_SESSION['user_id'];

try {
    // Query untuk mengambil detail tiket dengan nama wahana
    $sql = "SELECT p.*, t.namaWahana 
            FROM pemesanan p
            JOIN tiket t ON p.tiketID = t.tiketID
            WHERE p.pemesananID = ? AND p.userID = ?";
    
    // Persiapkan statement
    $stmt = $conn->prepare($sql);
    
    // Periksa persiapan statement
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query: " . $conn->error);
    }
    
    // Bind parameter
    $stmt->bind_param("ii", $pemesananID, $userID);
    
    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
    }
    
    // Ambil hasil
    $result = $stmt->get_result();

    // Periksa apakah ada hasil
    if ($result->num_rows > 0) {
        $tiket = $result->fetch_assoc();
        
        // Kembalikan detail tiket dalam format JSON
        header('Content-Type: application/json');
        echo json_encode([
            'namaWahana' => $tiket['namaWahana'],
            'tanggalKunjungan' => $tiket['tanggalKunjungan'],
            'jumlahTiket' => $tiket['jumlahTiket'],
            'totalHarga' => $tiket['totalHarga']
        ]);
    } else {
        // Tiket tidak ditemukan
        http_response_code(404);
        echo json_encode(['error' => 'Tiket tidak ditemukan']);
    }
} catch (Exception $e) {
    // Tangani error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Tutup statement
    if (isset($stmt)) {
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();
}