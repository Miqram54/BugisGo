<?php
session_start();
include('koneksi.php'); // Koneksi ke database

// Pastikan hanya admin atau pengelola yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'pengelola'])) {
    header("Location: login.php");
    exit;
}
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
                <a href="pengelola_dashboard.php" class="simple-text">
                BugisGo Pengelola Waterpark
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
                    <li class="active">
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
                    <li>
                        <a href="pengelola_dashboard.php">
                            <i class="pe-7s-graph"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li>
                        <a href="daftar_tiket_pengelola.php">
                            <i class="pe-7s-ticket"></i>
                            <p>Daftar Tiket</p>
                        </a>
                    </li>
                    <li  class="active">>
                        <a href="wahana_pengelola.php">
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
                                    echo "| <b>" . $jam . " | TAMBAH WAHANA" . "</b>";
                                    ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


        <div class="content">
            <div class="container-fluid">
                <h1 class="mb-4">Form Tambah Wahana</h1>

                <!-- Form Tambah Wahana -->
                <form action="proses/proses_tambah_wahana_pengelola.php" method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="title">Form Wahana Baru</h4>
                        </div>
                        <div class="card-body">
                            <!-- Nama Wahana -->
                            <div class="form-group row">
                                <label for="nama" class="col-md-4 col-form-label">Nama Wahana</label>
                                <div class="col-md-8">
                                    <input type="text" name="nama" class="form-control" placeholder="Nama Wahana" required>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="form-group row">
                                <label for="deskripsi" class="col-md-4 col-form-label">Deskripsi</label>
                                <div class="col-md-8">
                                    <textarea name="deskripsi" class="form-control" placeholder="Deskripsi Wahana" required></textarea>
                                </div>
                            </div>

                            <!-- Usia Minimal -->
                            <div class="form-group row">
                                <label for="persyaratanUsia" class="col-md-4 col-form-label">Usia Minimal</label>
                                <div class="col-md-8">
                                    <input type="number" name="persyaratanUsia" class="form-control" placeholder="Usia Minimal" required>
                                </div>
                            </div>

                            <!-- Tinggi Minimal -->
                            <div class="form-group row">
                                <label for="persyaratanTinggi" class="col-md-4 col-form-label">Tinggi Minimal (cm)</label>
                                <div class="col-md-8">
                                    <input type="number" name="persyaratanTinggi" class="form-control" placeholder="Tinggi Minimal" required>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div class="form-group row">
                                <label for="rating" class="col-md-4 col-form-label">Rating Wahana</label>
                                <div class="col-md-8">
                                    <input type="number" name="rating" class="form-control" placeholder="Rating Wahana (0 - 5)" step="0.1" min="0" max="5" required>
                                </div>
                            </div>

                            <!-- Gambar -->
                            <div class="form-group row">
                                <label for="gambar" class="col-md-4 col-form-label">Gambar Wahana</label>
                                <div class="col-md-8">
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <!-- Button Submit -->
                            <div class="form-group row justify-content-between">
                                <div class="col-md-4">
                                    <button type="submit" name="submit" class="btn btn-primary">Simpan Wahana</button>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a href="wahana_pengelola.php" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <p class="copyright pull-left">&copy; <script>document.write(new Date().getFullYear())</script> BugisGo, Makassar</p>
            </div>
        </footer>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/chartist.min.js"></script>
<script src="assets/js/bootstrap-notify.js"></script>
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
<script src="assets/js/demo.js"></script>

</body>
</html>
