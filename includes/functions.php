<?php
/**
 * Common helper functions used across the portal.
 */
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function app_base_url(): string
{
    $configuredUrl = trim((string) APP_URL);

    if ($configuredUrl !== '' && strtolower($configuredUrl) !== 'auto') {
        return rtrim($configuredUrl, '/');
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    $basePath = preg_replace('#/(modules|ajax|api|database)/.*$#', '', $scriptName);
    if ($basePath === $scriptName) {
        $basePath = preg_replace('#/index\.php$#', '', $scriptName);
    }

    return rtrim($scheme . '://' . $host . rtrim((string) $basePath, '/'), '/');
}

function asset(string $path): string
{
    return app_base_url() . '/assets/' . ltrim($path, '/');
}

function app_url(string $path = ''): string
{
    return app_base_url() . '/' . ltrim($path, '/');
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

function upload_url(?string $path): string
{
    if (!$path) {
        return '';
    }

    if (preg_match('#^https?://#', $path)) {
        return $path;
    }

    return app_base_url() . '/' . ltrim($path, '/');
}

function selected_attr(string $actual, string $expected): string
{
    return $actual === $expected ? 'selected' : '';
}

function profile_completion(array $user, array $profile): int
{
    $fields = [
        $user['name'] ?? '',
        $user['email'] ?? '',
        $user['mobile'] ?? '',
        $user['photo'] ?? '',
        $user['signature'] ?? '',
        $profile['address'] ?? '',
        $profile['bank_name'] ?? '',
        $profile['bank_account'] ?? '',
        $profile['ifsc'] ?? '',
        $profile['emergency_name'] ?? '',
        $profile['emergency_mobile'] ?? '',
    ];

    $filled = array_filter($fields, static fn ($value) => trim((string) $value) !== '');
    return (int) round((count($filled) / count($fields)) * 100);
}

function log_activity(int $userId, string $action, string $details = ''): void
{
    $stmt = db()->prepare('INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $userId,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? '',
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);
}
