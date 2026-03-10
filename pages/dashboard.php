<?php
require '../config/database.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../index.php"); exit();
}

$user_id      = $_SESSION['user_id'];
$buku_count   = $conn->query("SELECT COUNT(*) as t FROM buku")->fetch_assoc()['t'];
$total_pinjam = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=$user_id")->fetch_assoc()['t'];
$aktif_count  = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=$user_id AND status IN ('pending','approved')")->fetch_assoc()['t'];
$latest_books = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC LIMIT 6");
?>

<!-- Header Row -->
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
  <div>
    <div class="sec-title" style="margin-bottom:4px;">Dashboard</div>
    <p style="color:var(--muted);font-size:.85rem;margin:0;">Selamat datang, <strong style="color:var(--primary);"><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>!</p>
  </div>
  <a href="cari_buku.php" class="btn-main"><i class="bi bi-search"></i> Cari Buku</a>
</div>

<!-- Stat Cards -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px;">
  <div class="stat-card">
    <div>
      <div class="stat-num"><?php echo $buku_count; ?></div>
      <div class="stat-lbl">Total Koleksi Buku</div>
    </div>
    <i class="bi bi-book stat-ico"></i>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-num"><?php echo $total_pinjam; ?></div>
      <div class="stat-lbl">Total Pinjaman Saya</div>
    </div>
    <i class="bi bi-journal-bookmark stat-ico"></i>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-num"><?php echo $aktif_count; ?></div>
      <div class="stat-lbl">Pinjaman Aktif</div>
    </div>
    <i class="bi bi-clock stat-ico"></i>
  </div>
</div>

<!-- Buku Terbaru -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
  <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--primary);margin:0;">Buku Terbaru</h2>
  <a href="cari_buku.php" class="btn-outline" style="font-size:.8rem;padding:5px 12px;">Lihat Semua →</a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:32px;">
  <?php
  $has_books = false;
  while ($book = $latest_books->fetch_assoc()):
    $has_books = true;
  ?>
  <div class="card" style="cursor:pointer;" onclick="location.href='detail_buku.php?id=<?php echo $book['id_buku']; ?>'">
    <div class="book-cover">
      <?php if (!empty($book['gambar_buku']) && file_exists('../uploads/books/'.$book['gambar_buku'])): ?>
        <img src="../uploads/books/<?php echo htmlspecialchars($book['gambar_buku']); ?>" alt="">
      <?php else: ?>
        <div class="book-cover-inner">
          <i class="bi bi-book"></i>
          <span><?php echo htmlspecialchars(substr($book['pengarang'],0,20)); ?></span>
        </div>
      <?php endif; ?>
    </div>
    <div style="padding:12px;">
      <div style="font-weight:600;font-size:.88rem;color:var(--primary);margin-bottom:3px;line-height:1.3;"><?php echo htmlspecialchars($book['judul']); ?></div>
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:8px;"><?php echo htmlspecialchars($book['pengarang']); ?></div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span class="<?php echo $book['jumlah_buku']>0?'badge-available':'badge-borrowed'; ?>">Stok: <?php echo $book['jumlah_buku']; ?></span>
        <a href="detail_buku.php?id=<?php echo $book['id_buku']; ?>" class="btn-main" style="font-size:.75rem;padding:4px 10px;">Detail</a>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
  <?php if (!$has_books): ?>
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">
    <i class="bi bi-book" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:10px;"></i>
    Belum ada buku dalam koleksi.
  </div>
  <?php endif; ?>
</div>

<!-- Quick Links -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div class="card" style="padding:20px;">
    <div style="font-weight:600;color:var(--primary);margin-bottom:6px;"><i class="bi bi-search" style="color:var(--accent);"></i> Cari Buku</div>
    <p style="font-size:.83rem;color:var(--muted);margin-bottom:14px;">Temukan buku berdasarkan judul, pengarang, atau penerbit.</p>
    <a href="cari_buku.php" class="btn-outline" style="font-size:.82rem;">Mulai Mencari →</a>
  </div>
  <div class="card" style="padding:20px;">
    <div style="font-weight:600;color:var(--primary);margin-bottom:6px;"><i class="bi bi-journal-text" style="color:var(--accent);"></i> Riwayat Peminjaman</div>
    <p style="font-size:.83rem;color:var(--muted);margin-bottom:14px;">Pantau status dan riwayat semua peminjaman buku Anda.</p>
    <a href="peminjamanku.php" class="btn-outline" style="font-size:.82rem;">Lihat Riwayat →</a>
  </div>
</div>

<?php require '../includes/footer.php'; ?>
