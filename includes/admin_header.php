<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
$user_name = $_SESSION['nama_lengkap'] ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin - Perpustakaan'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark admin-navbar">
    <div class="container-fluid px-3">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-book-half"></i> Perpustakaan — Admin
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white opacity-75 small"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_name); ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div style="padding-top: 56px; display: flex;">
    <div class="sidebar">
        <div class="sidebar-section">Menu Utama</div>
        <a href="dashboard.php" class="nav-link <?php echo $current_page==='dashboard.php'?'active':''; ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <div class="sidebar-section">Koleksi</div>
        <a href="buku.php" class="nav-link <?php echo ($current_page==='buku.php'||$current_page==='tambah_buku.php'||$current_page==='edit_buku.php')?'active':''; ?>">
            <i class="bi bi-book"></i> Kelola Buku
        </a>
        <div class="sidebar-section">Peminjaman</div>
        <a href="peminjaman.php" class="nav-link <?php echo $current_page==='peminjaman.php'?'active':''; ?>">
            <i class="bi bi-journal-bookmark"></i> Semua Peminjaman
        </a>
        <a href="peminjaman.php?status=pending" class="nav-link">
            <i class="bi bi-clock"></i> Menunggu Verifikasi
        </a>
        <div class="sidebar-section">Pengguna</div>
        <a href="users.php" class="nav-link <?php echo $current_page==='users.php'?'active':''; ?>">
            <i class="bi bi-people"></i> Data Pengguna
        </a>
    </div>
    <div class="admin-content w-100">
