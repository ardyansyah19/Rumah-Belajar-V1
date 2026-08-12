<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Redirect ke url tertentu lalu hentikan eksekusi */
function redirect($url) {
    header("Location: $url");
    exit;
}

/** Cek apakah user sudah login */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/** Wajibkan login, kalau tidak lempar ke halaman login */
function require_login() {
    if (!is_logged_in()) {
        redirect(base_url('auth/login.php'));
    }
}

/** Wajibkan role tertentu (guru/murid), kalau tidak sesuai lempar ke dashboard masing-masing */
function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        redirect(base_url($_SESSION['role'] . '/dashboard.php'));
    }
}

/** Bersihkan output supaya aman dari XSS */
function h($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/** Set pesan flash (notifikasi sekali tampil) */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil & hapus pesan flash */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Hitung path relatif dari root project, dipakai supaya link konsisten di semua folder */
function base_url($path = '') {
    return '/' . ltrim($path, '/');
}

/** Label warna badge sesuai jenjang */
function jenjang_badge_class($jenjang) {
    return [
        'SD'  => 'badge-sd',
        'SMP' => 'badge-smp',
        'SMA' => 'badge-sma',
    ][$jenjang] ?? 'badge-sd';
}

/** Potong teks dengan aman (tidak bergantung pada ekstensi mbstring) */
function potong_teks($text, $panjang = 100) {
    $text = trim(strip_tags($text ?? ''));
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) > $panjang ? mb_substr($text, 0, $panjang) . '...' : $text;
    }
    return strlen($text) > $panjang ? substr($text, 0, $panjang) . '...' : $text;
}

/** Format tanggal ke Bahasa Indonesia sederhana */
function format_tanggal($tanggal) {
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $ts = strtotime($tanggal);
    return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}
