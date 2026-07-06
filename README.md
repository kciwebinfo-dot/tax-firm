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

## Phase 2 Included

- Editable staff profile
- Bank and emergency contact details
- Profile photo and signature upload
- Password change with current password verification
- Theme, dark mode, language and session timeout preferences
- Profile completion meter

## Setup

1. Create a MySQL database and import `database/schema.sql`.
2. Update `config/config.php` with your database credentials. Keep `APP_URL` as `auto` while testing, or set the final public URL such as `https://yourdomain.com/aa`.
3. Open `database/seed.php` once to create the demo admin user.
4. Open `/login` or `modules/auth/login.php`.

For an existing Phase 1 database, import `database/phase2_profile.sql` once.

Demo login:

- Username: `admin`
- Password: `Admin@12345`

## Next Modules

Recommended order:

1. WhatsApp OTP login and forgot password module
2. Staff and role permission management
3. Client master module
4. GST and ITR due workflow
5. Fee collection and receipt module
