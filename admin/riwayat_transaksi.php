<?php
session_start();
include('koneksi.php');

// Pastikan hanya pengunjung yang bisa mengakses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Ambil userID dari session
$userID = $_SESSION['user_id'];

try {
    // Query untuk mengambil riwayat transaksi dengan nama wahana
    $sql = "SELECT p.*, t.namaWahana 
            FROM pemesanan p
            JOIN tiket t ON p.tiketID = t.tiketID
            WHERE p.userID = ? 
            ORDER BY p.tanggalPemesanan DESC";
    
    // Persiapkan statement
    $stmt = $conn->prepare($sql);
    
    // Periksa persiapan statement
    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan query: " . $conn->error);
    }
    
    // Bind parameter
    $stmt->bind_param("i", $userID);
    
    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
    }
    
    // Ambil hasil
    $result = $stmt->get_result();

    // Periksa apakah ada hasil
    $transactions = [];
    if ($result->num_rows > 0) {
        // Ambil semua transaksi
        while ($row = $result->fetch_assoc()) {
            // Default status jika kosong
            // Paksa semua status menjadi 'selesai pembayaran' jika sudah bayar
            if ($row['statusPembayaran'] == 'menunggu pembayaran') {
                $update_sql = "UPDATE pemesanan SET statusPembayaran = 'selesai pembayaran' WHERE pemesananID = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $row['pemesananID']);
                $update_stmt->execute();
                $update_stmt->close();
                $row['statusPembayaran'] = 'selesai pembayaran'; // Update status lokal
            }

            $transactions[] = $row;
        }
    }
} catch (Exception $e) {
    // Tangani error
    $error_message = $e->getMessage();
    $transactions = [];
}

// Tutup statement
if (isset($stmt)) {
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - BugisGo</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet" />
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    <style>
        .status-menunggu-pembayaran {
            color: orange;
            font-weight: bold;
        }
        .status-selesai-pembayaran {
            color: green;
            font-weight: bold;
        }
        .status-gagal {
            color: red;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.075);
        }
        .qr-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .qr-modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        .qr-modal-content img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-color="blue" data-image="images/bugisgo.jpg">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="pengunjung_dashboard.php" class="simple-text">
                    BugisGo Pengunjung
                </a>
            </div>
            <ul class="nav">
                <?php if ($_SESSION['role'] == 'pengunjung') { ?>
                    <li>
                        <a href="pengunjung_dashboard.php">
                            <i class="pe-7s-graph"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="pesan_tiket.php">
                            <i class="pe-7s-user"></i>
                            <p>Pesan Tiket</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="riwayat_transaksi.php">
                            <i class="pe-7s-note2"></i>
                            <p>Riwayat Transaksi</p>
                        </a>
                    </li>
                    <li>
                        <a href="ulasan.php">
                            <i class="pe-7s-science"></i>
                            <p>Rating & Ulasan</p>
                        </a>
                    </li>
                <?php } ?>

                <li>
                    <a href="logout.php">
                        <i class="pe-7s-power"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        <li>
                            <a href="#">
                                <i class="fa fa-calendar"></i>
                                <?php
                                $tanggal = mktime(date("m"), date("d"), date("Y"));
                                echo "Tanggal & Pukul, <b>" . date("d-M-Y", $tanggal) . "</b> ";
                                date_default_timezone_set('Asia/Makassar');
                                $jam = date("H:i:s");
                                echo "| <b>" . $jam . " | RIWAYAT TRANSAKSI" . "</b>";
                                ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content">
            <div class="container-fluid">
                <h3 class="mb-4">Daftar Riwayat Transaksi Pembelian Tiket</h3>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <?php if (count($transactions) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Wahana</th>
                                <th>Tanggal Kunjungan</th>
                                <th>Jumlah Tiket</th>
                                <th>Total Harga</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Pemesanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $index => $transaction): 
                                // Normalisasi status pembayaran
                                $status = strtolower(str_replace(' ', '-', $transaction['statusPembayaran'])); 
                                $original_status = strtolower($transaction['statusPembayaran']);
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($transaction['namaWahana'] ?? 'Tidak Diketahui') ?></td>
                                    <td><?= htmlspecialchars($transaction['tanggalKunjungan'] ?? '-') ?></td>
                                    <td><?= $transaction['jumlahTiket'] ?? '-' ?></td>
                                    <td>Rp <?= number_format($transaction['totalHarga'] ?? 0, 0, ',', '.') ?></td>
                                    <td>
                                        <span class="status-<?= $status ?>">
                                            <?= htmlspecialchars($transaction['statusPembayaran'] ?? 'Tidak Diketahui') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($transaction['tanggalPemesanan'] ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        switch ($original_status) {
                                            case 'menunggu pembayaran':
                                                echo '<a href="bayar.php?id=' . $transaction['pemesananID'] . '" class="btn btn-sm btn-warning">Bayar</a>';
                                                break;
                                            case 'selesai pembayaran':
                                                ?>
                                                <div class="btn-group">
                                                    <button onclick="showQRCode(<?= $transaction['pemesananID'] ?>)" class="btn btn-sm btn-info">Lihat QR</button>
                                                    <a href="download_tiket.php?id=<?= $transaction['pemesananID'] ?>" class="btn btn-sm btn-success">Unduh Tiket</a>
                                                </div>
                                                <?php
                                                break;
                                            case 'gagal':
                                                echo '<a href="pesan_tiket.php" class="btn btn-sm btn-danger">Pesan Ulang</a>';
                                                break;
                                            default:
                                                echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <p>Belum ada transaksi yang dilakukan.</p>
                        <a href="pesan_tiket.php" class="btn btn-primary mt-3">Pesan Tiket Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div id="qrModal" class="qr-modal">
            <div class="qr-modal-content">
                <h2>QR Tiket</h2>
                <div id="qrCodeContainer"></div>
                <button onclick="closeQRModal()" class="btn btn-secondary mt-3">Tutup</button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <p class="copyright pull-left">&copy; <script>document.write(new Date().getFullYear())</script> BugisGo, Makassar</p>
            </div>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
function showQRCode(pemesananID) {
    fetch(`get_tiket_detail.php?id=${pemesananID}`)
        .then(response => response.json())
        .then(data => {
            const qr = qrcode(0, 'M');
            const tiketInfo = `Nama Wahana: ${data.namaWahana}
Tanggal Kunjungan: ${data.tanggalKunjungan}
Jumlah Tiket: ${data.jumlahTiket}
Total Harga: Rp ${data.totalHarga.toLocaleString('id-ID')}
ID Pemesanan: ${pemesananID}`;
            qr.addData(tiketInfo);
            qr.make();
            
            const qrContainer = document.getElementById('qrCodeContainer');
            qrContainer.innerHTML = qr.createImgTag(5);
            
            document.getElementById('qrModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil detail tiket');
        });
}

function closeQRModal() {
    document.getElementById('qrModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('qrModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>
