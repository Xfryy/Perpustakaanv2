<?php
require '../config/database.php';
require '../includes/admin_header.php';

$total_buku = $conn->query("SELECT COUNT(*) as t FROM buku")->fetch_assoc()['t'];
$total_user = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='siswa'")->fetch_assoc()['t'];
$total_pinjam = $conn->query("SELECT COUNT(*) as t FROM peminjaman")->fetch_assoc()['t'];
$pending = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE status='pending'")->fetch_assoc()['t'];
$aktif = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE status='approved'")->fetch_assoc()['t'];

$recent_peminjaman = $conn->query("SELECT p.*, u.nama_lengkap, u.email FROM peminjaman p JOIN users u ON p.id_user=u.id_user ORDER BY p.created_at DESC LIMIT 8");

$page_title = 'Dashboard Admin - Perpustakaan';
?>
<h2 class="section-title">Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div><div class="stat-number"><?php echo $total_buku; ?></div><div class="stat-label">Total Buku</div></div>
            <i class="bi bi-book stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div><div class="stat-number"><?php echo $total_user; ?></div><div class="stat-label">Pengguna Terdaftar</div></div>
            <i class="bi bi-people stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div><div class="stat-number"><?php echo $pending; ?></div><div class="stat-label">Menunggu Verifikasi</div></div>
            <i class="bi bi-clock stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div><div class="stat-number"><?php echo $aktif; ?></div><div class="stat-label">Sedang Dipinjam</div></div>
            <i class="bi bi-journal-check stat-icon"></i>
        </div>
    </div>
</div>

<?php if ($pending > 0): ?>
<div class="alert alert-warning mb-4">
    <i class="bi bi-bell"></i> Ada <strong><?php echo $pending; ?> peminjaman</strong> yang menunggu verifikasi.
    <a href="peminjaman.php?status=pending" class="alert-link ms-2">Verifikasi sekarang →</a>
</div>
<?php endif; ?>

<h5 class="fw-semibold mb-3">Peminjaman Terbaru</h5>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Peminjam</th><th>Judul Buku</th><th>Tgl. Pinjam</th><th>Tgl. Kembali</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php $badge=['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected','returned'=>'badge-returned'];
                $label=['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak','returned'=>'Dikembalikan'];
                while($r=$recent_peminjaman->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['nama_lengkap']); ?></strong><br><span class="text-muted small"><?php echo htmlspecialchars($r['email']); ?></span></td>
                    <td><?php echo htmlspecialchars($r['judul']); ?></td>
                    <td class="small"><?php echo date('d M Y', strtotime($r['tgl_pinjam'])); ?></td>
                    <td class="small"><?php echo date('d M Y', strtotime($r['tgl_balik'])); ?></td>
                    <td><span class="<?php echo $badge[$r['status']]??'badge-pending'; ?>"><?php echo $label[$r['status']]??$r['status']; ?></span></td>
                    <td><a href="peminjaman.php?id=<?php echo $r['id_peminjaman']; ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><a href="peminjaman.php" class="btn btn-outline-primary btn-sm">Lihat Semua Peminjaman →</a></div>

<?php require '../includes/admin_footer.php'; ?>
