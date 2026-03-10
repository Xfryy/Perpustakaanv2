<?php
require '../config/database.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../index.php"); exit();
}
if (!isset($_GET['id'])) { header("Location: cari_buku.php"); exit(); }

$book_id = intval($_GET['id']);
$book    = $conn->query("SELECT * FROM buku WHERE id_buku=$book_id")->fetch_assoc();
if (!$book || $book['jumlah_buku'] <= 0) { header("Location: cari_buku.php"); exit(); }

$success_msg = '';
$error_msg   = '';
$user_id     = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_balik  = $_POST['tgl_balik']  ?? '';
    if (empty($tgl_pinjam) || empty($tgl_balik)) {
        $error_msg = 'Semua field harus diisi!';
    } elseif (strtotime($tgl_pinjam) >= strtotime($tgl_balik)) {
        $error_msg = 'Tanggal kembali harus setelah tanggal pinjam!';
    } elseif ((strtotime($tgl_balik)-strtotime($tgl_pinjam))/86400 > 14) {
        $error_msg = 'Maksimal peminjaman 14 hari!';
    } else {
        $judul = $conn->real_escape_string($book['judul']);
        $tp = $tgl_pinjam.' 08:00:00'; $tb = $tgl_balik.' 17:00:00';
        if ($conn->query("INSERT INTO peminjaman (id_user,id_buku,judul,tgl_pinjam,tgl_balik,status) VALUES ($user_id,$book_id,'$judul','$tp','$tb','pending')")) {
            $success_msg = 'Peminjaman berhasil diajukan!';
        } else {
            $error_msg = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>

<div style="margin-bottom:20px;">
  <a href="detail_buku.php?id=<?php echo $book_id; ?>" style="color:var(--muted);text-decoration:none;font-size:.83rem;">
    <i class="bi bi-arrow-left"></i> Kembali ke Detail Buku
  </a>
</div>

<div class="sec-title">Form Peminjaman</div>

<?php if ($success_msg): ?>
<div class="alert-ok" style="margin-bottom:16px;"><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?></div>
<div class="card" style="padding:24px;max-width:560px;">
  <h6 style="font-weight:600;color:var(--primary);margin-bottom:8px;">Langkah Selanjutnya</h6>
  <p style="font-size:.85rem;color:var(--muted);margin-bottom:16px;">Peminjaman sedang menunggu verifikasi admin. Setelah disetujui, ambil buku langsung di perpustakaan.</p>
  <div style="display:flex;gap:8px;">
    <a href="peminjamanku.php" class="btn-main"><i class="bi bi-journal-bookmark"></i> Lihat Status</a>
    <a href="dashboard.php" class="btn-outline">Dashboard</a>
  </div>
</div>
<?php else: ?>
<?php if ($error_msg): ?><div class="alert-err" style="margin-bottom:16px;"><i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:start;max-width:820px;">

  <!-- Buku Info -->
  <div class="card" style="overflow:hidden;">
    <div class="book-cover" style="height:200px;">
      <?php if (!empty($book['gambar_buku']) && file_exists('../uploads/books/'.$book['gambar_buku'])): ?>
        <img src="../uploads/books/<?php echo htmlspecialchars($book['gambar_buku']); ?>" alt="">
      <?php else: ?>
        <div class="book-cover-inner"><i class="bi bi-book" style="font-size:2.5rem;"></i></div>
      <?php endif; ?>
    </div>
    <div style="padding:12px;">
      <div style="font-size:.72rem;color:var(--muted);font-weight:600;letter-spacing:.5px;margin-bottom:4px;">BUKU DIPINJAM</div>
      <div style="font-weight:600;color:var(--primary);font-size:.88rem;margin-bottom:3px;"><?php echo htmlspecialchars($book['judul']); ?></div>
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:3px;"><?php echo htmlspecialchars($book['pengarang']); ?></div>
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:10px;"><?php echo htmlspecialchars($book['penerbit']); ?></div>
      <span class="badge-available">Stok: <?php echo $book['jumlah_buku']; ?></span>
    </div>
  </div>

  <!-- Form -->
  <div class="card" style="padding:24px;">
    <h5 style="font-weight:600;color:var(--primary);margin-bottom:20px;">Isi Tanggal Peminjaman</h5>
    <form method="POST">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div>
          <label class="form-label">Tanggal Pinjam <span style="color:#dc2626;">*</span></label>
          <input type="date" class="form-control" name="tgl_pinjam" id="tgl_pinjam"
                 required min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div>
          <label class="form-label">Tanggal Pengembalian <span style="color:#dc2626;">*</span></label>
          <input type="date" class="form-control" name="tgl_balik" id="tgl_balik"
                 required min="<?php echo date('Y-m-d',strtotime('+1 day')); ?>">
        </div>
      </div>
      <div class="alert-info2" style="margin-bottom:20px;font-size:.82rem;">
        <i class="bi bi-info-circle"></i> Maksimal peminjaman <strong>14 hari</strong>. Denda berlaku bila terlambat.
      </div>
      <div style="display:flex;gap:8px;">
        <button type="submit" class="btn-main"><i class="bi bi-check-circle"></i> Ajukan Peminjaman</button>
        <a href="detail_buku.php?id=<?php echo $book_id; ?>" class="btn-outline">Batal</a>
      </div>
    </form>
  </div>

</div>
<?php endif; ?>

<script>
document.getElementById('tgl_pinjam').addEventListener('change', function() {
    var d = new Date(this.value); d.setDate(d.getDate()+1);
    var min = d.toISOString().split('T')[0];
    var tb = document.getElementById('tgl_balik');
    tb.min = min;
    if (!tb.value || tb.value < min) tb.value = min;
});
</script>

<?php require '../includes/footer.php'; ?>
