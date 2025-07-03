<?php
session_start();
include('koneksi.php'); // Koneksi ke database

// Pastikan pengunjung sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Mendapatkan data pengguna yang login
$userID = $_SESSION['user_id'];
$userQuery = "SELECT * FROM users WHERE userID = '$userID'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();

// Query untuk mendapatkan wahana yang populer berdasarkan rating tertinggi
$wahanaQuery = "SELECT * FROM wahana ORDER BY rating DESC LIMIT 6"; // Ambil 3 wahana populer
$wahanaResult = $conn->query($wahanaQuery);

// Query untuk mendapatkan ulasan dan rating wahana
$ratingQuery = "SELECT namaWahana, AVG(rating) AS avgRating FROM ulasan GROUP BY namaWahana";
$ratingResult = $conn->query($ratingQuery);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - BugisGo</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Menambahkan CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet" />
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
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
                                    echo "| <b>" . $jam . " | DASHBOARD PENGUNJUNG" . "</b>";
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
                    <!-- Data Pengguna -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Profil Pengguna</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $userData['nama']; ?></h5>
                                    <p>Email: <?php echo $userData['email']; ?></p>
                                    <p>Role: <?php echo $userData['role']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wahana Populer -->
<h3 class="mt-4">Wahana Populer</h3>
<div class="row">
    <?php while ($row = $wahanaResult->fetch_assoc()) { ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-black bg-white d-flex flex-column">
                <div class="card-header"><?php echo $row['nama']; ?></div>
                <div class="card-body d-flex flex-column">
                    <img src="<?php echo $row['gambar']; ?>" class="img-fluid mb-2" alt="Gambar Wahana">
                    <p class="flex-grow-1"><?php echo $row['deskripsi']; ?></p>
                    <p>Rating: <?php echo isset($row['avgRating']) ? number_format($row['avgRating'], 2) : 'Belum ada rating'; ?></p>
                    <a href="ulasan.php?wahana=<?php echo urlencode($row['nama']); ?>" class="btn btn-black mt-auto">Lihat Ulasan</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <p class="copyright pull-left">
                        &copy; <script>document.write(new Date().getFullYear())</script> Mini Zoo, Yogyakarta
                    </p>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/chartist.min.js"></script>
    <script src="assets/js/bootstrap-notify.js"></script>
    <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
    <script src="assets/js/demo.js"></script>

</body>
</html>
