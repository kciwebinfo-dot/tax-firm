<?php
require_once __DIR__ . '/functions.php';

$firm = firm();
$error = '';
$duplicateUser = null;

if (current_user()) {
    redirect(app_url('dashboard.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        $error = 'Security token expired. Please try again.';
    } elseif (isset($_POST['force_login']) && !empty($_SESSION['pending_login_user_id'])) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? AND status = 1 LIMIT 1');
        $stmt->execute([(int) $_SESSION['pending_login_user_id']]);
        $forceUser = $stmt->fetch();

        if ($forceUser) {
            login_user($forceUser);
            unset($_SESSION['pending_login_user_id']);
            redirect(app_url('dashboard.php'));
        }

        $error = 'Unable to continue login. Please try again.';
    } else {
        $login = trim((string) ($_POST['login'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = $login !== '' ? find_user_by_login($login) : null;

        if (!$user || !password_verify($password, (string) $user['password'])) {
            $error = 'Invalid login details.';
        } elseif (!empty($user['session_token']) && strtotime((string) $user['session_expires']) > time()) {
            $duplicateUser = $user;
            $_SESSION['pending_login_user_id'] = (int) $user['id'];
        } else {
            login_user($user);
            unset($_SESSION['pending_login_user_id']);
            redirect(app_url('dashboard.php'));
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link rel="icon" href="<?= e(firm_favicon_url($firm)) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="login-screen">
<main class="login-shell">
    <section class="login-card">
        <div class="firm-brand">
            <img src="<?= e(firm_logo_url($firm)) ?>" alt="Firm logo">
            <div>
                <h1><?= e($firm['firm_name'] ?? APP_NAME) ?></h1>
                <p><?= e($firm['tagline'] ?? 'Secure tax management workspace') ?></p>
            </div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['expired'])): ?>
            <div class="alert alert-warning">Your session expired. Please login again.</div>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-floating">
                <input class="form-control" id="login" name="login" placeholder="Username, email or mobile" required>
                <label for="login">Username, Email or Mobile</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <button class="btn btn-primary w-100" type="submit"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</button>
        </form>
        <div class="auth-links">
            <a href="<?= e(app_url('otp-login.php')) ?>">WhatsApp OTP Login</a>
            <a href="<?= e(app_url('forgot.php')) ?>">Forgot Password?</a>
        </div>
        <footer><?= e($firm['footer_text'] ?? '') ?></footer>
    </section>
</main>
<?php if ($duplicateUser): ?>
<form id="forceLoginForm" method="post">
    <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="force_login" value="1">
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    title: 'Already logged in',
    text: 'This account is active somewhere else.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue Here',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) document.getElementById('forceLoginForm').submit();
});
</script>
<?php endif; ?>
</body>
</html>
