<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('murid');

$stmt = $pdo->prepare('SELECT * FROM materi WHERE jenjang = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['jenjang']]);
$materi_list = $stmt->fetchAll();

$page_title = 'Materi Belajar';
include __DIR__ . '/../includes/header_murid.php';
?>
<div class="page-head">
    <div>
        <h1>Materi <span class="stabilo">Belajar</span></h1>
        <div class="desc">Semua materi untuk jenjang <?= h($_SESSION['jenjang']) ?>.</div>
    </div>
</div>

<?php if (empty($materi_list)): ?>
    <div class="empty"><span class="emoji">📚</span>Belum ada materi untuk jenjangmu. Coba cek lagi nanti ya!</div>
<?php else: ?>
    <div class="grid-cards">
        <?php foreach ($materi_list as $m): ?>
            <div class="item-card <?= strtolower($m['jenjang']) ?>">
                <div class="meta">
                    <span class="badge <?= jenjang_badge_class($m['jenjang']) ?>"><?= h($m['jenjang']) ?></span>
                    <span><?= format_tanggal($m['created_at']) ?></span>
                    <?php if ($m['file_pdf']): ?><span>📎 PDF</span><?php endif; ?>
                </div>
                <h3><?= h($m['judul']) ?></h3>
                <div class="excerpt"><?= h(potong_teks($m['konten'], 110)) ?></div>
                <a href="<?= base_url('murid/materi_detail.php?id=' . $m['id']) ?>" class="btn btn-primary btn-sm">Baca Materi</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
