<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/theme.php';

header('Content-Type: application/json');

if (empty($_SESSION['user']) || !verify_csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized request.']);
    exit;
}

$theme = $_POST['theme'] ?? $_SESSION['user']['theme'];
$mode = $_POST['mode'] ?? $_SESSION['user']['mode'];
$timeout = (int) ($_POST['session_timeout'] ?? $_SESSION['user']['session_timeout']);
$allowedTimeouts = [15, 30, 45, 60, 90, 120, 240];

if (!in_array($theme, available_themes(), true) || !in_array($mode, ['light', 'dark'], true) || !in_array($timeout, $allowedTimeouts, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid preference.']);
    exit;
}

$stmt = db()->prepare('UPDATE users SET theme = ?, mode = ?, session_timeout = ? WHERE id = ?');
$stmt->execute([$theme, $mode, $timeout, (int) $_SESSION['user']['id']]);

$_SESSION['user']['theme'] = $theme;
$_SESSION['user']['mode'] = $mode;
$_SESSION['user']['session_timeout'] = $timeout;

echo json_encode(['success' => true, 'message' => 'Preference saved.']);
