<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('murid');

$kuis_id = (int)($_GET['id'] ?? 0);
$murid_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('
    SELECT hk.*, k.judul AS judul_kuis
    FROM hasil_kuis hk
    JOIN kuis k ON k.id = hk.kuis_id
    WHERE hk.kuis_id = ? AND hk.murid_id = ?
');
$stmt->execute([$kuis_id, $murid_id]);
$hasil = $stmt->fetch();

if (!$hasil) {
    set_flash('error', 'Kamu belum mengerjakan kuis ini.');
    redirect(base_url('murid/kuis.php'));
}

$jawaban_stmt = $pdo->prepare('
    SELECT jm.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, s.jawaban_benar
    FROM jawaban_murid jm
    JOIN soal s ON s.id = jm.soal_id
    WHERE jm.hasil_id = ?
    ORDER BY jm.id ASC
');
$jawaban_stmt->execute([$hasil['id']]);
$jawaban_list = $jawaban_stmt->fetchAll();

$pesan = $hasil['skor'] >= 80 ? 'Keren banget, kerja bagus! 🌟' : ($hasil['skor'] >= 60 ? 'Sudah bagus, terus berlatih ya! 💪' : 'Yuk pelajari lagi materinya, pasti bisa lebih baik! 📚');

$page_title = 'Hasil Kuis';
include __DIR__ . '/../includes/header_murid.php';
?>
<div class="page-head">
    <div>
        <h1><?= h($hasil['judul_kuis']) ?></h1>
        <div class="desc">Dikerjakan <?= format_tanggal($hasil['tanggal_pengerjaan']) ?></div>
    </div>
    <a href="<?= base_url('murid/kuis.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="skor-hero">
    <div class="angka"><?= (int)$hasil['skor'] ?></div>
    <div class="ket">Benar <?= (int)$hasil['jumlah_benar'] ?> dari <?= (int)$hasil['jumlah_soal'] ?> soal — <?= h($pesan) ?></div>
</div>

<div class="card">
    <h3>Pembahasan</h3>
    <?php foreach ($jawaban_list as $i => $j): ?>
        <div class="soal-block">
            <span class="soal-nomor"><?= $i + 1 ?></span>
            <p class="soal-teks"><?= h($j['pertanyaan']) ?></p>
            <div class="opsi-list">
                <?php foreach (['A' => $j['pilihan_a'], 'B' => $j['pilihan_b'], 'C' => $j['pilihan_c'], 'D' => $j['pilihan_d']] as $huruf => $teks):
                    $kelas = '';
                    if ($huruf === $j['jawaban_benar']) $kelas = 'benar';
                    if ($huruf === $j['jawaban_dipilih'] && !$j['benar']) $kelas = 'salah';
                ?>
                    <div class="opsi <?= $kelas ?>">
                        <span class="huruf"><?= $huruf ?></span> <?= h($teks) ?>
                        <?php if ($huruf === $j['jawaban_dipilih']): ?>
                            <span class="badge <?= $j['benar'] ? 'badge-done' : 'badge-pending' ?>" style="margin-left:auto;"><?= $j['benar'] ? '✓ Jawabanmu' : '✕ Jawabanmu' ?></span>
                        <?php elseif ($huruf === $j['jawaban_benar']): ?>
                            <span class="badge badge-done" style="margin-left:auto;">Jawaban Benar</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
