<?php
/**
 * Application-wide configuration.
 * Update DB_PASS only on the hosting server. Do not commit live passwords to a public repository.
 */
declare(strict_types=1);

define('APP_NAME', 'Tax Management Portal');
define('APP_ENV', 'development');
define('APP_URL', 'https://knverse.in/aa/');
define('APP_TIMEZONE', 'Asia/Kolkata');

define('DB_HOST', 'localhost');
define('DB_NAME', 'u937735496_aa');
define('DB_USER', 'u937735496_aa');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'tax_firm_session');
define('DEFAULT_SESSION_TIMEOUT', 15);
define('OTP_VALID_MINUTES', 10);
define('OTP_MAX_ATTEMPTS', 5);

date_default_timezone_set(APP_TIMEZONE);
