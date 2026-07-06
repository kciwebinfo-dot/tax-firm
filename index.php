<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user'])) {
    redirect(app_url('modules/dashboard/index.php'));
}

redirect(app_url('modules/auth/login.php'));
