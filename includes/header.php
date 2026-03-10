<?php
$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['role'] ?? null;
$user_name    = $_SESSION['nama_lengkap'] ?? null;
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title ?? 'Perpustakaan Sekolah'; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --primary:#1a2332;
  --accent:#c8a96e;
  --accent-light:#e8d5b0;
  --bg:#f4f2ee;
  --border:#e0dbd2;
  --text:#2d3748;
  --muted:#6b7280;
  --sidebar-w:220px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);font-size:14px;}

/* TOPBAR */
.topbar{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  background:var(--primary);
  border-bottom:2px solid var(--accent);
  height:54px;display:flex;align-items:center;
  padding:0 20px;justify-content:space-between;
}
.topbar-brand{
  font-family:'Playfair Display',serif;
  color:var(--accent);font-size:1.1rem;
  text-decoration:none;display:flex;align-items:center;gap:8px;
}
.topbar-right{display:flex;align-items:center;gap:12px;}
.topbar-user{color:rgba(255,255,255,.65);font-size:.82rem;}
.btn-logout{
  background:transparent;border:1px solid rgba(255,255,255,.3);
  color:rgba(255,255,255,.8);border-radius:4px;
  padding:4px 12px;font-size:.8rem;cursor:pointer;text-decoration:none;
  transition:.2s;
}
.btn-logout:hover{background:rgba(255,255,255,.1);color:white;}

/* LAYOUT */
.layout{display:flex;padding-top:54px;min-height:100vh;}

/* SIDEBAR */
.sidebar{
  width:var(--sidebar-w);
  background:var(--primary);
  position:fixed;
  top:54px;left:0;bottom:0;
  overflow-y:auto;
  z-index:999;
  padding:16px 0;
  flex-shrink:0;
}
.sidebar-label{
  padding:16px 18px 6px;
  font-size:.68rem;font-weight:600;
  letter-spacing:1.5px;text-transform:uppercase;
  color:rgba(255,255,255,.3);
}
.sidebar a{
  display:flex;align-items:center;gap:10px;
  padding:9px 18px;
  color:rgba(255,255,255,.65);
  text-decoration:none;font-size:.85rem;
  border-left:3px solid transparent;
  transition:.15s;
}
.sidebar a:hover,.sidebar a.active{
  color:var(--accent);
  border-left-color:var(--accent);
  background:rgba(255,255,255,.05);
}
.sidebar a i{width:16px;font-size:.9rem;}

/* CONTENT */
.content{
  margin-left:var(--sidebar-w);
  flex:1;
  padding:28px;
  min-height:calc(100vh - 54px);
}

/* CARDS */
.card{
  background:#fff;border:1px solid var(--border);
  border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.06);
  transition:transform .18s,box-shadow .18s;
}
.card:hover{transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,0,0,.09);}

/* STAT CARD */
.stat-card{
  background:#fff;border:1px solid var(--border);
  border-left:3px solid var(--accent);
  border-radius:8px;padding:18px 20px;
  display:flex;align-items:center;justify-content:space-between;
}
.stat-num{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:var(--primary);line-height:1;}
.stat-lbl{color:var(--muted);font-size:.8rem;margin-top:3px;}
.stat-ico{font-size:1.8rem;color:var(--accent);opacity:.7;}

