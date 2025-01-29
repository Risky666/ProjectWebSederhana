<?php
// Konfigurasi koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'simple_web';

// Membuat koneksi ke database menggunakan mysqli
$conn = new mysqli($host, $user, $password, $database);

// Periksa apakah koneksi berhasil
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}
?>
