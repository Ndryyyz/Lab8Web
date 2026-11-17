<?php
// File koneksi database, dipanggil oleh semua halaman lain
$host = "localhost";
$user = "root";
$pass = "";
$db   = "latihan1";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke server gagal: " . mysqli_connect_error());
}
// Hapus tanda komentar di bawah ini kalau mau mengetes koneksi langsung dari browser
// echo "Koneksi berhasil";