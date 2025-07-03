<?php
session_start();
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sertakan file koneksi
include('koneksi.php');

// Periksa koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Fungsi untuk memeriksa dan membuat tabel ulasan jika belum ada
function cekDanBuatTabelUlasan($conn) {
    $query = "CREATE TABLE IF NOT EXISTS ulasan (
        ulasanID INT AUTO_INCREMENT PRIMARY KEY,
        userID INT NOT NULL,
        namawahana VARCHAR(100) NOT NULL,
        rating VARCHAR(10) NOT NULL,
        komentar TEXT,
        tanggalUlasan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        nama VARCHAR(100)
    )";

    if (!$conn->query($query)) {
        error_log("Gagal membuat tabel ulasan: " . $conn->error);
        die("Gagal membuat tabel ulasan: " . $conn->error);
    }
}

// Panggil fungsi pemeriksaan tabel
cekDanBuatTabelUlasan($conn);

class Ulasan {
    private $conn;

    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }

    // Metode untuk menambahkan ulasan baru
    public function tambahUlasan($userID, $namawahana, $rating, $komentar = null) {
        try {
            // Validasi input
            if (empty($userID) || empty($namawahana) || empty($rating)) {
                throw new Exception("Semua field harus diisi");
            }

            // Validasi rating
            if ($rating < 1 || $rating > 5) {
                throw new Exception("Rating harus antara 1-5");
            }

            // Validasi panjang komentar jika ada
            if ($komentar !== null && strlen($komentar) > 500) {
                throw new Exception("Komentar maksimal 500 karakter");
            }

            // Ambil nama pengguna
            $stmt_nama = $this->conn->prepare("SELECT nama FROM users WHERE userID = ?");
            $stmt_nama->bind_param("i", $userID);
            $stmt_nama->execute();
            $result = $stmt_nama->get_result();
            $user = $result->fetch_assoc();
            $nama_pengguna = $user['nama'] ?? 'Pengguna';
            $stmt_nama->close();

            // Persiapkan statement untuk insert
            $stmt = $this->conn->prepare("
                INSERT INTO ulasan (userID, namawahana, rating, komentar, nama) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // Periksa persiapan statement
            if ($stmt === false) {
                throw new Exception("Gagal mempersiapkan query: " . $this->conn->error);
            }
            
            // Konversi rating ke string
            $ratingStr = strval($rating);
            
            // Bind parameter
            $stmt->bind_param("issss", $userID, $namawahana, $ratingStr, $komentar, $nama_pengguna);
            
            // Eksekusi statement
            if (!$stmt->execute()) {
                throw new Exception("Gagal menambahkan ulasan: " . $stmt->error);
            }
            
            // Ambil ID ulasan yang baru saja dibuat
            $ulasanID = $stmt->insert_id;
            
            // Tutup statement
            $stmt->close();
            
            return $ulasanID;
        } catch (Exception $e) {
            // Catat error
            error_log("Error tambahUlasan: " . $e->getMessage());
            throw $e;
        }
    }

    // Metode untuk melihat ulasan
    public function lihatUlasan($userID = null) {
        try {
            // Buat query dasar
            $sql = "SELECT * FROM ulasan WHERE 1=1";
            
            // Array untuk menyimpan parameter
            $params = [];
            $types = '';
            
            // Tambahkan filter berdasarkan userID jika disediakan
            if ($userID !== null) {
                $sql .= " AND userID = ?";
                $params[] = $userID;
                $types .= 'i';
            }
            
            // Tambahkan ordering
            $sql .= " ORDER BY tanggalUlasan DESC";
            
            // Persiapkan statement
            $stmt = $this->conn->prepare($sql);
            
            // Periksa persiapan statement
            if ($stmt === false) {
                throw new Exception("Gagal mempersiapkan query: " . $this->conn->error);
            }
            
            // Bind parameter jika ada
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            // Eksekusi query
            if (!$stmt->execute()) {
                throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
            }
            
            // Ambil hasil
            $result = $stmt->get_result();
            
            // Simpan ulasan dalam array
            $ulasan = [];
            while ($row = $result->fetch_assoc()) {
                $ulasan[] = $row;
            }
            
            // Tutup statement
            $stmt->close();
            
            return $ulasan;
        } catch (Exception $e) {
            // Catat error
            error_log("Error lihatUlasan: " . $e->getMessage());
            throw $e;
        }
    }

    // Metode untuk menghitung rata-rata rating per wahana
    public function hitungRataRating() {
        try {
            // Buat query
            $sql = "
                SELECT 
                    w.nama as namawahana, 
                    COALESCE(ROUND(AVG(CAST(u.rating AS DECIMAL)), 1), 0) as rata_rating, 
                    COALESCE(COUNT(u.rating), 0) as jumlah_ulasan,
                    COALESCE(GROUP_CONCAT(CONCAT(u.nama, ': ', u.komentar) SEPARATOR ' | '), 'Belum ada komentar') as komentar_ringkasan
                FROM 
                    wahana w
                LEFT JOIN 
                    ulasan u ON w.nama = u.namawahana
                GROUP BY 
                    w.nama
                ORDER BY 
                    w.nama
            ";
            
            // Eksekusi query
            $result = $this->conn->query($sql);
            
            // Periksa apakah query berhasil
            if ($result === false) {
                throw new Exception("Gagal menjalankan query: " . $this->conn->error);
            }
            
            // Simpan hasil dalam array
            $rating = [];
            while ($row = $result->fetch_assoc()) {
                $rating[] = $row;
            }
            
            return $rating;
        } catch (Exception $e) {
            // Catat error
            error_log("Error hitungRataRating: " . $e->getMessage());
            throw $e;
        }
    }
}

