<?php
session_start();
include('koneksi.php');

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengelola') {
    header("Location: login.php");
    exit;
}

// Query untuk mendapatkan data riwayat pesanan
$query = "
    SELECT 
        pemesanan.pemesananID, 
        users.nama AS userName, 
        users.email AS userEmail, 
        tiket.namaWahana AS ticketName, 
        tiket.harga AS ticketPrice, 
        pemesanan.jumlahTiket, 
        pemesanan.totalHarga, 
        pemesanan.statusPembayaran, 
        pemesanan.tanggalPemesanan 
    FROM pemesanan
    JOIN users ON pemesanan.userID = users.userID
    JOIN tiket ON pemesanan.tiketID = tiket.tiketID
    ORDER BY pemesanan.tanggalPemesanan DESC
";

// Eksekusi query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengelola - BugisGo</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Menambahkan CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet" />
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='assets/fonts/Pe-icon-7-stroke' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
</head>
<body>
<div class="wrapper">
       <!-- Sidebar -->
       <div class="sidebar" data-color="blue" data-image="images/bugisgo.jpg">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="pengelola_dashboard.php" class="simple-text">
                    BugisGo Pengelola
                </a>
            </div>
            <ul class="nav">
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <!-- Admin only items -->
                    <li>
                        <a href="admin_dashboard.php">
                            <i class="pe-7s-graph"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="user.php">
                            <i class="pe-7s-user"></i>
                            <p>Pengguna</p>
                        </a>
                    </li>
                    <li>
                        <a href="daftar_tiket.php">
                            <i class="pe-7s-ticket"></i>
                            <p>Daftar Tiket</p>
                        </a>
                    </li>
                    <li>
                    <li>
                        <a href="wahana.php">
                            <i class="pe-7s-ribbon"></i>
                            <p>Daftar Wahana</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="laporan.php">
                            <i class="pe-7s-note2"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'pengelola') { ?>
                    <!-- Pengelola only items -->
                    <li>
                    <li >
                        <a href="pengelola_dashboard.php">
                            <i class="pe-7s-graph"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="laporan_pengelola.php">
                            <i class="pe-7s-note2"></i>
                            <p>Laporan Tiket</p>
                        </a>
                    </li>
                    <li>
                        <a href="laporan_ulasan.php">
                            <i class="pe-7s-ribbon"></i>
                            <p>Daftar Ulasan</p>
                        </a>
                    </li>

                <?php } ?>

                <?php if ($_SESSION['role'] == 'pengunjung') { ?>
                    <!-- Pengunjung only items -->
                    <li>
                    <li class="active">
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
                    <li>
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
                                    echo "| <b>" . $jam . " | LAPORAN PEMBELIAN TIKET" . "</b>";
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
                    <h3 class="mb-4">Daftar Riwayat Pembelian Tiket</h3>

                    <!-- Tabel Riwayat Pesanan -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengguna</th>
                                <th>Email Pengguna</th>
                                <th>Nama Tiket</th>
                                <th>Jumlah Tiket</th>
                                <th>Total Harga</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Pemesanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['userName']; ?></td>
                                        <td><?php echo $row['userEmail']; ?></td>
                                        <td><?php echo $row['ticketName']; ?></td>
                                        <td><?php echo $row['jumlahTiket']; ?></td>
                                        <td>Rp. <?php echo number_format($row['totalHarga'], 0, ',', '.'); ?></td>
                                        <td><?php echo $row['statusPembayaran']; ?></td>
                                        <td><?php echo $row['tanggalPemesanan']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada riwayat pesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
    
</body>
</html>
