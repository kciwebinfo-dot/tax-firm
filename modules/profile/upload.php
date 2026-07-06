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
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
$uploadDir = __DIR__ . '/../../uploads/profile';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$updates = [];

foreach (['photo', 'signature'] as $field) {
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        continue;
    }

    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || (int) $_FILES[$field]['size'] > 2 * 1024 * 1024) {
        flash_set('error', 'Only image files up to 2 MB are allowed.');
        redirect(app_url('modules/profile/index.php'));
    }

    $tmpName = (string) $_FILES[$field]['tmp_name'];
    $imageInfo = function_exists('mime_content_type') ? null : @getimagesize($tmpName);
    $mimeType = function_exists('mime_content_type') ? mime_content_type($tmpName) : ($imageInfo['mime'] ?? '');
    if (!isset($allowedMimeTypes[$mimeType])) {
        flash_set('error', 'Upload a valid JPG, PNG or WEBP image.');
        redirect(app_url('modules/profile/index.php'));
    }

    $filename = $userId . '-' . $field . '-' . time() . '.' . $allowedMimeTypes[$mimeType];
    $targetPath = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        flash_set('error', 'File upload failed. Please try again.');
        redirect(app_url('modules/profile/index.php'));
    }

    $updates[$field] = 'uploads/profile/' . $filename;
}

if (!$updates) {
    flash_set('error', 'Please choose a file to upload.');
    redirect(app_url('modules/profile/index.php'));
}

$sets = [];
$values = [];
foreach ($updates as $column => $path) {
    $sets[] = $column . ' = ?';
    $values[] = $path;
    $_SESSION['user'][$column] = $path;
}
$values[] = $userId;

$stmt = db()->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?');
$stmt->execute($values);

log_activity($userId, 'profile.files_uploaded', 'User uploaded profile assets.');
flash_set('success', 'Files uploaded successfully.');
redirect(app_url('modules/profile/index.php'));
