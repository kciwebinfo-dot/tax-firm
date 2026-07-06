<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/theme.php';
require_once __DIR__ . '/../../includes/flash.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['_csrf_token'] ?? null)) {
    flash_set('error', 'Security token expired. Please try again.');
    redirect(app_url('modules/profile/index.php'));
}

$userId = (int) current_user()['id'];
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));
$language = trim((string) ($_POST['language'] ?? 'en'));
$theme = trim((string) ($_POST['theme'] ?? 'blue'));
$mode = trim((string) ($_POST['mode'] ?? 'light'));
$sessionTimeout = (int) ($_POST['session_timeout'] ?? DEFAULT_SESSION_TIMEOUT);
$allowedTimeouts = [15, 30, 45, 60, 90, 120, 240];

if ($name === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
    flash_set('error', 'Please enter a valid name and email.');
    redirect(app_url('modules/profile/index.php'));
}

if (!in_array($theme, available_themes(), true) || !in_array($mode, ['light', 'dark'], true) || !in_array($language, ['en', 'hi'], true) || !in_array($sessionTimeout, $allowedTimeouts, true)) {
    flash_set('error', 'Please select valid preferences.');
    redirect(app_url('modules/profile/index.php'));
}

$profileFields = [
    'address' => substr(trim((string) ($_POST['address'] ?? '')), 0, 500),
    'bank_name' => substr(trim((string) ($_POST['bank_name'] ?? '')), 0, 120),
    'bank_account' => substr(trim((string) ($_POST['bank_account'] ?? '')), 0, 60),
    'ifsc' => strtoupper(substr(trim((string) ($_POST['ifsc'] ?? '')), 0, 20)),
    'emergency_name' => substr(trim((string) ($_POST['emergency_name'] ?? '')), 0, 120),
    'emergency_mobile' => substr(trim((string) ($_POST['emergency_mobile'] ?? '')), 0, 30),
];

$pdo = db();
$pdo->beginTransaction();

try {
    $userStmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, mobile = ?, theme = ?, mode = ?, language = ?, session_timeout = ? WHERE id = ?');
    $userStmt->execute([$name, $email, $mobile, $theme, $mode, $language, $sessionTimeout, $userId]);

    $profileStmt = $pdo->prepare('INSERT INTO user_profiles (user_id, address, bank_name, bank_account, ifsc, emergency_name, emergency_mobile) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE address = VALUES(address), bank_name = VALUES(bank_name), bank_account = VALUES(bank_account), ifsc = VALUES(ifsc), emergency_name = VALUES(emergency_name), emergency_mobile = VALUES(emergency_mobile)');
    $profileStmt->execute([
        $userId,
        $profileFields['address'],
        $profileFields['bank_name'],
        $profileFields['bank_account'],
        $profileFields['ifsc'],
        $profileFields['emergency_name'],
        $profileFields['emergency_mobile'],
    ]);

    $pdo->commit();

    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['mobile'] = $mobile;
    $_SESSION['user']['theme'] = $theme;
    $_SESSION['user']['mode'] = $mode;
    $_SESSION['user']['session_timeout'] = $sessionTimeout;

    log_activity($userId, 'profile.updated', 'User updated profile details.');
    flash_set('success', 'Profile updated successfully.');
} catch (Throwable $exception) {
    $pdo->rollBack();
    flash_set('error', 'Profile could not be updated.');
}

redirect(app_url('modules/profile/index.php'));
