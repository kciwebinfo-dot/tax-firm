<?php
require_once __DIR__ . '/functions.php';

$firm = firm();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        $error = 'Security token expired. Please try again.';
    } else {
        $login = trim((string) ($_POST['login'] ?? ''));
        $user = $login !== '' ? find_user_by_login($login) : null;

        if (!$user || empty($user['mobile'])) {
            $error = 'No active user found for this detail.';
        } else {
            $otp = (string) random_int(100000, 999999);
            $stmt = db()->prepare('UPDATE users SET otp_code = ?, otp_type = "forgot", otp_expires = ?, otp_attempts = 0 WHERE id = ?');
            $stmt->execute([$otp, date('Y-m-d H:i:s', time() + OTP_VALID_MINUTES * 60), (int) $user['id']]);
            send_whatsapp_otp((string) $user['mobile'], $otp, 'forgot');
            $message = 'OTP generated. If WhatsApp settings are active, it has been sent to the registered mobile.';
            $_SESSION['reset_user_id'] = (int) $user['id'];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="login-screen">
<main class="login-shell">
    <section class="login-card">
        <h1>Password Recovery</h1>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-floating">
                <input class="form-control" id="login" name="login" placeholder="Username, email or mobile" required>
                <label for="login">Username, Email or Mobile</label>
            </div>
            <button class="btn btn-primary w-100" type="submit">Generate OTP</button>
        </form>
        <div class="auth-links">
            <a href="<?= e(app_url('reset-password.php')) ?>">I have OTP</a>
            <a href="<?= e(app_url('login.php')) ?>">Back to Login</a>
        </div>
    </section>
</main>
</body>
</html>
