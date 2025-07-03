<?php
$host = 'localhost';
$username = 'root';  // Sesuaikan dengan username database Anda
$password = '';      // Sesuaikan dengan password database Anda
$database = 'bugisgo'; // Sesuaikan dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set karakter UTF-8
$conn->set_charset("utf8");
?>