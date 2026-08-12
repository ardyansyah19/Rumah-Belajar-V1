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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan = trim($_POST['pertanyaan'] ?? '');
    $a = trim($_POST['pilihan_a'] ?? '');
    $b = trim($_POST['pilihan_b'] ?? '');
    $c = trim($_POST['pilihan_c'] ?? '');
    $d = trim($_POST['pilihan_d'] ?? '');
    $jawaban = $_POST['jawaban_benar'] ?? '';

    if ($pertanyaan === '' || $a === '' || $b === '' || $c === '' || $d === '' || !in_array($jawaban, ['A','B','C','D'], true)) {
        $error = 'Semua kolom soal & jawaban benar wajib diisi.';
    } else {
        $ins = $pdo->prepare('INSERT INTO soal (kuis_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([$kuis_id, $pertanyaan, $a, $b, $c, $d, $jawaban]);
        set_flash('success', 'Soal berhasil ditambahkan.');
        redirect(base_url('guru/kuis_soal.php?id=' . $kuis_id));
    }
}

$soal_stmt = $pdo->prepare('SELECT * FROM soal WHERE kuis_id = ? ORDER BY id ASC');
$soal_stmt->execute([$kuis_id]);
$soal_list = $soal_stmt->fetchAll();

$page_title = 'Kelola Soal';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1><?= h($kuis['judul']) ?> <span class="badge <?= jenjang_badge_class($kuis['jenjang']) ?>"><?= h($kuis['jenjang']) ?></span></h1>
        <div class="desc"><?= count($soal_list) ?> soal tersimpan • otomatis dinilai sistem saat murid mengerjakan</div>
    </div>
    <a href="<?= base_url('guru/kuis.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="card" style="max-width:680px; margin-bottom:1.8em;">
    <h3>Tambah Soal</h3>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="pertanyaan">Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan" placeholder="Tulis pertanyaan..." required style="min-height:80px;"></textarea>
        </div>
        <div class="form-group"><label>Pilihan A</label><input type="text" name="pilihan_a" required></div>
        <div class="form-group"><label>Pilihan B</label><input type="text" name="pilihan_b" required></div>
        <div class="form-group"><label>Pilihan C</label><input type="text" name="pilihan_c" required></div>
        <div class="form-group"><label>Pilihan D</label><input type="text" name="pilihan_d" required></div>
        <div class="form-group">
            <label for="jawaban_benar">Jawaban Benar</label>
            <select id="jawaban_benar" name="jawaban_benar" required>
                <option value="">-- Pilih Jawaban Benar --</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">+ Tambah Soal</button>
    </form>
</div>

<h3>Daftar Soal</h3>
<?php if (empty($soal_list)): ?>
    <div class="empty"><span class="emoji">✏️</span>Belum ada soal. Tambahkan minimal 1 soal sebelum murid bisa mengerjakan.</div>
<?php else: ?>
    <?php foreach ($soal_list as $i => $s): ?>
        <div class="card soal-block" style="margin-bottom:1em;">
            <div class="flex-between" style="align-items:flex-start;">
                <div>
                    <span class="soal-nomor"><?= $i + 1 ?></span>
                    <p class="soal-teks"><?= h($s['pertanyaan']) ?></p>
                    <div class="opsi-list">
                        <?php foreach (['A' => $s['pilihan_a'], 'B' => $s['pilihan_b'], 'C' => $s['pilihan_c'], 'D' => $s['pilihan_d']] as $huruf => $teks): ?>
                            <div class="opsi <?= $huruf === $s['jawaban_benar'] ? 'benar' : '' ?>">
                                <span class="huruf"><?= $huruf ?></span> <?= h($teks) ?>
                                <?php if ($huruf === $s['jawaban_benar']): ?><span class="badge badge-done" style="margin-left:auto;">Kunci Jawaban</span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form method="POST" action="<?= base_url('guru/soal_hapus.php') ?>" onsubmit="return confirm('Hapus soal ini?');">
                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                    <input type="hidden" name="kuis_id" value="<?= (int)$kuis_id ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