// Pastikan hanya pengunjung yang bisa mengakses halaman ini
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pengunjung') {
    header("Location: login.php");
    exit;
}

// Inisialisasi kelas Ulasan
$ulasanManager = new Ulasan($conn);

// Fungsi untuk membuat bintang rating
function generateStars($rating) {
    $fullStar = '<i class="fa fa-star text-warning"></i>';
    $emptyStar = '<i class="fa fa-star-o text-warning"></i>';
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? $fullStar : $emptyStar;
    }
    return $stars;
}

// Variabel untuk pesan
$error_message = '';
$success_message = '';

try {
    // Ambil ID pengguna dari sesi
    $userID = $_SESSION['user_id'];

    // Ambil daftar wahana dari tabel wahana
    $wahana = [];
    $wahana_query = $conn->query("SELECT nama FROM wahana ORDER BY nama");
    while ($row = $wahana_query->fetch_assoc()) {
        $wahana[] = $row;
    }

    // Proses submit ulasan baru
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Validasi input
            $namawahana = trim($_POST['wahanaID'] ?? '');
            $rating = intval($_POST['rating'] ?? 0);
            $komentar = trim($_POST['komentar'] ?? '');

            // Tambahkan ulasan
            $ulasanID = $ulasanManager->tambahUlasan($userID, $namawahana, $rating, $komentar);
            
            $success_message = "Ulasan berhasil ditambahkan dengan ID: " . $ulasanID;
            
            // Redirect untuk mencegah pengiriman ulang
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            $error_message = "Kesalahan: " . $e->getMessage();
        }
    }

    // Ambil ulasan pengguna
    $ulasan = $ulasanManager->lihatUlasan($userID);

    // Hitung rata-rata rating
    $ratingWahana = $ulasanManager->hitungRataRating();
} catch (Exception $e) {
    $error_message = "Kesalahan sistem: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - BugisGo</title>
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
        .star-rating {
            unicode-bidi: bidi-override;
            direction: rtl;
            text-align: left;
        }
        .star-rating > input {
            display: none;
        }
        .star-rating > label {
            display: inline-block;
            position: relative;
            width: 1.1em;
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
        }
        .star-rating > label:hover,
        .star-rating > label:hover ~ label,
        .star-rating > input:checked ~ label {
            color: #ffca08;
        }

        /* Gaya tambahan untuk ulasan */
        .card-title.text-primary {
            font-weight: 600;
        }

        .rating {
            color: #ffc107;
        }

        .card.shadow-sm {
            transition: transform 0.3s ease;
        }

        .card.shadow-sm:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .text-muted.fst-italic {
            line-height: 1.6;
        }
        .card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #e1e1e1;
    padding: 15px;
}

.card-body {
    padding: 20px;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
}

.rating {
    font-size: 16px;
    color: #ff9900;
}

.card-text {
    font-size: 14px;
    color: #6c757d;
}

.text-muted {
    font-style: italic;
}

.badge {
    font-size: 14px;
}

