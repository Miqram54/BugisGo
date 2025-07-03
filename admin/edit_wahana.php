<?php
session_start();
include('koneksi.php'); // Koneksi ke database

// Pastikan hanya admin atau pengelola yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'pengelola'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['wahanaID'])) {
    $wahanaID = $_GET['wahanaID'];
    // Query untuk mengambil data wahana
    $query = "SELECT * FROM wahana WHERE wahanaID = $wahanaID";
    $result = $conn->query($query);

    // Check if the query is successful and result is returned
    if ($result && $result->num_rows > 0) {
        $wahana = $result->fetch_assoc();
    } else {
        // Redirect or show error message if no data is found
        echo "<script>alert('Wahana tidak ditemukan!'); window.location.href = 'wahana.php';</script>";
        exit;
    }
} else {
    // Redirect if no wahanaID is set
    header("Location: wahana.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wahana - BugisGo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Menambahkan custom CSS untuk layout -->
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
                                    echo "| <b>" . $jam . " | EDIT WAHANA" . "</b>";
                                    ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


            <div class="content">
                <div class="container-fluid">
                    <h1 class="mb-4">Form Edit Wahana</h1>
                    <!-- Form Edit Wahana -->
                    <form action="proses/proses_edit_wahana.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="wahanaID" value="<?php echo $wahana['wahanaID']; ?>" />
                        <div class="card">
                            <div class="card-header">
                                <h4 class="title">Edit Data Wahana</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="nama" class="col-md-4 col-form-label">Nama Wahana</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $wahana['nama']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="deskripsi" class="col-md-4 col-form-label">Deskripsi Wahana</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" required><?php echo $wahana['deskripsi']; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="persyaratanUsia" class="col-md-4 col-form-label">Usia Minimal</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="persyaratanUsia" name="persyaratanUsia" value="<?php echo $wahana['persyaratanUsia']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="persyaratanTinggi" class="col-md-4 col-form-label">Tinggi Minimal (cm)</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="persyaratanTinggi" name="persyaratanTinggi" value="<?php echo $wahana['persyaratanTinggi']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="gambar" class="col-md-4 col-form-label">Gambar Wahana</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                        <!-- Display existing image if available -->
                                        <?php if ($wahana['gambar']) { ?>
                                            <div class="mt-2">
                                                <img src="<?php echo $wahana['gambar']; ?>" alt="Current Wahana Image" width="100">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-between">
                                    <div class="col-md-4">
                                        <button type="submit" name="submit" class="btn btn-primary">Update Wahana</button>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a href="wahana.php" class="btn btn-secondary">Kembali</a>
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
