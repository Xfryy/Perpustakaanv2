<?php
require '../config/database.php';
require '../includes/admin_header.php';

if (!isset($_GET['id'])) { header("Location: buku.php"); exit(); }
$id = intval($_GET['id']);
$book = $conn->query("SELECT * FROM buku WHERE id_buku=$id")->fetch_assoc();
if (!$book) { header("Location: buku.php"); exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $jumlah = intval($_POST['jumlah_buku'] ?? 1);
    $rak = $_POST['rak_buku'] ?? 'rak_1';
    $status = $_POST['status'] ?? 'tidak_dipinjam';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if (empty($judul)||empty($pengarang)||empty($penerbit)||empty($deskripsi)) {
        $error = 'Field wajib tidak boleh kosong!';
    } else {
        $gambar = $book['gambar_buku'];

        // Hapus gambar
        if (isset($_POST['hapus_gambar']) && !empty($gambar)) {
            @unlink('../uploads/books/'.$gambar);
            $gambar = '';
        }

        // Upload gambar baru
        if (!empty($_FILES['gambar_buku']['name'])) {
            $ext = strtolower(pathinfo($_FILES['gambar_buku']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                if (!empty($gambar)) @unlink('../uploads/books/'.$gambar);
                $gambar = uniqid('book_').'.'.$ext;
                move_uploaded_file($_FILES['gambar_buku']['tmp_name'], '../uploads/books/'.$gambar);
            } else { $error = 'Format gambar tidak valid!'; }
        }

        if (!$error) {
            $j=$conn->real_escape_string($judul); $p=$conn->real_escape_string($pengarang);
            $pb=$conn->real_escape_string($penerbit); $d=$conn->real_escape_string($deskripsi);
            $g=$conn->real_escape_string($gambar);
            $conn->query("UPDATE buku SET judul='$j',pengarang='$p',penerbit='$pb',deskripsi='$d',gambar_buku='$g',jumlah_buku=$jumlah,rak_buku='$rak',status='$status',tanggal='$tanggal' WHERE id_buku=$id");
            header("Location: buku.php?msg=edit"); exit();
        }
    }
}

$page_title = 'Edit Buku - Admin';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Edit Buku</h2>
    <a href="buku.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
<div class="card-body p-4">
<form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="judul" required value="<?php echo htmlspecialchars($book['judul']); ?>">
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="pengarang" required value="<?php echo htmlspecialchars($book['pengarang']); ?>">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="penerbit" required value="<?php echo htmlspecialchars($book['penerbit']); ?>">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Jumlah Stok</label>
                    <input type="number" class="form-control" name="jumlah_buku" min="0" value="<?php echo $book['jumlah_buku']; ?>">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Lokasi Rak</label>
                    <select class="form-select" name="rak_buku">
                        <?php foreach(['rak_1'=>'Rak 1','rak_2'=>'Rak 2','rak_3'=>'Rak 3'] as $k=>$v): ?>
                        <option value="<?php echo $k; ?>" <?php echo $book['rak_buku']===$k?'selected':''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="tidak_dipinjam" <?php echo $book['status']==='tidak_dipinjam'?'selected':''; ?>>Tersedia</option>
                        <option value="di_pinjam" <?php echo $book['status']==='di_pinjam'?'selected':''; ?>>Dipinjam</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Tanggal Input</label>
                    <input type="date" class="form-control" name="tanggal" value="<?php echo $book['tanggal']; ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" name="deskripsi" rows="5" required><?php echo htmlspecialchars($book['deskripsi']); ?></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Gambar Cover</label>
            <div class="book-cover rounded mb-2" style="height:200px;" id="cover-preview">
                <?php if (!empty($book['gambar_buku']) && file_exists('../uploads/books/'.$book['gambar_buku'])): ?>
                    <img src="../uploads/books/<?php echo htmlspecialchars($book['gambar_buku']); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
                <?php else: ?>
                    <div class="book-cover-placeholder"><i class="bi bi-image" style="font-size:3rem;"></i><span>Belum ada gambar</span></div>
                <?php endif; ?>
            </div>
            <input type="file" class="form-control mb-2" name="gambar_buku" id="gambar_input" accept="image/*">
            <?php if (!empty($book['gambar_buku'])): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="hapus_gambar" id="hapus_gambar">
                <label class="form-check-label small text-danger" for="hapus_gambar">Hapus gambar saat ini</label>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <hr class="mt-4">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
        <a href="buku.php" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
</div>
</div>
<script>
document.getElementById('gambar_input').addEventListener('change', function() {
    if (this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cover-preview').innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">';
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
<?php require '../includes/admin_footer.php'; ?>
