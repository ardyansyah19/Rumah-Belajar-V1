<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$filter = $_GET['jenjang'] ?? 'SEMUA';
$sql = "SELECT m.*, u.nama_lengkap FROM materi m JOIN users u ON u.id = m.guru_id";
$params = [];
if (in_array($filter, ['SD', 'SMP', 'SMA'], true)) {
    $sql .= " WHERE m.jenjang = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY m.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materi_list = $stmt->fetchAll();

$page_title = 'Materi';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Materi <span class="stabilo">Belajar</span></h1>
        <div class="desc">Kelola bahan bacaan dan file PDF untuk murid Anda.</div>
    </div>
    <a href="<?= base_url('guru/materi_tambah.php') ?>" class="btn btn-primary">+ Tambah Materi</a>
</div>

<div class="tabs">
    <a href="?jenjang=SEMUA" class="<?= $filter === 'SEMUA' ? 'active' : '' ?>">Semua</a>
    <a href="?jenjang=SD" class="<?= $filter === 'SD' ? 'active' : '' ?>">SD</a>
    <a href="?jenjang=SMP" class="<?= $filter === 'SMP' ? 'active' : '' ?>">SMP</a>
    <a href="?jenjang=SMA" class="<?= $filter === 'SMA' ? 'active' : '' ?>">SMA</a>
</div>

<?php if (empty($materi_list)): ?>
    <div class="empty"><span class="emoji">📚</span>Belum ada materi. Yuk tambahkan materi pertama!</div>
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
                <div class="actions">
                    <a href="<?= base_url('guru/materi_lihat.php?id=' . $m['id']) ?>" class="btn btn-outline btn-sm">Pratinjau</a>
                    <form method="POST" action="<?= base_url('guru/materi_hapus.php') ?>" onsubmit="return confirm('Hapus materi ini?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
