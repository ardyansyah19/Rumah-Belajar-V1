<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(base_url($_SESSION['role'] . '/dashboard.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $password === '') {
        $error = 'Nama lengkap dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE nama_lengkap = ? LIMIT 1');
        $stmt->execute([$nama]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['jenjang'] = $user['jenjang'];
            redirect(base_url($user['role'] . '/dashboard.php'));
        } else {
            $error = 'Nama lengkap atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Les Privat Pintar</title>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="logo-badge">📘</div>
            <span class="name">Les Privat Pintar</span>
        </div>
        <h1 class="text-center">Selamat Datang! <span class="stabilo">👋</span></h1>
        <p class="auth-sub">Masuk dulu untuk lanjut belajar</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= h($_POST['nama_lengkap'] ?? '') ?>" placeholder="Tulis nama lengkapmu" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
        </form>

        <p class="auth-switch">Belum punya akun murid? <a href="<?= base_url('auth/register.php') ?>">Daftar di sini</a></p>
    </div>
</div>
</body>
</html>
