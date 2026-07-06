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
<aside class="app-sidebar">
    <div class="firm-brand compact">
        <img src="<?= e(firm_logo_url($firm)) ?>" alt="Firm logo">
        <div>
            <strong><?= e($firm['short_name'] ?? 'Tax Firm') ?></strong>
            <span><?= e($firm['firm_code'] ?? '') ?></span>
        </div>
    </div>
    <nav>
        <a class="active" href="<?= e(app_url('dashboard.php')) ?>"><i class="fa-solid fa-chart-line"></i>Dashboard</a>
        <a href="<?= e(app_url('account.php')) ?>"><i class="fa-solid fa-user-gear"></i>My Account</a>
        <a href="<?= e(app_url('logout.php')) ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i>Logout</a>
    </nav>
</aside>
<main class="app-main">
    <header class="app-topbar">
        <div>
            <span>Welcome back</span>
            <h1><?= e($user['name'] ?? 'User') ?></h1>
        </div>
        <a class="profile-pill" href="<?= e(app_url('account.php')) ?>">
            <span><?= e(user_initials($user['name'] ?? 'User')) ?></span>
            <strong><?= e($user['role'] ?? 'user') ?></strong>
        </a>
    </header>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
