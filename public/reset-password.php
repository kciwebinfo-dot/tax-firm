<?php
require_once __DIR__ . '/functions.php';

$firm = firm();
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        $error = 'Security token expired. Please try again.';
    } else {
        $login = trim((string) ($_POST['login'] ?? ''));
        $otp = trim((string) ($_POST['otp'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = $login !== '' ? find_user_by_login($login) : null;

        if (!$user || $user['otp_type'] !== 'forgot' || $user['otp_code'] !== $otp || strtotime((string) $user['otp_expires']) < time()) {
            $error = 'Invalid or expired OTP.';
            if ($user) {
                db()->prepare('UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?')->execute([(int) $user['id']]);
            }
        } elseif ((int) $user['otp_attempts'] >= OTP_MAX_ATTEMPTS) {
            $error = 'Too many OTP attempts. Generate a new OTP.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } else {
            $stmt = db()->prepare('UPDATE users SET password = ?, otp_code = NULL, otp_type = NULL, otp_expires = NULL, otp_attempts = 0 WHERE id = ?');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), (int) $user['id']]);
            $message = 'Password reset successfully. You can login now.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="login-screen">
<main class="login-shell">
    <section class="login-card">
        <h1>Reset Password</h1>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-floating">
                <input class="form-control" id="login" name="login" required>
                <label for="login">Username, Email or Mobile</label>
            </div>
            <div class="form-floating">
                <input class="form-control" id="otp" name="otp" required maxlength="10">
                <label for="otp">OTP</label>
            </div>
            <div class="form-floating">
                <input class="form-control" id="password" name="password" type="password" required minlength="8">
                <label for="password">New Password</label>
            </div>
            <button class="btn btn-primary w-100" type="submit">Reset Password</button>
        </form>
        <div class="auth-links">
            <a href="<?= e(app_url('login.php')) ?>">Back to Login</a>
        </div>
    </section>
</main>
</body>
</html>
