<?php
session_start();
include('koneksi.php'); // Koneksi ke database

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Query untuk mendapatkan total pengunjung
$pengunjungQuery = "SELECT COUNT(*) AS total_pengunjung FROM users WHERE role = 'pengunjung'";
$pengunjungResult = $conn->query($pengunjungQuery);
$pengunjungData = $pengunjungResult->fetch_assoc();
$totalPengunjung = $pengunjungData['total_pengunjung'];

// Query untuk mendapatkan total user
$userQuery = "SELECT COUNT(*) AS total_user FROM users";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$totalUser = $userData['total_user'];

// Query untuk mendapatkan total wahana
$wahanaQuery = "SELECT COUNT(*) AS total_wahana FROM wahana";
$wahanaResult = $conn->query($wahanaQuery);
$wahanaData = $wahanaResult->fetch_assoc();
$totalWahana = $wahanaData['total_wahana'];

// Query untuk mendapatkan jumlah tiket yang terjual (menggunakan tabel pemesanan)
$tiketQuery = "SELECT SUM(jumlahTiket) AS total_tiket FROM pemesanan";
$tiketResult = $conn->query($tiketQuery);
$tiketData = $tiketResult->fetch_assoc();
$totalTiket = $tiketData['total_tiket'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BugisGo</title>
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
                <a href="admin_dashboard.php" class="simple-text">
                    BugisGo Admin
                </a>
            </div>
            <ul class="nav">
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <!-- Admin only items -->
                    <li>
                    <li class="active">
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
                        <a href="wahana.php">
                            <i class="pe-7s-ribbon"></i>
                            <p>Daftar Wahana</p>
                        </a>
                    </li>
                    <li>
                        <a href="laporan.php">
                            <i class="pe-7s-note2"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'pengelola') { ?>
                    <!-- Pengelola only items -->
                    <li>
                    <li class="active">
                        <a href="pengelola_dashboard.php">
                            <i class="pe-7s-graph"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="daftar_tiket.php">
                            <i class="pe-7s-ticket"></i>
                            <p>Daftar Tiket</p>
                        </a>
                    </li>
                    <li>
                        <a href="wahana.php">
                            <i class="pe-7s-ribbon"></i>
                            <p>Daftar Wahana</p>
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
                                    echo "| <b>" . $jam . " | DASHBOARD ADMIN" . "</b>";
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
                    <h1 class="mb-4">Dashboard Admin</h1>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <!-- Total Pengunjung -->
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-header">Total Pengunjung</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $totalPengunjung; ?></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total User -->
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">Total User</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $totalUser; ?></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Wahana -->
                        <div class="col-md-3">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Total Wahana</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $totalWahana; ?></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Tiket Terjual -->
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-header">Jumlah Tiket Terjual</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $totalTiket; ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Rows (Optional) -->
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <p class="copyright pull-left">
                        &copy; <script>document.write(new Date().getFullYear())</script> BugisGo, Admin Dashboard
                    </p>
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
