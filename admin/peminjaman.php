<?php
require '../config/database.php';
require '../includes/admin_header.php';

// Handle aksi (approve/reject/returned)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = intval($_POST['id_peminjaman']);
    $aksi = $_POST['aksi'] ?? '';
    $catatan = $conn->real_escape_string(trim($_POST['catatan_admin'] ?? ''));

    if (in_array($aksi, ['approved','rejected','returned'])) {
        $conn->query("UPDATE peminjaman SET status='$aksi', catatan_admin='$catatan' WHERE id_peminjaman=$pid");
        
        // Update stok buku
        if ($aksi === 'approved') {
            $buku_id = $conn->query("SELECT id_buku FROM peminjaman WHERE id_peminjaman=$pid")->fetch_assoc()['id_buku'] ?? 0;
            if ($buku_id) $conn->query("UPDATE buku SET jumlah_buku = jumlah_buku - 1, status='di_pinjam' WHERE id_buku=$buku_id AND jumlah_buku > 0");
        } elseif ($aksi === 'returned') {
            $buku_id = $conn->query("SELECT id_buku FROM peminjaman WHERE id_peminjaman=$pid")->fetch_assoc()['id_buku'] ?? 0;
            if ($buku_id) $conn->query("UPDATE buku SET jumlah_buku = jumlah_buku + 1, status='tidak_dipinjam' WHERE id_buku=$buku_id");
        }
        header("Location: peminjaman.php?msg=ok"); exit();
    }
}

// Filter
$filter = $_GET['status'] ?? 'semua';
$where = "WHERE 1=1";
if ($filter !== 'semua') $where .= " AND p.status='".mysqli_real_escape_string($conn,$filter)."'";

$loans = $conn->query("SELECT p.*, u.nama_lengkap, u.email, b.pengarang, b.gambar_buku FROM peminjaman p JOIN users u ON p.id_user=u.id_user JOIN buku b ON p.id_buku=b.id_buku $where ORDER BY p.created_at DESC");

$badge=['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected','returned'=>'badge-returned'];
$label=['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak','returned'=>'Dikembalikan'];

// Modal detail
$detail = null;
if (isset($_GET['id'])) {
    $did = intval($_GET['id']);
    $detail = $conn->query("SELECT p.*, u.nama_lengkap, u.email, b.pengarang, b.penerbit, b.rak_buku, b.gambar_buku FROM peminjaman p JOIN users u ON p.id_user=u.id_user JOIN buku b ON p.id_buku=b.id_buku WHERE p.id_peminjaman=$did")->fetch_assoc();
}

$page_title = 'Kelola Peminjaman - Admin';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Kelola Peminjaman</h2>
</div>

<?php if (isset($_GET['msg'])): ?><div class="alert alert-success"><i class="bi bi-check-circle"></i> Status peminjaman berhasil diperbarui.</div><?php endif; ?>

