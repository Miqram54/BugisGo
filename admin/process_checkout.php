<?php
// Load Midtrans Library
require_once 'vendor/autoload.php'; // pastikan kamu sudah install Midtrans PHP SDK lewat Composer

\Midtrans\Config::$serverKey = 'YOUR_SERVER_KEY';
\Midtrans\Config::$isProduction = false; // true jika sudah production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil data dari form
$nama = $_POST['nama'];
$phone = $_POST['phone'];
$namaWahana = $_POST['namaWahana'];
$harga = (int) $_POST['harga'];
$jumlah = (int) $_POST['jumlah'];
$totalHarga = (int) $_POST['totalHarga'];
$adminFee = (int) $_POST['adminFee'];
$grandTotal = (int) $_POST['grandTotal'];

// Buat parameter transaksi
$transaction_details = [
    'order_id' => 'ORDER-' . time(), // Buat ID unik
    'gross_amount' => $grandTotal,
];

$item_details = [
    [
        'id' => 'ticket-001',
        'price' => $harga,
        'quantity' => $jumlah,
        'name' => $namaWahana
    ],
    [
        'id' => 'admin-001',
        'price' => $adminFee,
        'quantity' => 1,
        'name' => 'Admin Fee'
    ]
];

$customer_details = [
    'first_name' => $nama,
    'phone' => $phone,
];

// Gabungkan semua parameter
$params = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => $customer_details
];

// Generate Snap Token
$snapToken = \Midtrans\Snap::getSnapToken($params);

// Kembalikan Snap Token ke frontend
echo json_encode(['token' => $snapToken]);
?>
