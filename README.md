# Tax Management Portal

SQL-driven Core PHP login system for `u937735496_tax`.

## Structure

- `public/` contains all PHP files.
- `assets/` contains CSS, images and static assets.
- No `app`, `includes`, `modules`, `controllers`, `views`, or extra PHP folders are required.

## SQL Tables Used

The application uses only the uploaded SQL structure:

- `users`
- `firm_settings`

No extra user, role, session, OTP, log, or settings tables are created by the project.

## Main Files

- `public/config.php`
- `public/functions.php`
- `public/login.php`
- `public/otp-login.php`
- `public/forgot.php`
- `public/reset-password.php`
- `public/dashboard.php`
- `public/account.php`
- `public/logout.php`

## Setup

1. Import the uploaded SQL file `u937735496_tax.sql`.
2. Update `public/config.php` database credentials on hosting.
3. Open `https://knverse.in/aa/login.php`.

The public GitHub copy keeps `DB_PASS` blank. Add the live password only on the server.
