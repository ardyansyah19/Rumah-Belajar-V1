<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('murid');

$murid_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT k.*,
        (SELECT COUNT(*) FROM soal s WHERE s.kuis_id = k.id) AS jumlah_soal,
        hk.id AS hasil_id, hk.skor
    FROM kuis k
    LEFT JOIN hasil_kuis hk ON hk.kuis_id = k.id AND hk.murid_id = ?
    WHERE k.jenjang = ?
    ORDER BY k.created_at DESC
");
$stmt->execute([$murid_id, $_SESSION['jenjang']]);
$kuis_list = $stmt->fetchAll();

$page_title = 'Kuis';
include __DIR__ . '/../includes/header_murid.php';
?>
<div class="page-head">
    <div>
        <h1>Daftar <span class="stabilo">Kuis</span></h1>
        <div class="desc">Kerjakan kuis untuk mengasah pemahamanmu.</div>
    </div>
</div>

<?php if (empty($kuis_list)): ?>
    <div class="empty"><span class="emoji">📝</span>Belum ada kuis untuk jenjangmu.</div>
<?php else: ?>
    <div class="grid-cards">
        <?php foreach ($kuis_list as $k): ?>
            <div class="item-card <?= strtolower($k['jenjang']) ?>">
                <div class="meta">
                    <span class="badge <?= jenjang_badge_class($k['jenjang']) ?>"><?= h($k['jenjang']) ?></span>
                    <span><?= (int)$k['jumlah_soal'] ?> soal</span>
                    <span class="badge <?= $k['hasil_id'] ? 'badge-done' : 'badge-pending' ?>"><?= $k['hasil_id'] ? 'Selesai · Skor ' . (int)$k['skor'] : 'Belum Dikerjakan' ?></span>
                </div>
                <h3><?= h($k['judul']) ?></h3>
                <div class="excerpt"><?= h($k['deskripsi'] ?: 'Ayo uji pemahamanmu!') ?></div>
                <?php if ($k['jumlah_soal'] == 0): ?>
                    <span class="form-hint">Kuis belum memiliki soal.</span>
                <?php elseif ($k['hasil_id']): ?>
                    <a href="<?= base_url('murid/kuis_hasil.php?id=' . $k['id']) ?>" class="btn btn-outline btn-sm">Lihat Hasil</a>
                <?php else: ?>
                    <a href="<?= base_url('murid/kuis_kerjakan.php?id=' . $k['id']) ?>" class="btn btn-primary btn-sm">Kerjakan Sekarang</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
