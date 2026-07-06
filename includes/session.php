<?php
/**
 * Session bootstrap and inactivity timeout.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

ini_set('session.use_strict_mode', '1');
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']),
    'samesite' => 'Lax',
]);
session_start();

function enforce_session_timeout(): void
{
    if (empty($_SESSION['user'])) {
        return;
    }

    $timeout = (int) ($_SESSION['user']['session_timeout'] ?? DEFAULT_SESSION_TIMEOUT);
    $lastActivity = (int) ($_SESSION['last_activity'] ?? time());

    if ((time() - $lastActivity) > ($timeout * 60)) {
        session_unset();
        session_destroy();
        redirect(app_url('modules/auth/login.php?timeout=1'));
    }

    $_SESSION['last_activity'] = time();
}
