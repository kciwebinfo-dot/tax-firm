<?php
/**
 * Application-wide configuration.
 * Update these values after uploading to Hostinger.
 */
declare(strict_types=1);

define('APP_NAME', 'Tax Management Portal');
define('APP_ENV', 'development');
// Use your final public URL, for example: https://yourdomain.com/aa
// Use "auto" while testing on Hostinger preview URLs, localhost, or changing folders.
define('APP_URL', 'auto');
define('APP_TIMEZONE', 'Asia/Kolkata');

define('DB_HOST', 'localhost');
define('DB_NAME', 'tax_firm_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'tax_firm_session');
define('DEFAULT_SESSION_TIMEOUT', 15);
define('OTP_VALID_MINUTES', 10);
define('OTP_MAX_ATTEMPTS', 5);

date_default_timezone_set(APP_TIMEZONE);
