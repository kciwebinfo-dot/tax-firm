<?php
require_once __DIR__ . '/functions.php';

$firm = firm();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
        $error = 'Security token expired. Please try again.';
    } else {
        $action = (string) ($_POST['action'] ?? 'send');
        $login = trim((string) ($_POST['login'] ?? ''));
        $user = $login !== '' ? find_user_by_login($login) : null;

        if (!$user || empty($user['mobile'])) {
            $error = 'No active user found for this detail.';
        } elseif ($action === 'verify') {
            $otp = trim((string) ($_POST['otp'] ?? ''));
            if ($user['otp_type'] !== 'login' || $user['otp_code'] !== $otp || strtotime((string) $user['otp_expires']) < time()) {
                db()->prepare('UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?')->execute([(int) $user['id']]);
                $error = 'Invalid or expired OTP.';
            } elseif ((int) $user['otp_attempts'] >= OTP_MAX_ATTEMPTS) {
                $error = 'Too many OTP attempts. Generate a new OTP.';
            } else {
                login_user($user);
                redirect(app_url('dashboard.php'));
            }
        } else {
            $otp = (string) random_int(100000, 999999);
            $stmt = db()->prepare('UPDATE users SET otp_code = ?, otp_type = "login", otp_expires = ?, otp_attempts = 0 WHERE id = ?');
            $stmt->execute([$otp, date('Y-m-d H:i:s', time() + OTP_VALID_MINUTES * 60), (int) $user['id']]);
            send_whatsapp_otp((string) $user['mobile'], $otp, 'login');
            $message = 'OTP generated. If WhatsApp settings are active, it has been sent to the registered mobile.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Login | <?= e($firm['short_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="login-screen">
<main class="login-shell">
    <section class="login-card">
        <h1>WhatsApp OTP Login</h1>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-floating">
                <input class="form-control" id="login" name="login" required>
                <label for="login">Username, Email or Mobile</label>
            </div>
            <div class="form-floating">
                <input class="form-control" id="otp" name="otp" maxlength="10">
                <label for="otp">OTP</label>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit" name="action" value="send">Generate OTP</button>
                <button class="btn btn-outline-primary" type="submit" name="action" value="verify">Verify & Login</button>
            </div>
        </form>
        <div class="auth-links">
            <a href="<?= e(app_url('login.php')) ?>">Password Login</a>
        </div>
    </section>
</main>
</body>
</html>
