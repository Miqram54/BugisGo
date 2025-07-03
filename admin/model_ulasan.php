<?php
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
?>