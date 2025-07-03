<?php
// Header konfigurasi
header('Content-Type: text/plain');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// File untuk log debug
$logFile = 'debug_log.txt';
$logData = "======= REQUEST " . date('Y-m-d H:i:s') . " =======\n";
$logData .= "POST Data:\n" . print_r($_POST, true) . "\n\n";
file_put_contents($logFile, $logData, FILE_APPEND);

try {
    // Load library Midtrans
    $midtransPath = dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';
    
    if (!file_exists($midtransPath)) {
        throw new Exception("Midtrans library tidak ditemukan di: " . $midtransPath);
    }
    
    require_once $midtransPath;
    
    // Konfigurasi Midtrans
    \Midtrans\Config::$serverKey = 'SB-Mid-server-3Er9EYN-cebia58pTr1GqrCH';
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;
    
    // SOLUSI UNTUK SSL ERROR: Konfigurasi cURL untuk mengabaikan verifikasi SSL
    \Midtrans\Config::$curlOptions = [
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode(\Midtrans\Config::$serverKey . ':')
        ]
    ];
    
    // Validasi input
    if (empty($_POST['grandTotal'])) {
        throw new Exception("Grand Total tidak ditemukan");
    }
    
    if (empty($_POST['nama'])) {
        throw new Exception("Nama Pemesan tidak ditemukan");
    }
    
    if (empty($_POST['phone'])) {
        throw new Exception("Nomor HP tidak ditemukan");
    }
    
    // Siapkan data untuk Midtrans
    $orderId = 'ORDER-' . time() . '-' . rand(1000, 9999);
    
    $params = [
        'transaction_details' => [
            'order_id' => $orderId,
            'gross_amount' => (int) $_POST['grandTotal']
        ],
        'item_details' => [
            [
                'id' => $_POST['id_tiket'] ?? 'TIKET01',
                'price' => (int) $_POST['harga'] ?? 0,
                'quantity' => (int) $_POST['jumlah'] ?? 1,
                'name' => $_POST['namaWahana'] ?? 'Tiket Wahana'
            ]
        ],
        'customer_details' => [
            'first_name' => $_POST['nama'],
            'phone' => $_POST['phone']
        ]
    ];
    
    // Jika ada biaya admin, tambahkan sebagai item terpisah
    if (isset($_POST['adminFee']) && $_POST['adminFee'] > 0) {
        $params['item_details'][] = [
            'id' => 'ADMIN-FEE',
            'price' => (int) $_POST['adminFee'],
            'quantity' => 1,
            'name' => 'Biaya Admin'
        ];
    }
    
    // Log data yang akan dikirim ke Midtrans
    $logData = "Data untuk Midtrans:\n" . print_r($params, true) . "\n\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    
    // Dapatkan Snap Token
    // Gunakan try-catch khusus untuk menangkap error pada proses getSnapToken
    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        // Log token yang diterima
        file_put_contents($logFile, "Token dari Midtrans: " . $snapToken . "\n\n", FILE_APPEND);
        
        // Return token as plain text
        echo $snapToken;
    } catch (\Exception $snapError) {
        // Log khusus untuk error snap
        $errorSnapMsg = "ERROR SNAP: " . $snapError->getMessage() . "\n";
        $errorSnapMsg .= "File: " . $snapError->getFile() . " Line: " . $snapError->getLine() . "\n";
        $errorSnapMsg .= "Stack trace: " . $snapError->getTraceAsString() . "\n\n";
        file_put_contents($logFile, $errorSnapMsg, FILE_APPEND);
        
        // Coba metode alternatif untuk mendapatkan token
        // Ini untuk mengatasi masalah pada library
        $snapUrl = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $snapUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode(\Midtrans\Config::$serverKey . ':')
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            throw new Exception("Curl Error: " . curl_error($ch));
        }
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        file_put_contents($logFile, "Alternative SNAP Response: " . print_r($responseData, true) . "\n\n", FILE_APPEND);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            if (isset($responseData['token'])) {
                echo $responseData['token'];
            } else {
                throw new Exception("Token tidak ditemukan dalam response");
            }
        } else {
            throw new Exception("HTTP Error: " . $httpCode . " - " . ($responseData['error_messages'][0] ?? 'Unknown error'));
        }
    }
    
} catch (Exception $e) {
    // Log error
    $errorMsg = "ERROR: " . $e->getMessage() . "\n";
    $errorMsg .= "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    $errorMsg .= "Stack trace: " . $e->getTraceAsString() . "\n\n";
    file_put_contents($logFile, $errorMsg, FILE_APPEND);
    
    // Return error message
    echo "ERROR: " . $e->getMessage();
}