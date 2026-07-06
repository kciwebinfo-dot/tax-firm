<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

ini_set('session.use_strict_mode', '1');
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']),
    'samesite' => 'Lax',
]);
session_start();

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_base_url(): string
{
    return rtrim(APP_URL, '/');
}

function app_url(string $path = ''): string
{
    return app_base_url() . '/' . ltrim($path, '/');
}

function asset_url(string $path): string
{
    return app_base_url() . '/assets/' . ltrim($path, '/');
}

function public_file_exists(?string $path): bool
{
    if (!$path || preg_match('#^https?://#', $path)) {
        return false;
    }

    return is_file(dirname(__DIR__) . '/' . ltrim($path, '/'));
}

function firm_logo_url(array $firm): string
{
    if (!empty($firm['logo']) && preg_match('#^https?://#', (string) $firm['logo'])) {
        return (string) $firm['logo'];
    }

    if (!empty($firm['logo']) && public_file_exists($firm['logo'])) {
        return app_url($firm['logo']);
    }

    return asset_url('img/logo.svg');
}

function firm_favicon_url(array $firm): string
{
    if (!empty($firm['favicon']) && preg_match('#^https?://#', (string) $firm['favicon'])) {
        return (string) $firm['favicon'];
    }

    if (!empty($firm['favicon']) && public_file_exists($firm['favicon'])) {
        return app_url($firm['favicon']);
    }

    return asset_url('img/favicon.svg');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['_csrf_token'] ?? '', $token);
}

