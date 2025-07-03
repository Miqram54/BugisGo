<?php
session_start();
include('koneksi.php');

// Pastikan hanya pengunjung yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - BugisGo</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet" />
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <style>
.modal-backdrop.in {
    opacity:0;
    display: none ;
}
    </style>
</head>

<body>
<div class="wrapper">

    <!-- Sidebar -->
    <div class="sidebar" data-color="blue" data-image="images/bugisgo.jpg">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="pengunjung_dashboard.php" class="simple-text">BugisGo Pengunjung</a>
            </div>
            <ul class="nav">
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <li><a href="admin_dashboard.php"><i class="pe-7s-graph"></i><p>Dashboard</p></a></li>
                    <li><a href="user.php"><i class="pe-7s-user"></i><p>Pengguna</p></a></li>
                    <li><a href="daftar_tiket.php"><i class="pe-7s-ticket"></i><p>Daftar Tiket</p></a></li>
                    <li><a href="wahana.php"><i class="pe-7s-ribbon"></i><p>Daftar Wahana</p></a></li>
                    <li><a href="laporan.php"><i class="pe-7s-note2"></i><p>Laporan</p></a></li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'pengelola') { ?>
                    <li><a href="pengelola_dashboard.php"><i class="pe-7s-graph"></i><p>Dashboard</p></a></li>
                    <li><a href="daftar_tiket.php"><i class="pe-7s-ticket"></i><p>Daftar Tiket</p></a></li>
                    <li><a href="wahana.php"><i class="pe-7s-ribbon"></i><p>Daftar Wahana</p></a></li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'pengunjung') { ?>
                    <li><a href="pengunjung_dashboard.php"><i class="pe-7s-graph"></i><p>Dashboard</p></a></li>
                    <li class="active"><a href="pesan_tiket.php"><i class="pe-7s-ticket"></i><p>Pesan Tiket</p></a></li>
                    <li><a href="riwayat_transaksi.php"><i class="pe-7s-note2"></i><p>Riwayat Transaksi</p></a></li>
                    <li><a href="ulasan.php"><i class="pe-7s-science"></i><p>Rating & Ulasan</p></a></li>
                <?php } ?>

                <li><a href="logout.php"><i class="pe-7s-power"></i><p>Logout</p></a></li>
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
                                date_default_timezone_set('Asia/Makassar');
                                echo "Tanggal & Pukul, <b>" . date("d-M-Y") . "</b> | <b>" . date("H:i:s") . " | TIKET BUGISGO</b>";
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
                <h3 class="mb-4">Daftar Tiket</h3>

                <?php
                $query = "SELECT * FROM tiket";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo '<div class="row">';

                    $gambarList = [
                        './images/bugisgo.jpg',
                        './images/bugis.webp',
                        './images/Promo.jpeg'
                    ];

                    $i = 0;

                    while ($row = mysqli_fetch_assoc($result)) {
                        $gambarPath = isset($gambarList[$i]) ? $gambarList[$i] : './images/default.jpg';

                        echo '<div class="col-md-4 mb-4">';
                        echo '  <div class="card shadow-sm h-100">';
                        echo '      <img src="' . $gambarPath . '" class="card-img-top" alt="gambar wahana" style="height: 200px; object-fit: cover;">';
                        echo '      <div class="card-body">';
                        echo '          <h5 class="card-title text-center">' . htmlspecialchars($row['namaWahana']) . '</h5>';
                        echo '          <p class="card-text"><strong>Jam Operasional:</strong> ' . htmlspecialchars($row['jamOperasional']) . '</p>';
                        echo '          <p class="card-text"><strong>Harga:</strong> Rp ' . number_format($row['harga'], 0, ',', '.') . '</p>';
                        echo '          <p class="card-text"><strong>Stok:</strong> ' . htmlspecialchars($row['status']) . '</p>';
                        echo '          <div class="text-center">';
                        echo '              <a href="#" class="btn btn-primary btn-sm pesan-btn" data-toggle="modal" data-target="#modalPesanTiket" data-id="' . $row['tiketID'] . '" data-nama="' . htmlspecialchars($row['namaWahana']) . '"data-harga="' . $row['harga'] .'">Pesan</a>';
                        echo '          </div>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';

                        $i++;
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Modal Pesan Tiket -->
<div class="modal fade" id="modalPesanTiket" tabindex="-1" role="dialog" aria-labelledby="modalPesanTiketLabel">
    <div class="modal-dialog" role="document" >
        <form action="checkout.php" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalPesanTiketLabel">Pilih Tanggal dan Jumlah Tiket</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Input tersembunyi untuk ID Tiket -->
                    <input type="hidden" name="tiketID" id="id_tiket">
                    <input type="hidden" name="harga" id="harga_satuan">

                    <!-- Nama Tiket -->
                    <div class="form-group">
                        <label for="nama_tiket">Nama Tiket</label>
                        <input type="text" class="form-control" name="namaWahana" id="nama_tiket" readonly>
                    </div>

                    <!-- Tanggal Kunjungan -->
                    <div class="form-group">
                        <label for="tanggal_kunjungan">Tanggal Kunjungan</label>
                        <input type="date" class="form-control" name="tanggal_pesanan" id="tanggal_kunjungan" required>
                    </div>

                    <!-- Jumlah Tiket -->
                    <div class="form-group">
                        <label for="jumlah_tiket">Jumlah Tiket</label>
                        <input type="number" class="form-control text-center" name="jumlah_tiket" id="jumlah_tiket" value="1" min="1" required>
                    </div>

                    <!-- Total Harga -->
                    <div class="form-group">
                        <label for="harga">Total Harga</label>
                        <input type="number" class="form-control" name="harga" id="harga" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Pesan Tiket</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container-fluid">
        <p class="copyright pull-left">
            &copy; <script>document.write(new Date().getFullYear())</script> BugisGo, Makassar
        </p>
    </div>
</footer>
</div>
</div>

<!-- JavaScript -->
<script src="assets/js/jquery.3.2.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<!-- Script Modal Isi Data -->
<script>
$(document).ready(function(){
    $('.pesan-btn').click(function(){
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var harga = $(this).data('harga');

        $('#id_tiket').val(id);
        $('#nama_tiket').val(nama);
        $('#harga_satuan').val(harga); // disimpan di input hidden
        $('#jumlah_tiket').val(1);
        $('#harga').val(harga); // total harga awal = harga satuan x 1
    });

    $('#jumlah_tiket').on('input', function(){
        var jumlah = $(this).val();
        var hargaSatuan = $('#harga_satuan').val();
        var total = jumlah * hargaSatuan;
        $('#harga').val(total);
    });
});
</script>


</body>
</html>