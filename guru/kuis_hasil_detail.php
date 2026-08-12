<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$hasil_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('
    SELECT hk.*, u.nama_lengkap, u.jenjang, k.judul AS judul_kuis, k.id AS kuis_id
    FROM hasil_kuis hk
    JOIN users u ON u.id = hk.murid_id
    JOIN kuis k ON k.id = hk.kuis_id
    WHERE hk.id = ?
');
$stmt->execute([$hasil_id]);
$hasil = $stmt->fetch();

if (!$hasil) {
    set_flash('error', 'Data hasil tidak ditemukan.');
    redirect(base_url('guru/kuis.php'));
}

$jawaban_stmt = $pdo->prepare('
    SELECT jm.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, s.jawaban_benar
    FROM jawaban_murid jm
    JOIN soal s ON s.id = jm.soal_id
    WHERE jm.hasil_id = ?
    ORDER BY jm.id ASC
');
$jawaban_stmt->execute([$hasil_id]);
$jawaban_list = $jawaban_stmt->fetchAll();

$page_title = 'Koreksi Jawaban';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Koreksi Jawaban <span class="stabilo"><?= h($hasil['nama_lengkap']) ?></span></h1>
        <div class="desc"><?= h($hasil['judul_kuis']) ?> • dikerjakan <?= format_tanggal($hasil['tanggal_pengerjaan']) ?></div>
    </div>
    <a href="<?= base_url('guru/kuis_hasil.php?id=' . $hasil['kuis_id']) ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="skor-hero">
    <div class="angka"><?= (int)$hasil['skor'] ?></div>
    <div class="ket">Benar <?= (int)$hasil['jumlah_benar'] ?> dari <?= (int)$hasil['jumlah_soal'] ?> soal</div>
</div>

<div class="card">
    <?php foreach ($jawaban_list as $i => $j): ?>
        <div class="soal-block">
            <span class="soal-nomor"><?= $i + 1 ?></span>
            <p class="soal-teks"><?= h($j['pertanyaan']) ?></p>
            <div class="opsi-list">
                <?php foreach (['A' => $j['pilihan_a'], 'B' => $j['pilihan_b'], 'C' => $j['pilihan_c'], 'D' => $j['pilihan_d']] as $huruf => $teks):
                    $kelas = '';
                    if ($huruf === $j['jawaban_benar']) $kelas = 'benar';
                    if ($huruf === $j['jawaban_dipilih'] && !$j['benar']) $kelas = 'salah';
                    $dipilih = $huruf === $j['jawaban_dipilih'] ? 'jawaban-dipilih' : '';
                ?>
                    <div class="opsi <?= $kelas ?> <?= $dipilih ?>">
                        <span class="huruf"><?= $huruf ?></span> <?= h($teks) ?>
                        <?php if ($huruf === $j['jawaban_dipilih']): ?>
                            <span class="badge <?= $j['benar'] ? 'badge-done' : 'badge-pending' ?>" style="margin-left:auto;">
                                <?= $j['benar'] ? '✓ Jawaban Murid — Benar' : '✕ Jawaban Murid — Salah' ?>
                            </span>
                        <?php elseif ($huruf === $j['jawaban_benar']): ?>
                            <span class="badge badge-done" style="margin-left:auto;">Kunci Jawaban</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (!$j['jawaban_dipilih']): ?>
                    <div class="form-hint">Murid tidak menjawab soal ini.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
