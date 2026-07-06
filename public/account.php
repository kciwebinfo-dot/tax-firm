<?php
require_once __DIR__ . '/functions.php';
require_login();

$firm = firm();
$user = refresh_session_user((int) current_user()['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        flash_set('danger', 'Security token expired. Please try again.');
        redirect(app_url('account.php'));
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $themeMode = in_array($_POST['theme_mode'] ?? 'light', ['light', 'dark'], true) ? $_POST['theme_mode'] : 'light';
    $themeColor = preg_replace('/[^a-z0-9_-]/i', '', (string) ($_POST['theme_color'] ?? 'royal'));

    if ($name === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        flash_set('danger', 'Please enter valid account details.');
        redirect(app_url('account.php'));
    }

    if (!empty($_POST['new_password'])) {
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) $_POST['new_password'];

        if (strlen($newPassword) < 8 || !password_verify($currentPassword, (string) $user['password'])) {
            flash_set('danger', 'Password not updated. Check current password and minimum length.');
            redirect(app_url('account.php'));
        }

        $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, mobile = ?, theme_mode = ?, theme_color = ?, password = ? WHERE id = ?');
        $stmt->execute([$name, $email, $mobile, $themeMode, $themeColor, password_hash($newPassword, PASSWORD_DEFAULT), (int) $user['id']]);
    } else {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, mobile = ?, theme_mode = ?, theme_color = ? WHERE id = ?');
        $stmt->execute([$name, $email, $mobile, $themeMode, $themeColor, (int) $user['id']]);
    }

    $_SESSION['user'] = array_merge($_SESSION['user'], [
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'theme_mode' => $themeMode,
        'theme_color' => $themeColor,
    ]);

    flash_set('success', 'Account updated successfully.');
    redirect(app_url('account.php'));
}

$flash = flash_get();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="app-screen <?= e(active_theme_class()) ?>">
<?php render_app_sidebar($firm); ?>
<main class="app-main">
    <?php render_app_topbar($firm, current_user() ?: [], 'My Profile'); ?>
    <section class="profile-layout">
        <aside class="panel profile-summary">
            <div class="profile-avatar large"><?= e(user_initials($user['name'] ?? 'User')) ?></div>
            <h2><?= e($user['name'] ?? 'User') ?></h2>
            <p><?= e($user['role'] ?? 'Staff') ?></p>
            <div class="summary-list">
                <span><i class="fa-solid fa-user"></i><?= e($user['username'] ?? 'Not set') ?></span>
                <span><i class="fa-solid fa-envelope"></i><?= e($user['email'] ?? 'Not set') ?></span>
                <span><i class="fa-solid fa-phone"></i><?= e($user['mobile'] ?? 'Not set') ?></span>
                <span><i class="fa-solid fa-circle-half-stroke"></i><?= e(ucfirst($user['theme_mode'] ?? 'light')) ?> mode</span>
            </div>
        </aside>
        <section class="panel account-panel">
        <div class="panel-header">
            <h1>Account Details</h1>
            <span class="badge text-bg-light">User Profile</span>
        </div>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <form method="post" class="row g-3">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="col-md-6 form-floating">
                <input class="form-control" id="name" name="name" value="<?= e($user['name'] ?? '') ?>" required>
                <label for="name">Name</label>
            </div>
            <div class="col-md-6 form-floating">
                <input class="form-control" id="email" name="email" type="email" value="<?= e($user['email'] ?? '') ?>">
                <label for="email">Email</label>
            </div>
            <div class="col-md-6 form-floating">
                <input class="form-control" id="mobile" name="mobile" value="<?= e($user['mobile'] ?? '') ?>">
                <label for="mobile">Mobile</label>
            </div>
            <div class="col-md-3 form-floating">
                <select class="form-select" id="theme_mode" name="theme_mode">
                    <option value="light" <?= ($user['theme_mode'] ?? 'light') === 'light' ? 'selected' : '' ?>>Light</option>
                    <option value="dark" <?= ($user['theme_mode'] ?? 'light') === 'dark' ? 'selected' : '' ?>>Dark</option>
                </select>
                <label for="theme_mode">Mode</label>
            </div>
            <div class="col-md-3 form-floating">
                <select class="form-select" id="theme_color" name="theme_color">
                    <?php foreach (available_theme_colors() as $theme => $label): ?>
                        <option value="<?= e($theme) ?>" <?= ($user['theme_color'] ?? 'royal') === $theme ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="theme_color">Theme</label>
            </div>
            <div class="col-12"><h2 class="form-section">Change Password</h2></div>
            <div class="col-md-6 form-floating">
                <input class="form-control" id="current_password" name="current_password" type="password">
                <label for="current_password">Current Password</label>
            </div>
            <div class="col-md-6 form-floating">
                <input class="form-control" id="new_password" name="new_password" type="password" minlength="8">
                <label for="new_password">New Password</label>
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Save Account</button>
            </div>
        </form>
        </section>
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
