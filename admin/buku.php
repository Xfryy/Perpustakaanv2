<?php
require '../config/database.php';
require '../includes/admin_header.php';

// Hapus buku
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $bk = $conn->query("SELECT gambar_buku FROM buku WHERE id_buku=$id")->fetch_assoc();
    if ($bk && !empty($bk['gambar_buku'])) @unlink('../uploads/books/'.$bk['gambar_buku']);
    $conn->query("DELETE FROM buku WHERE id_buku=$id");
    header("Location: buku.php?msg=hapus"); exit();
}

$search = trim($_GET['search'] ?? '');
$where = '';
if ($search) $where = "WHERE judul LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR pengarang LIKE '%".mysqli_real_escape_string($conn,$search)."%'";
$books = $conn->query("SELECT * FROM buku $where ORDER BY id_buku DESC");

$page_title = 'Kelola Buku - Admin';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Kelola Buku</h2>
    <a href="tambah_buku.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Buku</a>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success"><i class="bi bi-check-circle"></i>
    <?php echo $_GET['msg']==='hapus'?'Buku berhasil dihapus.':($_GET['msg']==='tambah'?'Buku berhasil ditambahkan.':'Buku berhasil diperbarui.'); ?>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari judul atau pengarang..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
            <?php if ($search): ?><a href="buku.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th style="width:60px;">Cover</th><th>Judul</th><th>Pengarang</th><th>Penerbit</th><th>Rak</th><th>Stok</th><th>Status</th><th style="width:120px;">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($books->num_rows > 0): while ($b = $books->fetch_assoc()): ?>
                <tr>
                    <td>
                        <div style="width:45px;height:60px;" class="book-cover rounded">
                            <?php if (!empty($b['gambar_buku']) && file_exists('../uploads/books/'.$b['gambar_buku'])): ?>
                                <img src="../uploads/books/<?php echo htmlspecialchars($b['gambar_buku']); ?>" alt="">
                            <?php else: ?>
                                <div class="book-cover-placeholder h-100"><i class="bi bi-book" style="font-size:1rem;"></i></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><strong class="small"><?php echo htmlspecialchars($b['judul']); ?></strong></td>
                    <td class="small"><?php echo htmlspecialchars($b['pengarang']); ?></td>
                    <td class="small"><?php echo htmlspecialchars($b['penerbit']); ?></td>
                    <td class="small"><?php echo str_replace('_',' ',ucfirst($b['rak_buku'])); ?></td>
                    <td><span class="badge bg-secondary"><?php echo $b['jumlah_buku']; ?></span></td>
                    <td><?php echo $b['status']==='di_pinjam'?'<span class="badge-borrowed">Dipinjam</span>':'<span class="badge-available">Tersedia</span>'; ?></td>
                    <td>
                        <a href="edit_buku.php?id=<?php echo $b['id_buku']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="buku.php?hapus=<?php echo $b['id_buku']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus buku ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada buku ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/admin_footer.php'; ?>
