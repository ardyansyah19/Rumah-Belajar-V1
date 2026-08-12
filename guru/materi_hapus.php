<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $pdo->prepare('SELECT file_pdf FROM materi WHERE id = ?');
    $stmt->execute([$id]);
    $materi = $stmt->fetch();

    if ($materi) {
        if ($materi['file_pdf']) {
            $path = __DIR__ . '/../uploads/materi/' . $materi['file_pdf'];
            if (file_exists($path)) unlink($path);
        }
        $del = $pdo->prepare('DELETE FROM materi WHERE id = ?');
        $del->execute([$id]);
        set_flash('success', 'Materi berhasil dihapus.');
    }
}

redirect(base_url('guru/materi.php'));
