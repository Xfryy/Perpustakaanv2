<?php
require '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); exit();
}

$msg = '';

// Proses pengembalian
if (isset($_GET['return'])) {
    $pid = intval($_GET['return']);
    $p = $conn->query("SELECT id_buku FROM peminjaman WHERE id_peminjaman=$pid AND status='approved'")->fetch_assoc();
    if ($p) {
        $today = date('Y-m-d');
        $conn->query("UPDATE peminjaman SET status='returned', tgl_kembali_aktual='$today' WHERE id_peminjaman=$pid");
        // Tambah stok
        $conn->query("UPDATE buku SET jumlah_buku = jumlah_buku + 1 WHERE id_buku={$p['id_buku']}");
        $msg = 'Buku berhasil ditandai sebagai dikembalikan.';
    }
}

$loans = $conn->query("
    SELECT p.*, b.judul, b.pengarang, u.nama_lengkap, u.email 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    JOIN users u ON p.id_user = u.id_user
    WHERE p.status = 'approved'
    ORDER BY p.tgl_balik ASC
");

$page_title = 'Pengembalian Buku';
require 'header_admin.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo $msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-arrow-return-left me-2"></i>Buku yang Sedang Dipinjam</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl. Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($loans->num_rows === 0): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada buku yang sedang dipinjam.</td></tr>
                    <?php endif; ?>
                    <?php while ($p = $loans->fetch_assoc()): 
                        $terlambat = strtotime($p['tgl_balik']) < time();
                        $hari_terlambat = $terlambat ? floor((time() - strtotime($p['tgl_balik'])) / 86400) : 0;
                    ?>
                    <tr class="<?php echo $terlambat ? 'table-danger' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($p['nama_lengkap']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($p['email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($p['judul']); ?></td>
                        <td><?php echo date('d M Y', strtotime($p['tgl_pinjam'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($p['tgl_balik'])); ?></td>
                        <td>
                            <?php if ($terlambat): ?>
                                <span class="badge bg-danger">Terlambat <?php echo $hari_terlambat; ?> hari</span>
                            <?php else: ?>
                                <?php $sisa = ceil((strtotime($p['tgl_balik']) - time()) / 86400); ?>
                                <span class="badge bg-<?php echo $sisa <= 2 ? 'warning' : 'success'; ?>">
                                    Sisa <?php echo $sisa; ?> hari
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="pengembalian.php?return=<?php echo $p['id_peminjaman']; ?>" class="btn btn-sm btn-primary py-0 px-2"
                               onclick="return confirm('Tandai buku ini sudah dikembalikan?')">
                                <i class="bi bi-check2-circle"></i> Kembalikan
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer_admin.php'; ?>
