<?php
session_start();
include('koneksi.php');

// Pastikan hanya pengunjung yang bisa mengakses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Periksa apakah ID pemesanan diberikan
if (!isset($_GET['id'])) {
    die("ID Pemesanan tidak valid");
}

$pemesananID = intval($_GET['id']);
$userID = $_SESSION['user_id'];

try {
    // Query untuk mengambil detail tiket dengan nama wahana
    $sql = "SELECT p.*, t.namaWahana, u.nama AS namaPengunjung, p.snap_token
            FROM pemesanan p
            JOIN tiket t ON p.tiketID = t.tiketID
            JOIN users u ON p.userID = u.userID
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
        
        // Buat QR Code Manual (tanpa library eksternal)
        function generateSimpleQR($data) {
            $hash = md5($data);
            $qr_pattern = '';
            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    $index = $i * 10 + $j;
                    $qr_pattern .= (hexdec($hash[$index % strlen($hash)]) % 2 == 0) ? '█' : ' ';
                }
                $qr_pattern .= "\n";
            }
            return base64_encode($qr_pattern);
        }

        // Pilih data untuk QR Code
        $qr_data = json_encode([
            'pemesananID' => $tiket['pemesananID'],
            'namaWahana' => $tiket['namaWahana'],
            'namaPengunjung' => $tiket['namaPengunjung'],
            'tanggalKunjungan' => $tiket['tanggalKunjungan'],
            'snapToken' => $tiket['snap_token'] // Tambahkan Snap Token
        ]);

        // Buat PDF Manual
        ob_start();
        ?>
        %PDF-1.7
        1 0 obj
        <<
        /Type /Catalog
        /Pages 2 0 R
        >>
        endobj

        2 0 obj
        <<
        /Type /Pages
        /Kids [3 0 R]
        /Count 1
        >>
        endobj

        3 0 obj
        <<
        /Type /Page
        /Parent 2 0 R
        /Resources <<
            /Font <<
                /F1 4 0 R
            >>
        >>
        /MediaBox [0 0 595 842]
        /Contents 5 0 R
        >>
        endobj

        4 0 obj
        <<
        /Type /Font
        /Subtype /Type1
        /BaseFont /Helvetica
        >>
        endobj

        5 0 obj
        <<
        /Length 1000
        >>
        stream
        BT
        /F1 16 Tf
        50 750 Td
        (TIKET BUGISGO) Tj
        /F1 12 Tf
        0 -30 Td
        (Nama Pengunjung: <?= $tiket['namaPengunjung'] ?>) Tj
        0 -20 Td
        (Nama Wahana: <?= $tiket['namaWahana'] ?>) Tj
        0 -20 Td
        (Tanggal Kunjungan: <?= $tiket['tanggalKunjungan'] ?>) Tj
        0 -20 Td
        (Jumlah Tiket: <?= $tiket['jumlahTiket'] ?>) Tj
        0 -20 Td
        (Total Harga: Rp <?= number_format($tiket['totalHarga'], 0, ',', '.') ?>) Tj
        0 -20 Td
        (ID Pemesanan: <?= $tiket['pemesananID'] ?>) Tj
        0 -40 Td
        (QR Code:) Tj
        0 -20 Td
        (<?= base64_decode(generateSimpleQR($qr_data)) ?>) Tj
        ET
        endstream
        endobj

        xref
        0 6
        0000000000 65535 f
        0000000009 00000 n
        0000000056 00000 n
        0000000111 00000 n
        0000000223 00000 n
        0000000300 00000 n

        trailer
        <<
        /Size 6
        /Root 1 0 R
        >>
        startxref
        600
        %%EOF
        <?php
        $pdf_content = ob_get_clean();

        // Kirim header PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Tiket_BugisGo_' . $tiket['pemesananID'] . '.pdf"');
        header('Content-Length: ' . strlen($pdf_content));
        
        // Keluarkan konten PDF
        echo $pdf_content;
        exit;
    } else {
        // Tiket tidak ditemukan
        throw new Exception("Tiket tidak ditemukan");
    }
} catch (Exception $e) {
    // Tangani error
    echo "Error: " . $e->getMessage();
} finally {
    // Tutup statement
    if (isset($stmt)) {
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();
}