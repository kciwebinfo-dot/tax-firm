<?php
/**
 * Authentication helpers.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function require_login(): void
{
    if (empty($_SESSION['user'])) {
        redirect(app_url('modules/auth/login.php'));
    }
}

function attempt_login(string $username, string $password): array
{
    $stmt = db()->prepare('SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.username = ? AND u.status = "active" LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    if (!empty($user['active_session_id']) && $user['active_session_id'] !== session_id()) {
        $_SESSION['pending_login_user_id'] = (int) $user['id'];
        return ['success' => false, 'duplicate' => true, 'message' => 'This account is already logged in elsewhere.'];
    }

    complete_login($user);
    return ['success' => true, 'message' => 'Welcome back.'];
}

function complete_login(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'email' => $user['email'],
        'mobile' => $user['mobile'],
        'role' => $user['role_name'] ?? 'Staff',
        'theme' => $user['theme'] ?: 'blue',
        'mode' => $user['mode'] ?: 'light',
        'session_timeout' => (int) ($user['session_timeout'] ?: DEFAULT_SESSION_TIMEOUT),
        'photo' => $user['photo'] ?? '',
    ];
    $_SESSION['last_activity'] = time();

    $stmt = db()->prepare('UPDATE users SET active_session_id = ?, last_login_at = NOW() WHERE id = ?');
    $stmt->execute([session_id(), (int) $user['id']]);

    $log = db()->prepare('INSERT INTO login_logs (user_id, session_id, ip_address, user_agent, status, created_at) VALUES (?, ?, ?, ?, "success", NOW())');
    $log->execute([(int) $user['id'], session_id(), $_SERVER['REMOTE_ADDR'] ?? '', substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)]);
}

function logout_user(): void
{
    if (!empty($_SESSION['user']['id'])) {
        $stmt = db()->prepare('UPDATE users SET active_session_id = NULL WHERE id = ? AND active_session_id = ?');
        $stmt->execute([(int) $_SESSION['user']['id'], session_id()]);
    }

    session_unset();
    session_destroy();
}
