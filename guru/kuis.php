<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$kuis_list = $pdo->query("
    SELECT k.*,
        (SELECT COUNT(*) FROM soal s WHERE s.kuis_id = k.id) AS jumlah_soal,
        (SELECT COUNT(*) FROM hasil_kuis hk WHERE hk.kuis_id = k.id) AS jumlah_pengerjaan
    FROM kuis k
    ORDER BY k.created_at DESC
")->fetchAll();

$page_title = 'Kuis';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Kuis &amp; <span class="stabilo">Koreksi</span></h1>
        <div class="desc">Buat soal pilihan ganda, sistem otomatis menilai, Anda tinggal mengoreksi hasilnya.</div>
    </div>
    <a href="<?= base_url('guru/kuis_tambah.php') ?>" class="btn btn-primary">+ Buat Kuis</a>
</div>

<?php if (empty($kuis_list)): ?>
    <div class="empty"><span class="emoji">📝</span>Belum ada kuis. Ayo buat kuis pertama untuk murid Anda!</div>
<?php else: ?>
    <div class="grid-cards">
        <?php foreach ($kuis_list as $k): ?>
            <div class="item-card <?= strtolower($k['jenjang']) ?>">
                <div class="meta">
                    <span class="badge <?= jenjang_badge_class($k['jenjang']) ?>"><?= h($k['jenjang']) ?></span>
                    <span><?= (int)$k['jumlah_soal'] ?> soal</span>
                    <span><?= (int)$k['jumlah_pengerjaan'] ?> murid mengerjakan</span>
                </div>
                <h3><?= h($k['judul']) ?></h3>
                <div class="excerpt"><?= h($k['deskripsi'] ?: 'Tidak ada deskripsi.') ?></div>
                <div class="actions">
                    <a href="<?= base_url('guru/kuis_soal.php?id=' . $k['id']) ?>" class="btn btn-outline btn-sm">Kelola Soal</a>
                    <a href="<?= base_url('guru/kuis_hasil.php?id=' . $k['id']) ?>" class="btn btn-mint btn-sm">Koreksi Hasil</a>
                    <form method="POST" action="<?= base_url('guru/kuis_hapus.php') ?>" onsubmit="return confirm('Hapus kuis ini beserta semua soal & hasilnya?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$k['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
