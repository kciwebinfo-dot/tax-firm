<?php
declare(strict_types=1);

define('APP_NAME', 'Tax Management Portal');
define('APP_URL', 'https://knverse.in/aa/');
define('APP_TIMEZONE', 'Asia/Kolkata');

define('DB_HOST', 'localhost');
define('DB_NAME', 'u937735496_tax');
define('DB_USER', 'u937735496_aa');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'tax_portal_session');
define('SESSION_TIMEOUT_MINUTES', 15);
define('OTP_VALID_MINUTES', 10);
define('OTP_MAX_ATTEMPTS', 5);

date_default_timezone_set(APP_TIMEZONE);
