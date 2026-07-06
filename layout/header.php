<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/theme.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/notification.php';
enforce_session_timeout();
$firm = db()->query('SELECT * FROM firm_settings LIMIT 1')->fetch() ?: [];
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!doctype html>
<html lang="en" data-theme="<?= e(active_theme()) ?>" data-mode="<?= e(active_mode()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle) ?> | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link rel="icon" href="<?= e($firm['favicon'] ?? asset('img/favicon.svg')) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/variables.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/theme.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/common.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/responsive.css')) ?>" rel="stylesheet">
    <script>
        window.TaxPortalConfig = {
            preferencesUrl: '<?= e(app_url('ajax/preferences.php')) ?>',
            logoutUrl: '<?= e(app_url('modules/auth/logout.php')) ?>',
            sessionMinutes: <?= (int) (current_user()['session_timeout'] ?? DEFAULT_SESSION_TIMEOUT) ?>
        };
    </script>
</head>
<body>
<?php require __DIR__ . '/loader.php'; ?>
<div class="app-shell">
