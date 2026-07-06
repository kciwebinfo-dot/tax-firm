<?php
require_once __DIR__ . '/functions.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized request.']);
    exit;
}

$user = current_user();
$mode = $_POST['theme_mode'] ?? ($user['theme_mode'] ?? 'light');
$color = $_POST['theme_color'] ?? ($user['theme_color'] ?? 'royal');

if (!in_array($mode, ['light', 'dark'], true) || !array_key_exists($color, available_theme_colors())) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid preference.']);
    exit;
}

$stmt = db()->prepare('UPDATE users SET theme_mode = ?, theme_color = ? WHERE id = ?');
$stmt->execute([$mode, $color, (int) $user['id']]);

$_SESSION['user']['theme_mode'] = $mode;
$_SESSION['user']['theme_color'] = $color;

echo json_encode(['success' => true, 'message' => 'Preference saved.']);
