<?php
require '../config/database.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../index.php"); exit();
}
if (!isset($_GET['id'])) { header("Location: cari_buku.php"); exit(); }

$book_id = intval($_GET['id']);
$book = $conn->query("SELECT * FROM buku WHERE id_buku=$book_id")->fetch_assoc();
if (!$book) { header("Location: cari_buku.php"); exit(); }

$user_id = $_SESSION['user_id'];
$aktif   = $conn->query("SELECT id_peminjaman FROM peminjaman WHERE id_buku=$book_id AND id_user=$user_id AND status IN ('pending','approved')")->num_rows;
?>

<!-- Breadcrumb -->
<div style="margin-bottom:20px;">
  <a href="cari_buku.php" style="color:var(--muted);text-decoration:none;font-size:.83rem;">
    <i class="bi bi-arrow-left"></i> Kembali ke Cari Buku
  </a>
</div>

<div style="display:grid;grid-template-columns:260px 1fr;gap:24px;align-items:start;">

  <!-- LEFT: Cover + Aksi -->
  <div>
    <div class="card" style="overflow:hidden;margin-bottom:12px;">
      <div class="book-cover" style="height:300px;">
        <?php if (!empty($book['gambar_buku']) && file_exists('../uploads/books/'.$book['gambar_buku'])): ?>
          <img src="../uploads/books/<?php echo htmlspecialchars($book['gambar_buku']); ?>" alt="">
        <?php else: ?>
          <div class="book-cover-inner">
            <i class="bi bi-book" style="font-size:4rem;"></i>
            <span style="font-size:.75rem;margin-top:10px;"><?php echo htmlspecialchars($book['pengarang']); ?></span>
          </div>
        <?php endif; ?>
      </div>
      <div style="padding:16px;text-align:center;">
        <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:4px;">STOK TERSEDIA</div>
        <div style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:700;color:var(--primary);line-height:1;"><?php echo $book['jumlah_buku']; ?></div>
        <div style="font-size:.8rem;color:var(--muted);margin-bottom:14px;">buku</div>
        <?php if ($aktif > 0): ?>
          <div class="alert-warn" style="font-size:.8rem;padding:8px 12px;">Anda sudah punya pinjaman aktif untuk buku ini.</div>
        <?php elseif ($book['jumlah_buku'] <= 0): ?>
          <div class="alert-err" style="font-size:.8rem;padding:8px 12px;">Stok habis, tidak bisa dipinjam.</div>
        <?php else: ?>
          <a href="pinjam_buku.php?id=<?php echo $book['id_buku']; ?>" class="btn-main" style="width:100%;justify-content:center;">
            <i class="bi bi-hand-thumbs-up"></i> Pinjam Buku Ini
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- RIGHT: Detail -->
  <div class="card" style="padding:28px;">
    <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--primary);margin-bottom:4px;"><?php echo htmlspecialchars($book['judul']); ?></h1>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:24px;"><?php echo htmlspecialchars($book['pengarang']); ?></p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
      <div>
        <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:3px;">PENERBIT</div>
        <div style="font-weight:500;"><?php echo htmlspecialchars($book['penerbit']); ?></div>
      </div>
      <div>
        <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:3px;">LOKASI RAK</div>
        <div style="font-weight:500;"><?php echo str_replace('_',' ',ucfirst($book['rak_buku'])); ?></div>
      </div>
      <div>
        <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:3px;">STATUS</div>
        <span class="<?php echo $book['status']==='di_pinjam'?'badge-borrowed':'badge-available'; ?>">
          <?php echo $book['status']==='di_pinjam'?'Sedang Dipinjam':'Tersedia'; ?>
        </span>
      </div>
      <div>
        <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:3px;">TANGGAL INPUT</div>
        <div style="font-weight:500;"><?php echo date('d M Y', strtotime($book['tanggal'])); ?></div>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid var(--border);margin-bottom:20px;">

    <div style="font-size:.72rem;color:var(--muted);letter-spacing:.5px;font-weight:600;margin-bottom:8px;">DESKRIPSI</div>
    <p style="line-height:1.75;color:var(--text);font-size:.88rem;"><?php echo nl2br(htmlspecialchars($book['deskripsi'])); ?></p>
  </div>

</div>

<?php require '../includes/footer.php'; ?>
