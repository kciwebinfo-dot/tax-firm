<?php
require_once __DIR__ . '/functions.php';
require_login();

$firm = firm();
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link rel="icon" href="<?= e(firm_favicon_url($firm)) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="app-screen <?= e(active_theme_class()) ?>">
<?php render_app_sidebar($firm); ?>
<main class="app-main">
    <?php render_app_topbar($firm, $user, 'Dashboard'); ?>
    <section class="dashboard-grid">
        <article class="metric-card"><i class="fa-solid fa-building"></i><span>Firm</span><strong><?= e($firm['firm_name'] ?? 'Not set') ?></strong></article>
        <article class="metric-card"><i class="fa-solid fa-user-shield"></i><span>Role</span><strong><?= e($user['role'] ?? 'user') ?></strong></article>
        <article class="metric-card"><i class="fa-solid fa-clock"></i><span>Session</span><strong><?= e((string) SESSION_TIMEOUT_MINUTES) ?> min</strong></article>
        <article class="metric-card"><i class="fa-solid fa-palette"></i><span>Theme</span><strong><?= e($user['theme_color'] ?? 'royal') ?></strong></article>
    </section>
    <section class="panel">
        <div class="panel-header">
            <h2>Login System Ready</h2>
            <span class="badge text-bg-success">Secure</span>
        </div>
        <p>This portal is now using only the SQL tables: <strong>users</strong> and <strong>firm_settings</strong>.</p>
    </section>
</main>
<script>
window.TaxPortalConfig = {
    csrfToken: '<?= e(csrf_token()) ?>',
    preferencesUrl: '<?= e(app_url('preferences.php')) ?>',
    logoutUrl: '<?= e(app_url('logout.php')) ?>',
    sessionMinutes: <?= (int) SESSION_TIMEOUT_MINUTES ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset_url('js/app.js')) ?>"></script>
</body>
</html>
