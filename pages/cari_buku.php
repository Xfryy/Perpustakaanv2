<?php
require '../config/database.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../index.php"); exit();
}

$search = trim($_GET['search'] ?? '');
$rak    = $_GET['rak'] ?? '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (judul LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR pengarang LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR penerbit LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
if ($rak)    $where .= " AND rak_buku='".mysqli_real_escape_string($conn,$rak)."'";

$books = $conn->query("SELECT * FROM buku $where ORDER BY judul ASC");
$total = $books->num_rows;
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
  <div class="sec-title" style="margin-bottom:0;">Cari Buku</div>
  <span style="font-size:.82rem;color:var(--muted);"><?php echo $total; ?> buku ditemukan</span>
</div>

<!-- Search Box -->
<div class="card" style="padding:16px;margin-bottom:20px;">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
    <div style="flex:1;min-width:200px;">
      <label class="form-label">Kata Kunci</label>
      <input type="text" name="search" class="form-control" placeholder="Judul, pengarang, penerbit..." value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div style="width:150px;">
      <label class="form-label">Lokasi Rak</label>
      <select name="rak" class="form-select">
        <option value="">Semua Rak</option>
        <option value="rak_1" <?php echo $rak==='rak_1'?'selected':''; ?>>Rak 1</option>
        <option value="rak_2" <?php echo $rak==='rak_2'?'selected':''; ?>>Rak 2</option>
        <option value="rak_3" <?php echo $rak==='rak_3'?'selected':''; ?>>Rak 3</option>
      </select>
    </div>
    <div style="display:flex;gap:6px;">
      <button type="submit" class="btn-main"><i class="bi bi-search"></i> Cari</button>
      <?php if ($search||$rak): ?><a href="cari_buku.php" class="btn-outline"><i class="bi bi-x"></i> Reset</a><?php endif; ?>
    </div>
  </form>
</div>

<!-- Results -->
<?php if ($total > 0): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
  <?php while ($book = $books->fetch_assoc()): ?>
  <div class="card" style="cursor:pointer;" onclick="location.href='detail_buku.php?id=<?php echo $book['id_buku']; ?>'">
    <div class="book-cover">
      <?php if (!empty($book['gambar_buku']) && file_exists('../uploads/books/'.$book['gambar_buku'])): ?>
        <img src="../uploads/books/<?php echo htmlspecialchars($book['gambar_buku']); ?>" alt="">
      <?php else: ?>
        <div class="book-cover-inner">
          <i class="bi bi-book"></i>
          <span><?php echo htmlspecialchars(substr($book['pengarang'],0,18)); ?></span>
        </div>
      <?php endif; ?>
    </div>
    <div style="padding:12px;">
      <div style="font-weight:600;font-size:.88rem;color:var(--primary);margin-bottom:3px;line-height:1.3;"><?php echo htmlspecialchars($book['judul']); ?></div>
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:2px;"><i class="bi bi-person"></i> <?php echo htmlspecialchars($book['pengarang']); ?></div>
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:10px;"><i class="bi bi-building"></i> <?php echo htmlspecialchars($book['penerbit']); ?></div>
      <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:10px;">
        <span class="<?php echo $book['jumlah_buku']>0?'badge-available':'badge-borrowed'; ?>">Stok: <?php echo $book['jumlah_buku']; ?></span>
        <span style="background:#f1f0ed;color:#555;padding:3px 7px;border-radius:4px;font-size:.72rem;"><?php echo str_replace('_',' ',ucfirst($book['rak_buku'])); ?></span>
      </div>
      <a href="detail_buku.php?id=<?php echo $book['id_buku']; ?>" class="btn-main" style="font-size:.78rem;padding:5px 12px;width:100%;justify-content:center;">
        <i class="bi bi-eye"></i> Lihat Detail
      </a>
    </div>
  </div>
  <?php endwhile; ?>
</div>
<?php else: ?>
<div class="alert-warn" style="text-align:center;padding:30px;">
  <i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
  Tidak ada buku<?php echo $search?' untuk "<strong>'.htmlspecialchars($search).'</strong>"':''; ?> yang ditemukan.
</div>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>
