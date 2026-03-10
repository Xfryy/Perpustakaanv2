<?php
require '../config/database.php';
require '../includes/admin_header.php';

// Hapus user
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM users WHERE id_user=$id AND role='siswa'");
    header("Location: users.php?msg=hapus"); exit();
}

$search = trim($_GET['search'] ?? '');
$where = "WHERE role='siswa'";
if ($search) $where .= " AND (nama_lengkap LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR email LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
$users = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM peminjaman WHERE id_user=u.id_user) as total_pinjam FROM users u $where ORDER BY u.created_at DESC");

$page_title = 'Data Pengguna - Admin';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Data Pengguna</h2>
</div>

<?php if (isset($_GET['msg'])): ?><div class="alert alert-success"><i class="bi bi-check-circle"></i> Pengguna berhasil dihapus.</div><?php endif; ?>

<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="users.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Total Peminjaman</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $no=1; if ($users->num_rows > 0): while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td class="text-muted small"><?php echo $no++; ?></td>
                <td><strong><?php echo htmlspecialchars($u['nama_lengkap']); ?></strong></td>
                <td class="small"><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge bg-secondary"><?php echo $u['total_pinjam']; ?> pinjaman</span></td>
                <td class="small"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                <td>
                    <a href="users.php?hapus=<?php echo $u['id_user']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengguna ini?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna ditemukan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/admin_footer.php'; ?>
