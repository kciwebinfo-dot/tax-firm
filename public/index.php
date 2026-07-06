<?php
require_once __DIR__ . '/functions.php';

if (current_user()) {
    redirect(app_url('dashboard.php'));
}

redirect(app_url('login.php'));
