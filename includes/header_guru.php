<?php
require_role('guru');
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
            <a href="<?= base_url('guru/dashboard.php') ?>" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a>
            <a href="<?= base_url('guru/materi.php') ?>" class="<?= in_array($current, ['materi.php','materi_tambah.php']) ? 'active' : '' ?>">📚 Materi</a>
            <a href="<?= base_url('guru/kuis.php') ?>" class="<?= in_array($current, ['kuis.php','kuis_tambah.php','kuis_soal.php','kuis_hasil.php','kuis_hasil_detail.php']) ? 'active' : '' ?>">📝 Kuis &amp; Koreksi</a>
            <a href="<?= base_url('guru/murid.php') ?>" class="<?= $current === 'murid.php' ? 'active' : '' ?>">🧑‍🎓 Data Murid</a>
        </nav>
        <div class="user-box">
            <div class="who">Masuk sebagai<b><?= h($_SESSION['nama_lengkap']) ?></b></div>
            <a href="<?= base_url('auth/logout.php') ?>" class="logout-link">Keluar</a>
        </div>
    </aside>
    <main class="main">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
