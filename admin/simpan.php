<?php
// Koneksi ke database BugisGo
$koneksi = new mysqli("localhost", "root", "", "BugisGo");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data dari form
$tanggal_kunjungan = $_POST['tanggal_kunjungan'];
$jumlah_tiket = $_POST['jumlah_tiket'];

// Simpan ke tabel pesanan
$sql = "INSERT INTO pesanan (tanggal_pesanan, jumlah_tiket) 
        VALUES ('$tanggal_kunjungan', '$jumlah_tiket')";

if ($koneksi->query($sql) === TRUE) {
    echo "<script>alert('Tiket berhasil dipesan!'); window.location.href='checkout.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $koneksi->error;
}

// Tutup koneksi
$koneksi->close();
?>
