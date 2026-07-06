<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/flash.php';

$firm = db()->query('SELECT * FROM firm_settings LIMIT 1')->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        flash_set('error', 'Security token expired. Please try again.');
        redirect(app_url('modules/auth/login.php'));
    }

    if (isset($_POST['kill_previous']) && !empty($_SESSION['pending_login_user_id'])) {
        $stmt = db()->prepare('SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['pending_login_user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            db()->prepare('UPDATE users SET active_session_id = NULL WHERE id = ?')->execute([(int) $user['id']]);
            unset($_SESSION['pending_login_user_id']);
            complete_login($user);
            redirect(app_url('modules/dashboard/index.php'));
        }
    }

    $result = attempt_login(trim($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''));
    if (!empty($result['success'])) {
        redirect(app_url('modules/dashboard/index.php'));
    }
    $loginError = $result;
}
?>
<!doctype html>
<html lang="en" data-theme="blue" data-mode="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>Login | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/variables.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/theme.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/common.css')) ?>" rel="stylesheet">
</head>
<body class="login-page">
<main class="login-wrap">
    <section class="login-card">
        <div class="login-brand">
            <img src="<?= e($firm['logo'] ?? asset('img/logo.svg')) ?>" alt="Firm logo">
            <div>
                <h1><?= e($firm['name'] ?? 'Tax Management Portal') ?></h1>
                <p><?= e($firm['email'] ?? 'Secure staff workspace') ?></p>
            </div>
        </div>
        <form method="post" class="mt-4">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-floating mb-3">
                <input class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <button class="btn btn-gradient w-100" type="submit">Sign in</button>
        </form>
        <div class="login-options">
            <a href="#">WhatsApp OTP Login</a>
            <a href="#">Forgot Password</a>
        </div>
        <footer><?= e($firm['phone'] ?? '') ?> <?= e($firm['website'] ?? '') ?></footer>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($loginError['duplicate'])): ?>
<form method="post" id="killSessionForm">
    <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="kill_previous" value="1">
</form>
<script>
Swal.fire({
    title: 'Already logged in',
    text: 'This user is active in another browser or device.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Kill Previous Session',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) document.getElementById('killSessionForm').submit();
});
</script>
<?php elseif (!empty($loginError)): ?>
<script>Swal.fire('Login failed', '<?= e($loginError['message']) ?>', 'error');</script>
<?php endif; ?>
</body>
</html>