function firm(): array
{
    static $firm = null;

    if ($firm !== null) {
        return $firm;
    }

    $stmt = db()->query('SELECT * FROM firm_settings WHERE status = 1 ORDER BY id ASC LIMIT 1');
    $firm = $stmt->fetch() ?: [];

    return $firm;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function user_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';

    foreach (array_slice($parts ?: [], 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    return $initials ?: 'U';
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function active_theme_class(): string
{
    $user = current_user();
    $color = preg_replace('/[^a-z0-9_-]/i', '', (string) ($user['theme_color'] ?? 'royal'));
    $mode = preg_replace('/[^a-z0-9_-]/i', '', (string) ($user['theme_mode'] ?? 'light'));

    return 'theme-' . ($color ?: 'royal') . ' mode-' . ($mode ?: 'light');
}

function available_theme_colors(): array
{
    return [
        'royal' => 'Royal',
        'purple' => 'Purple',
        'green' => 'Green',
        'orange' => 'Orange',
        'teal' => 'Teal',
        'slate' => 'Slate',
    ];
}

function selected_attr(?string $value, string $expected): string
{
    return $value === $expected ? 'selected' : '';
}

function active_nav(string $page): string
{
    $current = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    return $current === $page ? 'active' : '';
}

function render_app_sidebar(array $firm): void
{
    ?>
    <aside class="app-sidebar" id="appSidebar">
        <div class="firm-brand compact">
            <img src="<?= e(firm_logo_url($firm)) ?>" alt="Firm logo">
            <div>
                <strong><?= e(($firm['short_name'] ?? '') ?: ($firm['firm_name'] ?? 'Tax Firm')) ?></strong>
                <span><?= e(($firm['tagline'] ?? '') ?: ($firm['firm_code'] ?? 'Tax Portal')) ?></span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a class="<?= e(active_nav('dashboard.php')) ?>" href="<?= e(app_url('dashboard.php')) ?>"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
            <a href="#"><i class="fa-solid fa-users"></i><span>Clients</span></a>
            <a href="#"><i class="fa-solid fa-file-invoice"></i><span>GST</span></a>
            <a href="#"><i class="fa-solid fa-receipt"></i><span>ITR</span></a>
            <a href="#"><i class="fa-solid fa-wallet"></i><span>Fees</span></a>
            <a href="#"><i class="fa-solid fa-user-shield"></i><span>Roles</span></a>
            <a class="<?= e(active_nav('account.php')) ?>" href="<?= e(app_url('account.php')) ?>"><i class="fa-solid fa-user-gear"></i><span>Profile</span></a>
            <a href="#"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
        </nav>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <?php
}

function render_app_topbar(array $firm, array $user, string $title = 'Dashboard'): void
{
    ?>
    <header class="app-topbar">
        <button class="icon-btn d-lg-none" id="sidebarToggle" type="button" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <div class="topbar-title">
            <span><?= e($firm['firm_name'] ?? APP_NAME) ?></span>
            <h1><?= e($title) ?></h1>
        </div>
        <div class="global-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Search clients, returns, invoices">
        </div>
        <div class="topbar-actions">
            <button class="icon-btn" id="modeToggle" type="button" title="Toggle dark mode" aria-label="Toggle dark mode">
                <i class="fa-solid <?= ($user['theme_mode'] ?? 'light') === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
            </button>
            <select class="form-select form-select-sm theme-select" id="themeSelect" title="Theme">
                <?php foreach (available_theme_colors() as $theme => $label): ?>
                    <option value="<?= e($theme) ?>" <?= selected_attr($user['theme_color'] ?? 'royal', $theme) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="dropdown">
                <button class="icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications"><i class="fa-solid fa-bell"></i></button>
                <div class="dropdown-menu dropdown-menu-end notification-menu">
                    <span class="dropdown-item-text text-muted">No notifications</span>
                </div>
            </div>
            <div class="dropdown">
                <button class="profile-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span><?= e(user_initials($user['name'] ?? 'User')) ?></span>
                    <strong><?= e($user['name'] ?? 'User') ?></strong>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= e(app_url('account.php')) ?>">My Account</a></li>
                    <li><a class="dropdown-item" href="#">Activity</a></li>
                    <li><a class="dropdown-item" href="#">Wallet</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= e(app_url('logout.php')) ?>">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>
    <?php
}

function find_user_by_login(string $login): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE (username = ? OR email = ? OR mobile = ?) AND status = 1 LIMIT 1');
    $stmt->execute([$login, $login, $login]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function refresh_session_user(int $userId): ?array
{
    $stmt = db()->prepare('SELECT id, name, username, email, mobile, password, role, photo, sign, session_token, session_expires, theme_mode, theme_color, theme_style FROM users WHERE id = ? AND status = 1 LIMIT 1');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + SESSION_TIMEOUT_MINUTES * 60);

    $stmt = db()->prepare('UPDATE users SET session_token = ?, session_expires = ?, otp_code = NULL, otp_type = NULL, otp_expires = NULL, otp_attempts = 0, last_login = NOW(), last_ip = ?, last_user_agent = ? WHERE id = ?');
    $stmt->execute([
        $token,
        $expiresAt,
        $_SERVER['REMOTE_ADDR'] ?? '',
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        (int) $user['id'],
    ]);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'email' => $user['email'],
        'mobile' => $user['mobile'],
        'role' => $user['role'],
        'photo' => $user['photo'],
        'sign' => $user['sign'],
        'theme_mode' => $user['theme_mode'] ?: 'light',
        'theme_color' => $user['theme_color'] ?: 'royal',
        'theme_style' => $user['theme_style'] ?: 'default',
        'session_token' => $token,
    ];
}

function logout_user(): void
{
    $user = current_user();

    if ($user) {
        $stmt = db()->prepare('UPDATE users SET session_token = NULL, session_expires = NULL, last_logout = NOW() WHERE id = ? AND session_token = ?');
        $stmt->execute([(int) $user['id'], (string) $user['session_token']]);
    }

    session_unset();
    session_destroy();
}

function require_login(): void
{
    $sessionUser = current_user();

    if (!$sessionUser) {
        redirect(app_url('login.php'));
    }

    $dbUser = refresh_session_user((int) $sessionUser['id']);

    if (!$dbUser || !hash_equals((string) $dbUser['session_token'], (string) $sessionUser['session_token']) || strtotime((string) $dbUser['session_expires']) < time()) {
        session_unset();
        session_destroy();
        redirect(app_url('login.php?expired=1'));
    }

    $expiresAt = date('Y-m-d H:i:s', time() + SESSION_TIMEOUT_MINUTES * 60);
    $stmt = db()->prepare('UPDATE users SET session_expires = ? WHERE id = ?');
    $stmt->execute([$expiresAt, (int) $dbUser['id']]);

    $_SESSION['user'] = array_merge($_SESSION['user'], [
        'name' => $dbUser['name'],
        'email' => $dbUser['email'],
        'mobile' => $dbUser['mobile'],
        'role' => $dbUser['role'],
        'photo' => $dbUser['photo'],
        'sign' => $dbUser['sign'],
        'theme_mode' => $dbUser['theme_mode'],
        'theme_color' => $dbUser['theme_color'],
        'theme_style' => $dbUser['theme_style'],
    ]);
}

function send_whatsapp_otp(string $mobile, string $otp, string $type): bool
{
    $firm = firm();

    if (empty($firm['wa_meta_token']) || empty($firm['wa_phone_id']) || !function_exists('curl_init')) {
        return false;
    }

    $template = $type === 'forgot' ? 'forgot_otp' : 'otp_login';
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => preg_replace('/\D+/', '', $mobile),
        'type' => 'template',
        'template' => [
            'name' => $template,
            'language' => ['code' => 'en'],
            'components' => [[
                'type' => 'body',
                'parameters' => [['type' => 'text', 'text' => $otp]],
            ]],
        ],
    ];

    $url = 'https://graph.facebook.com/' . ($firm['wa_api_version'] ?: 'v25.0') . '/' . $firm['wa_phone_id'] . '/messages';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $firm['wa_meta_token'],
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $status >= 200 && $status < 300;
}
