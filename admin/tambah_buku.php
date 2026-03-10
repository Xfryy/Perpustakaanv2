<?php
require '../config/database.php';
require '../includes/admin_header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $jumlah = intval($_POST['jumlah_buku'] ?? 1);
    $rak = $_POST['rak_buku'] ?? 'rak_1';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if (empty($judul)||empty($pengarang)||empty($penerbit)||empty($deskripsi)) {
        $error = 'Field judul, pengarang, penerbit, dan deskripsi wajib diisi!';
    } else {
        $gambar = '';
        if (!empty($_FILES['gambar_buku']['name'])) {
            $ext = strtolower(pathinfo($_FILES['gambar_buku']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $gambar = uniqid('book_').'.'.$ext;
                move_uploaded_file($_FILES['gambar_buku']['tmp_name'], '../uploads/books/'.$gambar);
            } else { $error = 'Format gambar harus jpg, jpeg, png, atau webp!'; }
        }
        if (!$error) {
            $judul_s = $conn->real_escape_string($judul);
            $pengarang_s = $conn->real_escape_string($pengarang);
            $penerbit_s = $conn->real_escape_string($penerbit);
            $deskripsi_s = $conn->real_escape_string($deskripsi);
            $gambar_s = $conn->real_escape_string($gambar);
            $conn->query("INSERT INTO buku (judul,pengarang,penerbit,deskripsi,gambar_buku,jumlah_buku,rak_buku,tanggal) VALUES ('$judul_s','$pengarang_s','$penerbit_s','$deskripsi_s','$gambar_s',$jumlah,'$rak','$tanggal')");
            header("Location: buku.php?msg=tambah"); exit();
        }
    }
}

$page_title = 'Tambah Buku - Admin';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Tambah Buku</h2>
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
                <input type="text" class="form-control" name="judul" required value="<?php echo htmlspecialchars($_POST['judul']??''); ?>">
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="pengarang" required value="<?php echo htmlspecialchars($_POST['pengarang']??''); ?>">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="penerbit" required value="<?php echo htmlspecialchars($_POST['penerbit']??''); ?>">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Jumlah Stok</label>
                    <input type="number" class="form-control" name="jumlah_buku" min="1" value="<?php echo intval($_POST['jumlah_buku']??1); ?>">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Lokasi Rak</label>
                    <select class="form-select" name="rak_buku">
                        <option value="rak_1" <?php echo ($_POST['rak_buku']??'rak_1')==='rak_1'?'selected':''; ?>>Rak 1</option>
                        <option value="rak_2" <?php echo ($_POST['rak_buku']??'')==='rak_2'?'selected':''; ?>>Rak 2</option>
                        <option value="rak_3" <?php echo ($_POST['rak_buku']??'')==='rak_3'?'selected':''; ?>>Rak 3</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Tanggal Input</label>
                    <input type="date" class="form-control" name="tanggal" value="<?php echo $_POST['tanggal']??date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" name="deskripsi" rows="5" required><?php echo htmlspecialchars($_POST['deskripsi']??''); ?></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Gambar Cover</label>
            <div class="book-cover rounded mb-2" style="height:200px;" id="cover-preview">
                <div class="book-cover-placeholder"><i class="bi bi-image" style="font-size:3rem;"></i><span>Pilih gambar</span></div>
            </div>
            <input type="file" class="form-control" name="gambar_buku" id="gambar_input" accept="image/*">
            <p class="text-muted small mt-1">Format: JPG, PNG, WEBP. Maks. 2MB.</p>
        </div>
    </div>
    <hr class="mt-4">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Buku</button>
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