<?php if ($detail): ?>
<!-- DETAIL PANEL -->
<div class="card mb-4" style="border-left:3px solid var(--accent);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between mb-3">
            <h5 class="fw-semibold">Detail Peminjaman #<?php echo $detail['id_peminjaman']; ?></h5>
            <a href="peminjaman.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i> Tutup</a>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <p class="mb-1 text-muted small">PEMINJAM</p>
                <strong><?php echo htmlspecialchars($detail['nama_lengkap']); ?></strong><br>
                <span class="text-muted small"><?php echo htmlspecialchars($detail['email']); ?></span>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">BUKU</p>
                <strong><?php echo htmlspecialchars($detail['judul']); ?></strong><br>
                <span class="text-muted small"><?php echo htmlspecialchars($detail['pengarang']); ?> &middot; <?php echo str_replace('_',' ',ucfirst($detail['rak_buku'])); ?></span>
            </div>
            <div class="col-md-3">
                <p class="mb-1 text-muted small">TGL. PINJAM</p>
                <strong><?php echo date('d M Y', strtotime($detail['tgl_pinjam'])); ?></strong>
            </div>
            <div class="col-md-3">
                <p class="mb-1 text-muted small">TGL. KEMBALI</p>
                <strong><?php echo date('d M Y', strtotime($detail['tgl_balik'])); ?></strong>
            </div>
            <div class="col-md-3">
                <p class="mb-1 text-muted small">DURASI</p>
                <strong><?php echo round((strtotime($detail['tgl_balik'])-strtotime($detail['tgl_pinjam']))/86400); ?> hari</strong>
            </div>
            <div class="col-md-3">
                <p class="mb-1 text-muted small">STATUS</p>
                <span class="<?php echo $badge[$detail['status']]??'badge-pending'; ?>"><?php echo $label[$detail['status']]??$detail['status']; ?></span>
            </div>
        </div>
        <?php if ($detail['status'] === 'pending'): ?>
        <form method="POST" class="d-flex gap-2 flex-wrap align-items-end">
            <input type="hidden" name="id_peminjaman" value="<?php echo $detail['id_peminjaman']; ?>">
            <div class="flex-grow-1">
                <label class="form-label small">Catatan (opsional)</label>
                <input type="text" class="form-control form-control-sm" name="catatan_admin" placeholder="Catatan untuk peminjam...">
            </div>
            <button name="aksi" value="approved" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Setujui</button>
            <button name="aksi" value="rejected" class="btn btn-sm btn-danger" onclick="return confirm('Tolak peminjaman ini?')"><i class="bi bi-x-lg"></i> Tolak</button>
        </form>
        <?php elseif ($detail['status'] === 'approved'): ?>
        <form method="POST">
            <input type="hidden" name="id_peminjaman" value="<?php echo $detail['id_peminjaman']; ?>">
            <input type="hidden" name="aksi" value="returned">
            <input type="hidden" name="catatan_admin" value="Buku telah dikembalikan.">
            <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Tandai buku sudah dikembalikan?')"><i class="bi bi-arrow-return-left"></i> Tandai Dikembalikan</button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-3 flex-wrap">
    <?php foreach(['semua'=>'Semua','pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak','returned'=>'Dikembalikan'] as $k=>$v): ?>
    <a href="peminjaman.php?status=<?php echo $k; ?>" class="btn btn-sm <?php echo $filter===$k?'btn-primary':'btn-outline-secondary'; ?>"><?php echo $v; ?></a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Peminjam</th><th>Judul Buku</th><th>Tgl. Pinjam</th><th>Tgl. Kembali</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if ($loans->num_rows > 0): while ($r = $loans->fetch_assoc()):
                $overdue = $r['status']==='approved' && strtotime($r['tgl_balik']) < time();
            ?>
            <tr style="<?php echo $overdue?'background:#fff5f5;':''; ?>">
                <td>
                    <strong class="small"><?php echo htmlspecialchars($r['nama_lengkap']); ?></strong><br>
                    <span class="text-muted" style="font-size:0.78rem;"><?php echo htmlspecialchars($r['email']); ?></span>
                </td>
                <td class="small"><?php echo htmlspecialchars($r['judul']); ?></td>
                <td class="small"><?php echo date('d M Y', strtotime($r['tgl_pinjam'])); ?></td>
                <td class="small">
                    <?php echo date('d M Y', strtotime($r['tgl_balik'])); ?>
                    <?php if ($overdue): ?><br><span class="text-danger" style="font-size:0.75rem;"><i class="bi bi-exclamation-triangle"></i> Terlambat</span><?php endif; ?>
                </td>
                <td><span class="<?php echo $badge[$r['status']]??'badge-pending'; ?>"><?php echo $label[$r['status']]??$r['status']; ?></span></td>
                <td>
                    <a href="peminjaman.php?id=<?php echo $r['id_peminjaman']; ?><?php echo $filter!=='semua'?'&status='.$filter:''; ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data peminjaman.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/admin_footer.php'; ?>
