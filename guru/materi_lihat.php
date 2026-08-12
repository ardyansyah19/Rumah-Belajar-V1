<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM materi WHERE id = ?');
$stmt->execute([$id]);
$materi = $stmt->fetch();

if (!$materi) {
    set_flash('error', 'Materi tidak ditemukan.');
    redirect(base_url('guru/materi.php'));
}

$page_title = $materi['judul'];
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1><?= h($materi['judul']) ?></h1>
        <div class="desc">
            <span class="badge <?= jenjang_badge_class($materi['jenjang']) ?>"><?= h($materi['jenjang']) ?></span>
            &nbsp;Diterbitkan <?= format_tanggal($materi['created_at']) ?>
        </div>
    </div>
    <a href="<?= base_url('guru/materi.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="card">
    <p style="white-space:pre-line;"><?= h($materi['konten']) ?></p>
    <?php if ($materi['file_pdf']): ?>
        <a href="<?= base_url('uploads/materi/' . $materi['file_pdf']) ?>" target="_blank" class="btn btn-mint mt-2">📎 Buka File PDF</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
