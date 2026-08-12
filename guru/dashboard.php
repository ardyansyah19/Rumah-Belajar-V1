<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$jumlah_murid = $pdo->query("SELECT COUNT(*) FROM users WHERE role='murid'")->fetchColumn();
$jumlah_materi = $pdo->query("SELECT COUNT(*) FROM materi")->fetchColumn();
$jumlah_kuis = $pdo->query("SELECT COUNT(*) FROM kuis")->fetchColumn();
$jumlah_pengerjaan = $pdo->query("SELECT COUNT(*) FROM hasil_kuis")->fetchColumn();

$hasil_terbaru = $pdo->query("
    SELECT hk.*, u.nama_lengkap, u.jenjang, k.judul AS judul_kuis
    FROM hasil_kuis hk
    JOIN users u ON u.id = hk.murid_id
    JOIN kuis k ON k.id = hk.kuis_id
    ORDER BY hk.tanggal_pengerjaan DESC
    LIMIT 6
")->fetchAll();

$page_title = 'Dashboard';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Halo, <span class="stabilo"><?= h($_SESSION['nama_lengkap']) ?></span> 👋</h1>
        <div class="desc">Ini ringkasan aktivitas belajar murid-murid Anda hari ini.</div>
    </div>
    <a href="<?= base_url('guru/kuis_tambah.php') ?>" class="btn btn-primary">+ Buat Kuis Baru</a>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="num"><?= $jumlah_murid ?></div><div class="label">Total Murid</div></div>
    <div class="stat-card"><div class="num"><?= $jumlah_materi ?></div><div class="label">Materi Diterbitkan</div></div>
    <div class="stat-card"><div class="num"><?= $jumlah_kuis ?></div><div class="label">Kuis Dibuat</div></div>
    <div class="stat-card"><div class="num"><?= $jumlah_pengerjaan ?></div><div class="label">Kuis Dikerjakan Murid</div></div>
</div>

<div class="card">
    <div class="flex-between mt-2" style="margin-top:0;">
        <h3>Hasil Kuis Terbaru</h3>
        <a href="<?= base_url('guru/kuis.php') ?>" class="btn btn-outline btn-sm">Lihat Semua Kuis</a>
    </div>
    <?php if (empty($hasil_terbaru)): ?>
        <div class="empty"><span class="emoji">🗒️</span>Belum ada murid yang mengerjakan kuis.</div>
    <?php else: ?>
        <div class="table-wrap mt-2">
            <table>
                <thead><tr><th>Murid</th><th>Jenjang</th><th>Kuis</th><th>Skor</th><th>Tanggal</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($hasil_terbaru as $h): ?>
                    <tr>
                        <td><?= h($h['nama_lengkap']) ?></td>
                        <td><span class="badge <?= jenjang_badge_class($h['jenjang']) ?>"><?= h($h['jenjang']) ?></span></td>
                        <td><?= h($h['judul_kuis']) ?></td>
                        <td><b><?= (int)$h['skor'] ?></b></td>
                        <td class="muted"><?= format_tanggal($h['tanggal_pengerjaan']) ?></td>
                        <td><a href="<?= base_url('guru/kuis_hasil_detail.php?id=' . $h['id']) ?>" class="btn btn-outline btn-sm">Koreksi</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
