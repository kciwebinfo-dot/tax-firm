# Tax Management Portal

Production-oriented PHP 8 + MySQL starter for a multi-staff tax firm portal.

## First Module Included

- Reusable folder architecture
- PDO database layer
- Shared layout files
- Staff login
- Duplicate login detection with kill previous session
- Dashboard shell with cards, charts, tables and notifications
- 12 theme tokens and dark mode preference storage
- Session timeout preference foundation
- RBAC-ready tables
- Hostinger-compatible `.htaccess`

## Setup

1. Create a MySQL database and import `database/schema.sql`.
2. Update `config/config.php` with your database credentials. Keep `APP_URL` as `auto` while testing, or set the final public URL such as `https://yourdomain.com/aa`.
3. Open `database/seed.php` once to create the demo admin user.
4. Open `/login` or `modules/auth/login.php`.

Demo login:

- Username: `admin`
- Password: `Admin@12345`

## Next Modules

Recommended order:

1. User profile edit and upload module
2. WhatsApp OTP login and forgot password module
3. Staff and role permission management
4. Client master module
5. GST and ITR due workflow
