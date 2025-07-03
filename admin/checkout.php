<?php
session_start();

// Fungsi validasi input
function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Pastikan hanya pengunjung yang bisa mengakses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Cek apakah user_id ada di session
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

// Koneksi ke Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bugisgo";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set karakter set untuk menghindari masalah encoding
$conn->set_charset("utf8");

// Ambil user ID dari session
$userID = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Validasi dan bersihkan input
$idTiket = bersihkan_input($_POST['tiketID'] ?? '');
$namaWahana = bersihkan_input($_POST['namaWahana'] ?? 'Nama Wahana Tidak Diketahui');
$tanggalKunjungan = bersihkan_input($_POST['tanggal_pesanan'] ?? 'Tanggal Tidak Diisi');
$harga = isset($_POST['harga']) ? (int) $_POST['harga'] : 0;
$jumlah = isset($_POST['jumlah_tiket']) ? (int) $_POST['jumlah_tiket'] : 0;

// Validasi input
if (empty($idTiket) || $harga <= 0 || $jumlah <= 0) {
    die("Data pemesanan tidak valid.");
}

// Hitung total harga
$totalHarga = $harga * $jumlah;
$adminFee = 3000;
$grandTotal = $totalHarga + $adminFee;
$tanggalPemesanan = date("Y-m-d H:i:s"); // Waktu pemesanan dibuat

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Pertama, periksa struktur tabel
    $checkTableQuery = "SHOW COLUMNS FROM pemesanan LIKE 'tanggalKunjungan'";
    $tableResult = $conn->query($checkTableQuery);

    // Persiapkan statement untuk insert
    if ($tableResult->num_rows > 0) {
        // Jika kolom tanggalKunjungan ada
        $sql = "INSERT INTO pemesanan (userID, tiketID, jumlahTiket, totalHarga, statusPembayaran, tanggalPemesanan, tanggalKunjungan) 
                VALUES (?, ?, ?, ?, 'menunggu pembayaran', ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidss", $userID, $idTiket, $jumlah, $totalHarga, $tanggalPemesanan, $tanggalKunjungan);
    } else {
        // Jika kolom tanggalKunjungan tidak ada
        $sql = "INSERT INTO pemesanan (userID, tiketID, jumlahTiket, totalHarga, statusPembayaran, tanggalPemesanan) 
                VALUES (?, ?, ?, ?, 'menunggu pembayaran', ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiids", $userID, $idTiket, $jumlah, $totalHarga, $tanggalPemesanan);
    }

    // Eksekusi statement
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan pemesanan: " . $stmt->error);
    }

    // Ambil ID pemesanan yang baru saja disimpan
    $pemesananID = $conn->insert_id;

    // Commit transaksi
    $conn->commit();

    // Tutup statement
    $stmt->close();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pemesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Snap.js Midtrans - Gunakan versi Production jika sudah siap -->
    <script 
        src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="SB-Mid-client-ADFh5ZkK3eszHjqC"></script>

    <style>
        .card-ticket { 
            max-width: 500px; 
            margin: 30px auto; 
            border-radius: 20px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .btn-orange { 
            background-color: #FF6600; 
            color: white; 
            border-radius: 12px; 
        }
        #loading-indicator { 
            display: none; 
        }
    </style>
</head>
<body class="bg-light">
<div class="card card-ticket p-4">
    <div class="mb-3 text-center">
        <h5 class="fw-semibold">Konfirmasi Pemesanan</h5>
    </div>

    <!-- Informasi Pemesan -->
    <div class="mb-3">
        <strong>Nama Pemesan: <?= htmlspecialchars($username) ?></strong>
    </div>

    <!-- Informasi Tiket -->
    <div class="mb-3">
        <strong><?= htmlspecialchars($namaWahana) ?></strong><br>
        <small class="text-muted">Tanggal Kunjungan: <?= htmlspecialchars($tanggalKunjungan) ?></small><br>
        <small class="text-muted">Jumlah Tiket: <?= $jumlah ?></small>
    </div>

    <!-- Rincian Harga -->
    <div class="d-flex justify-content-between mb-1">
        <span>Harga Tiket</span>
        <span><?= $jumlah ?> x Rp <?= number_format($harga, 0, ',', '.') ?></span>
    </div>
    <div class="d-flex justify-content-between mb-1">
        <span>Total Harga</span>
        <span>Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
    </div>
    <div class="d-flex justify-content-between mb-1">
        <span>Biaya Admin</span>
        <span>Rp <?= number_format($adminFee, 0, ',', '.') ?></span>
    </div>

    <hr>
    <div class="d-flex justify-content-between">
        <span>Total Bayar</span>
        <span>Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
    </div>

    <!-- Form Pemesanan -->
    <form id="payment-form" method="POST">
        <div class="mb-3 mt-4">
            <label for="nama" class="form-label">Nama Pemesan</label>
            <input type="text" class="form-control" id="nama" name="nama" 
                   value="<?= htmlspecialchars($username) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Nomor HP</label>
            <input type="tel" class="form-control" id="phone" name="phone" 
                   pattern="[0-9]{10,13}" 
                   placeholder="Contoh: 08123456789" required>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="pemesananID" value="<?= $pemesananID ?>">
        <input type="hidden" name="id_tiket" value="<?= htmlspecialchars($idTiket) ?>">
        <input type="hidden" name="namaWahana" value="<?= htmlspecialchars($namaWahana) ?>">
        <input type="hidden" name="tanggal_kunjungan" value="<?= htmlspecialchars($tanggalKunjungan) ?>">
        <input type="hidden" name="harga" value="<?= $harga ?>">
        <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
        <input type="hidden" name="totalHarga" value="<?= $totalHarga ?>">
        <input type="hidden" name="adminFee" value="<?= $adminFee ?>">
        <input type="hidden" name="grandTotal" value="<?= $grandTotal ?>">

        <button type="button" id="pay-button" class="btn btn-orange w-100 mt-3">
            <span>Bayar Sekarang</span>
            <div id="loading-indicator" class="spinner-border spinner-border-sm ms-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const payButton = document.getElementById("pay-button");
    const form = document.getElementById("payment-form");
    const loadingIndicator = document.getElementById("loading-indicator");
    const phoneInput = document.getElementById("phone");

    // Validasi nomor HP
    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    payButton.addEventListener("click", function (e) {
        e.preventDefault();

        const phoneNumber = phoneInput.value;
        if (!/^[0-9]{10,13}$/.test(phoneNumber)) {
            alert('Nomor HP harus terdiri dari 10-13 digit angka');
            return;
        }

        payButton.disabled = true;
        loadingIndicator.style.display = "inline-block";

        const formData = new FormData(form);

        fetch('placeorder.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error: ' + response.status);
            }
            return response.text();
        })
        .then(result => {
            const snapToken = result.trim();
            console.log("Snap Token:", snapToken); // Debugging

            if (!snapToken || snapToken.includes('<') || snapToken.length < 10) {
                throw new Error("Token pembayaran tidak valid: " + snapToken);
            }

            // Cek ketersediaan snap
            if (typeof window.snap === 'undefined') {
                throw new Error("Midtrans Snap tidak tersedia. Periksa koneksi internet dan script Snap.");
            }

            // Panggil Snap
            window.snap.pay(snapToken, {
                onSuccess: function (result) {
                    console.log('Payment success:', result);
                    updatePaymentStatus(result.order_id, 'selesai pembayaran');
                    alert("Pembayaran berhasil!");
                    window.location.href = "riwayat_transaksi.php";
                },
                onPending: function (result) {
                    console.log('Payment pending:', result);
                    updatePaymentStatus(result.order_id, 'menunggu pembayaran');
                    alert("Menunggu pembayaran...");
                    window.location.href = "riwayat_transaksi.php";
                },
                onError: function (result) {
                    console.log('Payment error:', result);
                    updatePaymentStatus(result.order_id, 'gagal');
                    alert("Pembayaran gagal: " + (result.status_message || "Unknown error"));
                    payButton.disabled = false;
                    loadingIndicator.style.display = "none";
                },
                onClose: function () {
                    console.log('Payment closed');
                    alert("Pembayaran dibatalkan.");
                    payButton.disabled = false;
                    loadingIndicator.style.display = "none";
                }
            });
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan: " + error.message);
            payButton.disabled = false;
            loadingIndicator.style.display = "none";
        });
    });

    function updatePaymentStatus(orderID, status) {
        return fetch('updatePaymentStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ orderID, status })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Gagal memperbarui status: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Status pembayaran diperbarui:', data);
            return data;
        })
        .catch(error => {
            console.error('Update error:', error);
            alert("Gagal memperbarui status pembayaran: " + error.message);
        });
    }
});
</script>
</body>
</html>