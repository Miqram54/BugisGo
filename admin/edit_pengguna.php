<?php
session_start();
include('koneksi.php'); // Koneksi ke database

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Pastikan userID ada dalam URL
if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];
    
    // Query untuk mengambil data pengguna berdasarkan userID
    $query = "SELECT * FROM users WHERE userID = $userID";
    $result = $conn->query($query);

    // Check if the query is successful and result is returned
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // Redirect or show error message if no data is found
        echo "<script>alert('Pengguna tidak ditemukan!'); window.location.href = 'pengguna.php';</script>";
        exit;
    }
} else {
    // Redirect if no userID is set
    header("Location: pengguna.php");
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
                    <li class="active">
                        <a href="user.php">
                            <i class="pe-7s-user"></i>
                            <p>Pengguna</p>
                        </a>
                    </li>
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
                                    echo "| <b>" . $jam . " | EDIT PENGGUNA</b>";
                                    ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content">
                <div class="container-fluid">
                    <h1 class="mb-4">Form Edit Pengguna</h1>
                    <!-- Form Edit Pengguna -->
                    <form action="proses/proses_edit_pengguna.php" method="POST">
                        <input type="hidden" name="userID" value="<?php echo $user['userID']; ?>" />
                        <div class="card">
                            <div class="card-header">
                                <h4 class="title">Edit Data Pengguna</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="nama" class="col-md-4 col-form-label">Nama Pengguna</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $user['nama']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label">Email</label>
                                    <div class="col-md-8">
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="role" class="col-md-4 col-form-label">Role</label>
                                    <div class="col-md-8">
                                        <select name="role" class="form-control" required>
                                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            <option value="pengunjung" <?php echo $user['role'] == 'pengunjung' ? 'selected' : ''; ?>>Pengunjung</option>
                                            <option value="pengelola" <?php echo $user['role'] == 'pengelola' ? 'selected' : ''; ?>>Pengelola</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-between">
                                    <div class="col-md-4">
                                        <button type="submit" name="update_pengguna" class="btn btn-primary">Update Pengguna</button>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a href="user.php" class="btn btn-secondary">Kembali</a>
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
