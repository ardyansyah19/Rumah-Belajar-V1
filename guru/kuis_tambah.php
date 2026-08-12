<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $jenjang = $_POST['jenjang'] ?? '';

    if ($judul === '' || !in_array($jenjang, ['SD', 'SMP', 'SMA'], true)) {
        $error = 'Judul dan jenjang wajib diisi.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO kuis (judul, deskripsi, jenjang, guru_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$judul, $deskripsi, $jenjang, $_SESSION['user_id']]);
        $kuis_id = $pdo->lastInsertId();
        set_flash('success', 'Kuis dibuat. Sekarang tambahkan soal-soalnya.');
        redirect(base_url('guru/kuis_soal.php?id=' . $kuis_id));
    }
}

$page_title = 'Buat Kuis';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Buat <span class="stabilo">Kuis Baru</span></h1>
        <div class="desc">Setelah ini Anda bisa langsung menambahkan soal pilihan ganda.</div>
    </div>
    <a href="<?= base_url('guru/kuis.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="card" style="max-width:600px;">
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="judul">Judul Kuis</label>
            <input type="text" id="judul" name="judul" value="<?= h($_POST['judul'] ?? '') ?>" placeholder="Contoh: Kuis Pecahan Bab 3" required>
        </div>
        <div class="form-group">
            <label for="jenjang">Jenjang</label>
            <select id="jenjang" name="jenjang" required>
                <option value="">-- Pilih Jenjang --</option>
                <option value="SD" <?= ($_POST['jenjang'] ?? '') === 'SD' ? 'selected' : '' ?>>SD</option>
                <option value="SMP" <?= ($_POST['jenjang'] ?? '') === 'SMP' ? 'selected' : '' ?>>SMP</option>
                <option value="SMA" <?= ($_POST['jenjang'] ?? '') === 'SMA' ? 'selected' : '' ?>>SMA</option>
            </select>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi Singkat (opsional)</label>
            <input type="text" id="deskripsi" name="deskripsi" value="<?= h($_POST['deskripsi'] ?? '') ?>" placeholder="Contoh: 10 soal, waktu bebas">
        </div>
        <button type="submit" class="btn btn-primary">Lanjut Tambah Soal →</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
