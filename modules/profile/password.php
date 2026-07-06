<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['_csrf_token'] ?? null)) {
    flash_set('error', 'Security token expired. Please try again.');
    redirect(app_url('modules/profile/index.php'));
}

$userId = (int) current_user()['id'];
$currentPassword = (string) ($_POST['current_password'] ?? '');
$newPassword = (string) ($_POST['new_password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

if (strlen($newPassword) < 8 || $newPassword !== $confirmPassword) {
    flash_set('error', 'New password must be at least 8 characters and match confirmation.');
    redirect(app_url('modules/profile/index.php'));
}

$stmt = db()->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$hash = (string) $stmt->fetchColumn();

if (!$hash || !password_verify($currentPassword, $hash)) {
    flash_set('error', 'Current password is incorrect.');
    redirect(app_url('modules/profile/index.php'));
}

$update = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);

log_activity($userId, 'profile.password_changed', 'User changed password.');
flash_set('success', 'Password updated successfully.');
redirect(app_url('modules/profile/index.php'));
