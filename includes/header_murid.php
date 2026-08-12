<?php
require_role('murid');
$flash = get_flash();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? h($page_title) . ' — ' : '' ?>Les Privat Pintar</title>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-badge">📘</div>
            <span>Les Privat Pintar</span>
        </div>
        <nav>
            <a href="<?= base_url('murid/dashboard.php') ?>" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a>
            <a href="<?= base_url('murid/materi.php') ?>" class="<?= in_array($current, ['materi.php','materi_detail.php']) ? 'active' : '' ?>">📚 Materi Belajar</a>
            <a href="<?= base_url('murid/kuis.php') ?>" class="<?= in_array($current, ['kuis.php','kuis_kerjakan.php','kuis_hasil.php']) ? 'active' : '' ?>">📝 Kuis</a>
        </nav>
        <div class="user-box">
            <div class="who">Masuk sebagai<b><?= h($_SESSION['nama_lengkap']) ?> <span class="badge <?= jenjang_badge_class($_SESSION['jenjang']) ?>"><?= h($_SESSION['jenjang']) ?></span></b></div>
            <a href="<?= base_url('auth/logout.php') ?>" class="logout-link">Keluar</a>
        </div>
    </aside>
    <main class="main">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
