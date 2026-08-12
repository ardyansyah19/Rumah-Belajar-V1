<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM kuis WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Kuis berhasil dihapus.');
}

redirect(base_url('guru/kuis.php'));
