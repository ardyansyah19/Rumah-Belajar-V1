<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $kuis_id = (int)($_POST['kuis_id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM soal WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Soal berhasil dihapus.');
    redirect(base_url('guru/kuis_soal.php?id=' . $kuis_id));
}

redirect(base_url('guru/kuis.php'));