@media (max-width: 768px) {
    .card-body {
        padding: 10px;
    }

    .card-title {
        font-size: 16px;
    }

    .rating {
        font-size: 14px;
    }
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
                    <li>
                        <a href="riwayat_transaksi.php">
                            <i class="pe-7s-note2"></i>
                            <p>Riwayat Transaksi</p>
                        </a>
                    </li>
                    <li class="active">
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
                                echo "| <b>" . $jam . " | ULASAN DAN RATING" . "</b>";
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
                <h3 class="mb-4">Ulasan & Rating Pengunjung</h3>

                <!-- Pesan Error atau Sukses -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger mb-4">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success mb-4">
                        <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Form Tambah Ulasan -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Ulasan Baru</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="wahanaID" class="form-label">Pilih Wahana</label>
                                <select class="form-control" id="wahanaID" name="wahanaID" required>
                                    <option value="">Pilih Wahana</option>
                                    <?php foreach ($wahana as $w): ?>
                                        <option value="<?= htmlspecialchars($w['nama']) ?>">
                                            <?= htmlspecialchars($w['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" />
                                    <label for="star5">★</label>
                                    <input type="radio" id="star4" name="rating" value="4" />
                                    <label for="star4">★</label>
                                    <input type="radio" id="star3" name="rating" value="3" />
                                    <label for="star3">★</label>
                                    <input type="radio" id="star2" name="rating" value="2" />
                                    <label for="star2">★</label>
                                    <input type="radio" id="star1" name="rating" value="1" />
                                    <label for="star1">★</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="komentar" class="form-label">Komentar</label>
                                <textarea class="form-control" id="komentar" name="komentar" rows="3" 
                                          placeholder="Tulis ulasan Anda (maks 500 karakter)" 
                                          maxlength="500"></textarea>
                                <small class="form-text text-muted">Sisa karakter: <span id="karakter-tersisa">500</span></small>
                            </div>

                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                        </form>
                    </div>
                </div>

<!-- Daftar Ulasan Pengguna -->
<?php if (!empty($ulasan)): ?>
    <div class="card mt-4">
        <div class="card-header text-center">
            <h4 class="card-title">Ulasan Anda</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($ulasan as $review): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0 text-primary">
                                        <?= htmlspecialchars($review['namawahana']) ?>
                                    </h5>
                                    <div class="rating">
                                        <?= generateStars($review['rating']) ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($review['komentar'])): ?>
                                    <p class="card-text text-muted fst-italic">
                                        "<?= htmlspecialchars($review['komentar']) ?>"
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <i class="fa fa-calendar me-1"></i>
                                        <?= date('d M Y H:i', strtotime($review['tanggalUlasan'])) ?>
                                    </small>
                                    <span class="badge bg-primary">
                                        <?= $review['rating'] ?> Bintang
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center" role="alert">
        Anda belum memiliki ulasan. Silakan berikan ulasan Anda setelah mengunjungi wahana.
    </div>
<?php endif; ?>


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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hitung sisa karakter pada textarea komentar
    const komentarTextarea = document.getElementById('komentar');
    const sisaKarakterSpan = document.getElementById('karakter-tersisa');

    komentarTextarea.addEventListener('input', function() {
        const maksimalKarakter = 500;
        const sisaKarakter = maksimalKarakter - this.value.length;
        sisaKarakterSpan.textContent = sisaKarakter;

        // Ubah warna teks jika mendekati batas
        if (sisaKarakter <= 50) {
            sisaKarakterSpan.style.color = 'red';
        } else {
            sisaKarakterSpan.style.color = '';
        }
    });

    // Validasi form sebelum submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const wahanaSelect = document.getElementById('wahanaID');
        const ratingInputs = document.querySelectorAll('input[name="rating"]');

        // Validasi wahana
        if (wahanaSelect.value === '') {
            e.preventDefault();
            alert('Silakan pilih wahana terlebih dahulu');
            wahanaSelect.focus();
            return;
        }

        // Validasi rating
        let ratingDipilih = false;
        ratingInputs.forEach(input => {
            if (input.checked) {
                ratingDipilih = true;
            }
        });

        if (!ratingDipilih) {
            e.preventDefault();
            alert('Silakan pilih rating');
            ratingInputs[4].focus();
            return;
        }
    });

    // Efek hover pada bintang rating
    const starLabels = document.querySelectorAll('.star-rating label');
    starLabels.forEach(label => {
        label.addEventListener('mouseover', function() {
            // Reset semua bintang
            starLabels.forEach(l => l.style.color = '#ccc');
            
            // Warnai bintang yang di-hover dan sebelumnya
            let current = this;
            while (current) {
                current.style.color = '#ffca08';
                current = current.previousElementSibling?.previousElementSibling;
            }
        });

        label.addEventListener('mouseout', function() {
            // Kembalikan warna default jika tidak ada bintang yang dipilih
            const checkedStar = document.querySelector('.star-rating input:checked + label');
            if (!checkedStar) {
                starLabels.forEach(l => l.style.color = '#ccc');
            }
        });
    });
});
</script>
</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>