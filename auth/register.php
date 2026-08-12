<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(base_url($_SESSION['role'] . '/dashboard.php'));
}

$error = '';
$old_nama = '';
$old_jenjang = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $jenjang = $_POST['jenjang'] ?? '';
    $old_nama = $nama;
    $old_jenjang = $jenjang;

    if ($nama === '' || $password === '' || $jenjang === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!in_array($jenjang, ['SD', 'SMP', 'SMA'], true)) {
        $error = 'Jenjang tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = $pdo->prepare('SELECT id FROM users WHERE nama_lengkap = ?');
        $cek->execute([$nama]);
        if ($cek->fetch()) {
            $error = 'Nama tersebut sudah terdaftar, gunakan nama lain atau tambahkan inisial.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (nama_lengkap, password, role, jenjang) VALUES (?, ?, "murid", ?)');
            $stmt->execute([$nama, $hash, $jenjang]);
            set_flash('success', 'Pendaftaran berhasil! Silakan masuk dengan akunmu.');
            redirect(base_url('auth/login.php'));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Murid — Les Privat Pintar</title>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="logo-badge">📘</div>
            <span class="name">Les Privat Pintar</span>
        </div>
        <h1 class="text-center">Daftar <span class="stabilo">Murid</span></h1>
        <p class="auth-sub">Isi datanya, langsung bisa belajar</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= h($old_nama) ?>" placeholder="Tulis nama lengkapmu" required autofocus>
            </div>
            <div class="form-group">
                <label for="jenjang">Jenjang Sekolah</label>
                <select id="jenjang" name="jenjang" required>
                    <option value="">-- Pilih Jenjang --</option>
                    <option value="SD" <?= $old_jenjang === 'SD' ? 'selected' : '' ?>>SD</option>
                    <option value="SMP" <?= $old_jenjang === 'SMP' ? 'selected' : '' ?>>SMP</option>
                    <option value="SMA" <?= $old_jenjang === 'SMA' ? 'selected' : '' ?>>SMA</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
        </form>

        <p class="auth-switch">Sudah punya akun? <a href="<?= base_url('auth/login.php') ?>">Masuk di sini</a></p>
    </div>
</div>
</body>
</html>
