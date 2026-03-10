<?php
require '../config/database.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['user_id'];
$filter  = $_GET['status'] ?? 'semua';

$where = "WHERE p.id_user=$user_id";
if ($filter !== 'semua') $where .= " AND p.status='".mysqli_real_escape_string($conn,$filter)."'";

$loans = $conn->query("SELECT p.*, b.pengarang, b.penerbit, b.gambar_buku FROM peminjaman p JOIN buku b ON p.id_buku=b.id_buku $where ORDER BY p.created_at DESC");

$badge = ['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected','returned'=>'badge-returned'];
$label = ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak','returned'=>'Selesai'];

// Hitung per status
$counts = [];
foreach (['semua','pending','approved','rejected','returned'] as $s) {
    $w = $s==='semua' ? "WHERE id_user=$user_id" : "WHERE id_user=$user_id AND status='$s'";
    $counts[$s] = $conn->query("SELECT COUNT(*) as t FROM peminjaman $w")->fetch_assoc()['t'];
}
$tabs = ['semua'=>'Semua','pending'=>'Menunggu','approved'=>'Aktif','rejected'=>'Ditolak','returned'=>'Selesai'];
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
  <div class="sec-title" style="margin-bottom:0;">Peminjamanku</div>
  <a href="cari_buku.php" class="btn-main" style="font-size:.82rem;"><i class="bi bi-plus"></i> Pinjam Buku</a>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
  <?php foreach($tabs as $k=>$v): ?>
  <a href="?status=<?php echo $k; ?>" class="filter-tab <?php echo $filter===$k?'active':''; ?>">
    <?php echo $v; ?> <span style="opacity:.65;">(<?php echo $counts[$k]; ?>)</span>
  </a>
  <?php endforeach; ?>
</div>

<!-- List -->
<?php if ($loans && $loans->num_rows > 0): ?>
<div style="display:flex;flex-direction:column;gap:10px;">
  <?php while ($loan = $loans->fetch_assoc()):
    $hari    = round((strtotime($loan['tgl_balik'])-strtotime($loan['tgl_pinjam']))/86400);
    $overdue = ($loan['status']==='approved' && strtotime($loan['tgl_balik']) < time());
  ?>
  <div class="card" style="padding:16px;<?php echo $overdue?'border-left:3px solid #dc2626;':''; ?>">
    <div style="display:flex;gap:14px;align-items:flex-start;">

      <!-- Cover Mini -->
      <div style="width:54px;height:72px;flex-shrink:0;border-radius:5px;overflow:hidden;">
        <div class="book-cover" style="height:72px;border-radius:5px;">
          <?php if (!empty($loan['gambar_buku']) && file_exists('../uploads/books/'.$loan['gambar_buku'])): ?>
            <img src="../uploads/books/<?php echo htmlspecialchars($loan['gambar_buku']); ?>" alt="">
          <?php else: ?>
            <div class="book-cover-inner"><i class="bi bi-book" style="font-size:1.2rem;"></i></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Info -->
      <div style="flex:1;min-width:0;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
          <div>
            <div style="font-weight:600;color:var(--primary);font-size:.92rem;margin-bottom:2px;"><?php echo htmlspecialchars($loan['judul']); ?></div>
            <div style="font-size:.78rem;color:var(--muted);"><?php echo htmlspecialchars($loan['pengarang']); ?> · <?php echo htmlspecialchars($loan['penerbit']); ?></div>
          </div>
          <span class="<?php echo $badge[$loan['status']] ?? 'badge-pending'; ?>">
            <?php echo $label[$loan['status']] ?? $loan['status']; ?>
          </span>
        </div>

        <div style="display:flex;gap:24px;flex-wrap:wrap;">
          <div>
            <div style="font-size:.7rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">TGL. PINJAM</div>
            <div style="font-size:.83rem;font-weight:500;"><?php echo date('d M Y', strtotime($loan['tgl_pinjam'])); ?></div>
          </div>
          <div>
            <div style="font-size:.7rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">TGL. KEMBALI</div>
            <div style="font-size:.83rem;font-weight:500;<?php echo $overdue?'color:#dc2626;':''; ?>">
              <?php echo date('d M Y', strtotime($loan['tgl_balik'])); ?>
              <?php if ($overdue): ?> <i class="bi bi-exclamation-triangle"></i><?php endif; ?>
            </div>
          </div>
          <div>
            <div style="font-size:.7rem;color:var(--muted);font-weight:600;letter-spacing:.5px;">DURASI</div>
            <div style="font-size:.83rem;font-weight:500;"><?php echo $hari; ?> hari</div>
          </div>
        </div>

        <?php if (!empty($loan['catatan_admin'])): ?>
        <div style="margin-top:8px;font-size:.78rem;color:var(--muted);">
          <i class="bi bi-chat-left-text"></i> <em><?php echo htmlspecialchars($loan['catatan_admin']); ?></em>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<?php else: ?>
<div style="text-align:center;padding:50px;color:var(--muted);">
  <i class="bi bi-journal-bookmark" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px;"></i>
  <p style="margin-bottom:14px;">Tidak ada data peminjaman<?php echo $filter!=='semua'?' dengan status "'.$tabs[$filter].'"':''; ?>.</p>
  <a href="cari_buku.php" class="btn-main"><i class="bi bi-search"></i> Cari Buku untuk Dipinjam</a>
</div>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>
