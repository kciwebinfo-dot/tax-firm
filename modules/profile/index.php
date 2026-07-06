<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$pageTitle = 'My Profile';
require_once __DIR__ . '/../../layout/header.php';
$user = current_user();
?>
<?php require __DIR__ . '/../../layout/sidebar.php'; ?>
<div class="main-panel">
<?php require __DIR__ . '/../../layout/topbar.php'; ?>
<main class="content">
    <?php require __DIR__ . '/../../layout/breadcrumbs.php'; ?>
    <section class="panel">
        <div class="panel-header"><h2>Profile Preferences</h2></div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="profile-avatar"><?= e(user_initials($user['name'])) ?></div>
                <h3 class="h5 mt-3"><?= e($user['name']) ?></h3>
                <p class="text-muted"><?= e($user['role']) ?></p>
                <div class="progress" role="progressbar" aria-label="Profile completion">
                    <div class="progress-bar" style="width: 72%">72%</div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6 form-floating">
                        <input class="form-control" value="<?= e($user['email']) ?>" id="email" readonly>
                        <label for="email">Email</label>
                    </div>
                    <div class="col-md-6 form-floating">
                        <input class="form-control" value="<?= e($user['mobile']) ?>" id="mobile" readonly>
                        <label for="mobile">Mobile</label>
                    </div>
                    <div class="col-md-6 form-floating">
                        <input class="form-control" value="<?= e(ucwords(str_replace('-', ' ', active_theme()))) ?>" id="theme" readonly>
                        <label for="theme">Theme</label>
                    </div>
                    <div class="col-md-6 form-floating">
                        <input class="form-control" value="<?= e((string) $user['session_timeout']) ?> minutes" id="timeout" readonly>
                        <label for="timeout">Session Timeout</label>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
