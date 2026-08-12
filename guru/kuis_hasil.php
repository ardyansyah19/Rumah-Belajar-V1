<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$kuis_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM kuis WHERE id = ?');
$stmt->execute([$kuis_id]);
$kuis = $stmt->fetch();

if (!$kuis) {
    set_flash('error', 'Kuis tidak ditemukan.');
    redirect(base_url('guru/kuis.php'));
}

$hasil_stmt = $pdo->prepare('
    SELECT hk.*, u.nama_lengkap
    FROM hasil_kuis hk
    JOIN users u ON u.id = hk.murid_id
    WHERE hk.kuis_id = ?
    ORDER BY hk.skor DESC, hk.tanggal_pengerjaan DESC
');
$hasil_stmt->execute([$kuis_id]);
$hasil_list = $hasil_stmt->fetchAll();

$rata2 = 0;
if (count($hasil_list) > 0) {
    $rata2 = round(array_sum(array_column($hasil_list, 'skor')) / count($hasil_list));
}

$page_title = 'Hasil Kuis';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1><?= h($kuis['judul']) ?> <span class="badge <?= jenjang_badge_class($kuis['jenjang']) ?>"><?= h($kuis['jenjang']) ?></span></h1>
        <div class="desc">Rekap koreksi otomatis — klik "Lihat Detail" untuk memeriksa jawaban per soal.</div>
    </div>
    <a href="<?= base_url('guru/kuis.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="num"><?= count($hasil_list) ?></div><div class="label">Murid Mengerjakan</div></div>
    <div class="stat-card"><div class="num"><?= $rata2 ?></div><div class="label">Rata-rata Skor</div></div>
</div>

<?php if (empty($hasil_list)): ?>
    <div class="empty"><span class="emoji">🗒️</span>Belum ada murid yang mengerjakan kuis ini.</div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Murid</th><th>Skor</th><th>Benar</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($hasil_list as $h): ?>
                <tr>
                    <td><?= h($h['nama_lengkap']) ?></td>
                    <td><b style="color:var(--indigo);"><?= (int)$h['skor'] ?></b></td>
                    <td class="muted"><?= (int)$h['jumlah_benar'] ?> / <?= (int)$h['jumlah_soal'] ?></td>
                    <td class="muted"><?= format_tanggal($h['tanggal_pengerjaan']) ?></td>
                    <td><a href="<?= base_url('guru/kuis_hasil_detail.php?id=' . $h['id']) ?>" class="btn btn-outline btn-sm">Lihat Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