/* BOOK COVER */
.book-cover{
  height:190px;
  background:linear-gradient(135deg,var(--primary) 0%,#2e4160 100%);
  border-radius:8px 8px 0 0;
  display:flex;align-items:center;justify-content:center;
  overflow:hidden;position:relative;
}
.book-cover img{width:100%;height:100%;object-fit:cover;}
.book-cover-inner{
  display:flex;flex-direction:column;align-items:center;
  color:var(--accent);text-align:center;padding:10px;
}
.book-cover-inner i{font-size:3rem;opacity:.75;}
.book-cover-inner span{
  font-family:'Playfair Display',serif;
  font-size:.65rem;color:rgba(200,169,110,.5);
  margin-top:6px;letter-spacing:1px;text-transform:uppercase;
}

/* BADGES */
.badge-available{background:#d1fae5;color:#065f46;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}
.badge-borrowed{background:#fef3c7;color:#92400e;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}
.badge-pending{background:#fef3c7;color:#92400e;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}
.badge-approved{background:#d1fae5;color:#065f46;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}
.badge-rejected{background:#fee2e2;color:#991b1b;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}
.badge-returned{background:#e0f2fe;color:#075985;padding:3px 9px;border-radius:4px;font-size:.75rem;font-weight:600;}

/* BTN */
.btn-main{background:var(--primary);color:#fff;border:none;border-radius:5px;padding:7px 16px;font-size:.85rem;font-weight:500;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.15s;}
.btn-main:hover{background:#243347;color:#fff;}
.btn-outline{background:transparent;color:var(--primary);border:1px solid var(--primary);border-radius:5px;padding:6px 14px;font-size:.85rem;font-weight:500;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.15s;}
.btn-outline:hover{background:var(--primary);color:#fff;}

/* SECTION TITLE */
.sec-title{font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--primary);font-weight:700;padding-bottom:8px;border-bottom:2px solid var(--accent-light);margin-bottom:20px;}

/* FORM */
.form-control,.form-select{border:1px solid var(--border);border-radius:5px;font-size:.88rem;padding:8px 11px;width:100%;}
.form-control:focus,.form-select:focus{border-color:var(--accent);outline:none;box-shadow:0 0 0 2px rgba(200,169,110,.2);}
.form-label{font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:4px;display:block;}

/* TABLE */
.tbl{width:100%;border-collapse:collapse;font-size:.85rem;}
.tbl thead th{background:var(--primary);color:#fff;padding:11px 14px;text-align:left;font-size:.75rem;letter-spacing:.5px;text-transform:uppercase;font-weight:500;}
.tbl tbody td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
.tbl tbody tr:hover{background:#faf9f7;}
.tbl tbody tr:last-child td{border-bottom:none;}

/* ALERT */
.alert-ok{background:#d1fae5;color:#065f46;border:none;border-radius:6px;padding:10px 14px;font-size:.85rem;}
.alert-err{background:#fee2e2;color:#991b1b;border:none;border-radius:6px;padding:10px 14px;font-size:.85rem;}
.alert-info2{background:#e0f2fe;color:#075985;border:none;border-radius:6px;padding:10px 14px;font-size:.85rem;}
.alert-warn{background:#fef3c7;color:#92400e;border:none;border-radius:6px;padding:10px 14px;font-size:.85rem;}

/* FILTER TABS */
.filter-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;}
.filter-tab{padding:5px 14px;border-radius:20px;font-size:.8rem;font-weight:500;text-decoration:none;border:1px solid var(--border);color:var(--muted);transition:.15s;}
.filter-tab:hover{border-color:var(--primary);color:var(--primary);}
.filter-tab.active{background:var(--primary);color:#fff;border-color:var(--primary);}

/* FOOTER */
.page-footer{text-align:center;padding:24px;color:var(--muted);font-size:.8rem;border-top:1px solid var(--border);margin-top:40px;}

@media(max-width:768px){
  .sidebar{display:none;}
  .content{margin-left:0;padding:16px;}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <a href="dashboard.php" class="topbar-brand">
    <i class="bi bi-book-half"></i> Perpustakaan Sekolah
  </a>
  <div class="topbar-right">
    <span class="topbar-user d-none d-sm-inline"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_name ?? ''); ?></span>
    <a href="../logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<div class="layout">
<!-- SIDEBAR -->
<div class="sidebar">
  <div class="sidebar-label">Menu</div>
  <a href="dashboard.php" class="<?php echo $current_page==='dashboard.php'?'active':''; ?>">
    <i class="bi bi-grid-1x2"></i> Dashboard
  </a>
  <div class="sidebar-label">Buku</div>
  <a href="cari_buku.php" class="<?php echo in_array($current_page,['cari_buku.php','detail_buku.php'])?'active':''; ?>">
    <i class="bi bi-search"></i> Cari Buku
  </a>
  <div class="sidebar-label">Peminjaman</div>
  <a href="peminjamanku.php" class="<?php echo $current_page==='peminjamanku.php'&&!isset($_GET['status'])?'active':''; ?>">
    <i class="bi bi-journal-bookmark"></i> Semua Pinjaman
  </a>
  <a href="peminjamanku.php?status=pending" class="<?php echo ($current_page==='peminjamanku.php'&&($_GET['status']??'')==='pending')?'active':''; ?>">
    <i class="bi bi-clock"></i> Menunggu
  </a>
  <a href="peminjamanku.php?status=approved" class="<?php echo ($current_page==='peminjamanku.php'&&($_GET['status']??'')==='approved')?'active':''; ?>">
    <i class="bi bi-check-circle"></i> Aktif
  </a>
</div>

<!-- CONTENT -->
<div class="content">
