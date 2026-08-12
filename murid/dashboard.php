<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('murid');

$jenjang = $_SESSION['jenjang'];
$murid_id = $_SESSION['user_id'];

$jumlah_materi = $pdo->prepare("SELECT COUNT(*) FROM materi WHERE jenjang = ?");
$jumlah_materi->execute([$jenjang]);
$jumlah_materi = $jumlah_materi->fetchColumn();

$jumlah_kuis_total = $pdo->prepare("SELECT COUNT(*) FROM kuis WHERE jenjang = ?");
$jumlah_kuis_total->execute([$jenjang]);
$jumlah_kuis_total = $jumlah_kuis_total->fetchColumn();

$jumlah_dikerjakan = $pdo->prepare("SELECT COUNT(*) FROM hasil_kuis WHERE murid_id = ?");
$jumlah_dikerjakan->execute([$murid_id]);
$jumlah_dikerjakan = $jumlah_dikerjakan->fetchColumn();

$rata2 = $pdo->prepare("SELECT ROUND(AVG(skor)) FROM hasil_kuis WHERE murid_id = ?");
$rata2->execute([$murid_id]);
$rata2 = $rata2->fetchColumn() ?: 0;

$materi_terbaru = $pdo->prepare("SELECT * FROM materi WHERE jenjang = ? ORDER BY created_at DESC LIMIT 3");
$materi_terbaru->execute([$jenjang]);
$materi_terbaru = $materi_terbaru->fetchAll();

$kuis_tersedia = $pdo->prepare("
    SELECT k.*, (SELECT COUNT(*) FROM hasil_kuis hk WHERE hk.kuis_id = k.id AND hk.murid_id = ?) AS sudah
    FROM kuis k WHERE k.jenjang = ? ORDER BY k.created_at DESC LIMIT 4
");
$kuis_tersedia->execute([$murid_id, $jenjang]);
$kuis_tersedia = $kuis_tersedia->fetchAll();

$page_title = 'Dashboard';
include __DIR__ . '/../includes/header_murid.php';
?>
<div class="page-head">
    <div>
        <h1>Semangat belajar, <span class="stabilo"><?= h($_SESSION['nama_lengkap']) ?></span>! 🚀</h1>
        <div class="desc">Berikut materi dan kuis untuk jenjang <?= h($jenjang) ?>.</div>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="num"><?= $jumlah_materi ?></div><div class="label">Materi Tersedia</div></div>
    <div class="stat-card"><div class="num"><?= $jumlah_kuis_total ?></div><div class="label">Kuis Tersedia</div></div>
    <div class="stat-card"><div class="num"><?= $jumlah_dikerjakan ?></div><div class="label">Kuis Selesai</div></div>
    <div class="stat-card"><div class="num"><?= $rata2 ?></div><div class="label">Rata-rata Skor</div></div>
</div>

<div class="flex-between">
    <h3>📚 Materi Terbaru</h3>
    <a href="<?= base_url('murid/materi.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
</div>
<?php if (empty($materi_terbaru)): ?>
    <div class="empty"><span class="emoji">📚</span>Belum ada materi untuk jenjangmu.</div>
<?php else: ?>
    <div class="grid-cards mt-2" style="margin-bottom:2em;">
        <?php foreach ($materi_terbaru as $m): ?>
            <div class="item-card <?= strtolower($m['jenjang']) ?>">
                <div class="meta"><span class="badge <?= jenjang_badge_class($m['jenjang']) ?>"><?= h($m['jenjang']) ?></span><span><?= format_tanggal($m['created_at']) ?></span></div>
                <h3><?= h($m['judul']) ?></h3>
                <div class="excerpt"><?= h(potong_teks($m['konten'], 90)) ?></div>
                <a href="<?= base_url('murid/materi_detail.php?id=' . $m['id']) ?>" class="btn btn-outline btn-sm">Baca Materi</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="flex-between">
    <h3>📝 Kuis Tersedia</h3>
    <a href="<?= base_url('murid/kuis.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
</div>
<?php if (empty($kuis_tersedia)): ?>
    <div class="empty mt-2"><span class="emoji">📝</span>Belum ada kuis untuk jenjangmu.</div>
<?php else: ?>
    <div class="grid-cards mt-2">
        <?php foreach ($kuis_tersedia as $k): ?>
            <div class="item-card <?= strtolower($k['jenjang']) ?>">
                <div class="meta">
                    <span class="badge <?= jenjang_badge_class($k['jenjang']) ?>"><?= h($k['jenjang']) ?></span>
                    <span class="badge <?= $k['sudah'] ? 'badge-done' : 'badge-pending' ?>"><?= $k['sudah'] ? 'Selesai' : 'Belum Dikerjakan' ?></span>
                </div>
                <h3><?= h($k['judul']) ?></h3>
                <div class="excerpt"><?= h($k['deskripsi'] ?: 'Ayo uji pemahamanmu!') ?></div>
                <?php if ($k['sudah']): ?>
                    <a href="<?= base_url('murid/kuis_hasil.php?id=' . $k['id']) ?>" class="btn btn-outline btn-sm">Lihat Hasil</a>
                <?php else: ?>
                    <a href="<?= base_url('murid/kuis_kerjakan.php?id=' . $k['id']) ?>" class="btn btn-primary btn-sm">Kerjakan Sekarang</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
