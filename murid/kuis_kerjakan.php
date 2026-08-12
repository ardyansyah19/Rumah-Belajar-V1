<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('murid');

$kuis_id = (int)($_GET['id'] ?? $_POST['kuis_id'] ?? 0);
$murid_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM kuis WHERE id = ? AND jenjang = ?');
$stmt->execute([$kuis_id, $_SESSION['jenjang']]);
$kuis = $stmt->fetch();

if (!$kuis) {
    set_flash('error', 'Kuis tidak ditemukan.');
    redirect(base_url('murid/kuis.php'));
}

// Cek kalau sudah pernah mengerjakan
$cek = $pdo->prepare('SELECT id FROM hasil_kuis WHERE kuis_id = ? AND murid_id = ?');
$cek->execute([$kuis_id, $murid_id]);
if ($sudah = $cek->fetch()) {
    redirect(base_url('murid/kuis_hasil.php?id=' . $kuis_id));
}

$soal_stmt = $pdo->prepare('SELECT * FROM soal WHERE kuis_id = ? ORDER BY id ASC');
$soal_stmt->execute([$kuis_id]);
$soal_list = $soal_stmt->fetchAll();

if (empty($soal_list)) {
    set_flash('error', 'Kuis ini belum memiliki soal.');
    redirect(base_url('murid/kuis.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jawaban_post = $_POST['jawaban'] ?? [];
    $jumlah_benar = 0;

    $pdo->beginTransaction();

    $ins_hasil = $pdo->prepare('INSERT INTO hasil_kuis (kuis_id, murid_id, skor, jumlah_benar, jumlah_soal) VALUES (?, ?, 0, 0, ?)');
    $ins_hasil->execute([$kuis_id, $murid_id, count($soal_list)]);
    $hasil_id = $pdo->lastInsertId();

    $ins_jawaban = $pdo->prepare('INSERT INTO jawaban_murid (hasil_id, soal_id, jawaban_dipilih, benar) VALUES (?, ?, ?, ?)');

    foreach ($soal_list as $s) {
        $dipilih = $jawaban_post[$s['id']] ?? null;
        $benar = ($dipilih === $s['jawaban_benar']) ? 1 : 0;
        if ($benar) $jumlah_benar++;
        $ins_jawaban->execute([$hasil_id, $s['id'], $dipilih ?: null, $benar]);
    }

    $skor = round(($jumlah_benar / count($soal_list)) * 100);
    $upd = $pdo->prepare('UPDATE hasil_kuis SET skor = ?, jumlah_benar = ? WHERE id = ?');
    $upd->execute([$skor, $jumlah_benar, $hasil_id]);

    $pdo->commit();

    redirect(base_url('murid/kuis_hasil.php?id=' . $kuis_id));
}

$page_title = $kuis['judul'];
include __DIR__ . '/../includes/header_murid.php';
?>
<div class="page-head">
    <div>
        <h1><?= h($kuis['judul']) ?></h1>
        <div class="desc"><?= count($soal_list) ?> soal • jawab semua lalu kirim, hasil langsung keluar</div>
    </div>
</div>

<form method="POST">
    <input type="hidden" name="kuis_id" value="<?= (int)$kuis_id ?>">
    <div class="card">
        <?php foreach ($soal_list as $i => $s): ?>
            <div class="soal-block">
                <span class="soal-nomor"><?= $i + 1 ?></span>
                <p class="soal-teks"><?= h($s['pertanyaan']) ?></p>
                <div class="opsi-list">
                    <?php foreach (['A' => $s['pilihan_a'], 'B' => $s['pilihan_b'], 'C' => $s['pilihan_c'], 'D' => $s['pilihan_d']] as $huruf => $teks): ?>
                        <label class="opsi">
                            <input type="radio" name="jawaban[<?= (int)$s['id'] ?>]" value="<?= $huruf ?>" required>
                            <span class="huruf"><?= $huruf ?></span> <?= h($teks) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary btn-block">Kumpulkan Jawaban</button>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
