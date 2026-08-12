<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$murid_list = $pdo->query("
    SELECT u.*,
        (SELECT COUNT(*) FROM hasil_kuis hk WHERE hk.murid_id = u.id) AS jumlah_kuis,
        (SELECT ROUND(AVG(skor)) FROM hasil_kuis hk WHERE hk.murid_id = u.id) AS rata2
    FROM users u
    WHERE u.role = 'murid'
    ORDER BY u.created_at DESC
")->fetchAll();

$page_title = 'Data Murid';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Data <span class="stabilo">Murid</span></h1>
        <div class="desc">Semua murid yang sudah mendaftar di platform ini.</div>
    </div>
</div>

<?php if (empty($murid_list)): ?>
    <div class="empty"><span class="emoji">🧑‍🎓</span>Belum ada murid yang mendaftar.</div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama Lengkap</th><th>Jenjang</th><th>Kuis Dikerjakan</th><th>Rata-rata Skor</th><th>Bergabung</th></tr></thead>
            <tbody>
            <?php foreach ($murid_list as $m): ?>
                <tr>
                    <td><?= h($m['nama_lengkap']) ?></td>
                    <td><span class="badge <?= jenjang_badge_class($m['jenjang']) ?>"><?= h($m['jenjang']) ?></span></td>
                    <td class="muted"><?= (int)$m['jumlah_kuis'] ?></td>
                    <td class="muted"><?= $m['rata2'] !== null ? (int)$m['rata2'] : '-' ?></td>
                    <td class="muted"><?= format_tanggal($m['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
