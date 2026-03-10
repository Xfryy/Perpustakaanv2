<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'perpustakaan';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
session_start();
?>
