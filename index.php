<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect(base_url($_SESSION['role'] . '/dashboard.php'));
} else {
    redirect(base_url('auth/login.php'));
}
