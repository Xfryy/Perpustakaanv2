<?php
require 'config/database.php';

$login_error = '';
$success_msg = '';
$errors = [];

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin/dashboard.php");
    else header("Location: pages/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if (empty($email) || empty($password)) {
        $login_error = 'Email dan password harus diisi!';
    } else {
        $stmt = $conn->prepare("SELECT id_user, role, nama_lengkap, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                if ($user['role'] === 'admin') header("Location: admin/dashboard.php");
                else header("Location: pages/dashboard.php");
                exit();
            } else { $login_error = 'Password salah!'; }
        } else { $login_error = 'Email tidak ditemukan!'; }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = trim($_POST['email_register']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password = trim($_POST['password_register']);
    $password_confirm = trim($_POST['password_confirm']);
    if (empty($email)||empty($nama_lengkap)||empty($password)||empty($password_confirm)) $errors[]='Semua field harus diisi!';
    if ($password !== $password_confirm) $errors[]='Password tidak cocok!';
    if (strlen($password) < 6) $errors[]='Password minimal 6 karakter!';
    $chk = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
    $chk->bind_param("s",$email); $chk->execute();
    if ($chk->get_result()->num_rows > 0) $errors[]='Email sudah terdaftar!';
    $chk->close();
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $ins = $conn->prepare("INSERT INTO users (role, email, nama_lengkap, password) VALUES ('siswa', ?, ?, ?)");
        $ins->bind_param("sss", $email, $nama_lengkap, $hashed);
        if ($ins->execute()) $success_msg = 'Registrasi berhasil! Silakan login.';
        else $errors[]='Terjadi kesalahan saat registrasi!';
        $ins->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background: var(--primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-wrap { background: var(--bg); border-radius: 8px; overflow: hidden; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .login-brand { background: var(--primary); padding: 32px 30px 28px; text-align: center; border-bottom: 2px solid var(--accent); }
        .login-brand h1 { font-family: system-ui, -apple-system, sans-serif; color: white; font-size: 1.8rem; margin-bottom: 4px; }
        .login-brand p { color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin: 0; }
        .login-body { padding: 28px 30px; }
        .nav-tabs { border-bottom: 2px solid var(--border); margin-bottom: 24px; }
        .nav-tabs .nav-link { color: var(--text) !important; border: none; padding: 8px 16px; font-weight: 500; font-size: 0.9rem; }
        .nav-tabs .nav-link.active { color: var(--primary) !important; border-bottom: 2px solid var(--accent); background: none; margin-bottom: -2px; }
        .btn-login { background: var(--primary); color: white; width: 100%; padding: 10px; font-weight: 500; border: none; border-radius: 5px; }
        .btn-login:hover { background: #1a252f; color: white; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-brand">
            <h1><i class="bi bi-book-half"></i> Perpustakaan</h1>
            <p>Sistem Perpustakaan Sekolah Digital</p>
        </div>
        <div class="login-body">
            <ul class="nav nav-tabs">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login"><i class="bi bi-box-arrow-in-right"></i> Login</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#register"><i class="bi bi-person-plus"></i> Daftar</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="login">
                    <?php if ($login_error): ?><div class="alert alert-danger mb-3"><i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($login_error); ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="email@example.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="" required>
                        </div>
                        <button type="submit" name="login" class="btn-login"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="register">
                    <?php if ($success_msg): ?><div class="alert alert-success mb-3"><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
                    <?php if (!empty($errors)): ?><div class="alert alert-danger mb-3"><?php foreach($errors as $e): ?><div><i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email_register" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password_register" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirm" required>
                        </div>
                        <button type="submit" name="register" class="btn-login"><i class="bi bi-person-plus"></i> Daftar Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
