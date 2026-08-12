<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('guru');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $konten = trim($_POST['konten'] ?? '');
    $jenjang = $_POST['jenjang'] ?? '';

    if ($judul === '' || $konten === '' || !in_array($jenjang, ['SD', 'SMP', 'SMA'], true)) {
        $error = 'Judul, konten, dan jenjang wajib diisi dengan benar.';
    } else {
        $nama_file = null;

        if (!empty($_FILES['file_pdf']['name'])) {
            $file = $_FILES['file_pdf'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Gagal mengunggah file PDF.';
            } elseif ($ext !== 'pdf') {
                $error = 'File yang diunggah harus berformat PDF.';
            } elseif ($file['size'] > 10 * 1024 * 1024) {
                $error = 'Ukuran file PDF maksimal 10MB.';
            } else {
                $nama_file = 'materi_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
                $tujuan = __DIR__ . '/../uploads/materi/' . $nama_file;
                if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
                    $error = 'Gagal menyimpan file PDF ke server.';
                    $nama_file = null;
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare('INSERT INTO materi (judul, konten, file_pdf, jenjang, guru_id) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$judul, $konten, $nama_file, $jenjang, $_SESSION['user_id']]);
            set_flash('success', 'Materi berhasil ditambahkan.');
            redirect(base_url('guru/materi.php'));
        }
    }
}

$page_title = 'Tambah Materi';
include __DIR__ . '/../includes/header_guru.php';
?>
<div class="page-head">
    <div>
        <h1>Tambah <span class="stabilo">Materi</span></h1>
        <div class="desc">Tulis materi dan lampirkan file PDF jika perlu.</div>
    </div>
    <a href="<?= base_url('guru/materi.php') ?>" class="btn btn-outline">← Kembali</a>
</div>

<div class="card" style="max-width:680px;">
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="judul">Judul Materi</label>
            <input type="text" id="judul" name="judul" value="<?= h($_POST['judul'] ?? '') ?>" placeholder="Contoh: Perkalian Bersusun" required>
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
            <label for="konten">Isi Materi</label>
            <textarea id="konten" name="konten" placeholder="Tulis penjelasan materi di sini..." required><?= h($_POST['konten'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="file_pdf">Lampiran PDF (opsional)</label>
            <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf">
            <div class="form-hint">Maksimal 10MB, format .pdf</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Materi</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
