<?php
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['nama_lengkap'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin - Perpustakaan'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-w: 230px; --primary: #2c3e50; }
        body { background: #f0f2f5; font-family: system-ui, -apple-system, sans-serif; }
        
        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--primary);
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 20px 20px 15px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand small { display: block; font-weight: 400; opacity: 0.6; font-size: 0.75rem; margin-top: 2px; }
        .sidebar-nav { padding: 10px 0; }
        .sidebar-nav .nav-label {
            padding: 8px 20px 4px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.15s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.12);
            color: white;
        }
        .sidebar-nav a i { font-size: 1rem; width: 18px; }

        /* Main */
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar h6 { margin: 0; font-weight: 600; color: #2c3e50; }
        .page-body { padding: 24px; }

        .card { border: none; box-shadow: 0 1px 5px rgba(0,0,0,0.07); border-radius: 10px; }
        .stat-card { border-radius: 10px; color: white; padding: 20px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: #1a252f; }
        .table th { font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { vertical-align: middle; font-size: 0.9rem; }
        .book-cover-sm {
            width: 45px; height: 60px; border-radius: 4px;
            object-fit: cover; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: white;
        }
        .cover-1{background:linear-gradient(135deg,#667eea,#764ba2);}
        .cover-2{background:linear-gradient(135deg,#f093fb,#f5576c);}
        .cover-3{background:linear-gradient(135deg,#4facfe,#00f2fe);}
        .cover-4{background:linear-gradient(135deg,#43e97b,#38f9d7);}
        .cover-5{background:linear-gradient(135deg,#fa709a,#fee140);}
        .cover-6{background:linear-gradient(135deg,#a18cd1,#fbc2eb);}
        .cover-7{background:linear-gradient(135deg,#fd7043,#ff8a65);}
        .cover-8{background:linear-gradient(135deg,#26c6da,#00acc1);}
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-book-half me-2"></i> Perpustakaan
        <small>Panel Admin</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <a href="/perpustakaan/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-label">Kelola</div>
        <a href="/perpustakaan/admin/buku.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'buku.php' ? 'active' : ''; ?>">
            <i class="bi bi-book"></i> Kelola Buku
        </a>
        <a href="/perpustakaan/admin/peminjaman.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'peminjaman.php' ? 'active' : ''; ?>">
            <i class="bi bi-journal-text"></i> Peminjaman
        </a>
        <a href="/perpustakaan/admin/pengembalian.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'pengembalian.php' ? 'active' : ''; ?>">
            <i class="bi bi-arrow-return-left"></i> Pengembalian
        </a>
        <a href="/perpustakaan/admin/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Data Siswa
        </a>

        <div class="nav-label">Akun</div>
        <a href="/perpustakaan/logout.php" class="text-danger-soft">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <h6><?php echo $page_title ?? 'Admin Panel'; ?></h6>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-circle"></i>
            <span style="font-size:0.9rem;"><?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </div>
    <div class="page-body">
