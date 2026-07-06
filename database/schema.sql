CREATE TABLE firm_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    short_name VARCHAR(50) NOT NULL,
    logo VARCHAR(255) NULL,
    favicon VARCHAR(255) NULL,
    email VARCHAR(120) NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    website VARCHAR(120) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE,
    slug VARCHAR(80) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    module VARCHAR(80) NOT NULL
);

CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NULL,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(120) NULL,
    mobile VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NULL,
    signature VARCHAR(255) NULL,
    theme VARCHAR(30) NOT NULL DEFAULT 'blue',
    mode ENUM('light','dark') NOT NULL DEFAULT 'light',
    language VARCHAR(20) NOT NULL DEFAULT 'en',
    session_timeout SMALLINT UNSIGNED NOT NULL DEFAULT 15,
    active_session_id VARCHAR(128) NULL,
    status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

CREATE TABLE user_profiles (
    user_id INT UNSIGNED PRIMARY KEY,
    address TEXT NULL,
    bank_name VARCHAR(120) NULL,
    bank_account VARCHAR(60) NULL,
    ifsc VARCHAR(20) NULL,
    emergency_name VARCHAR(120) NULL,
    emergency_mobile VARCHAR(30) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(60) NULL,
    user_agent VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ended_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE otp_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    mobile VARCHAR(30) NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    purpose ENUM('login','forgot') NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(120) NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(60) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE login_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    session_id VARCHAR(128) NULL,
    ip_address VARCHAR(60) NULL,
    user_agent VARCHAR(255) NULL,
    status VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE remember_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    selector VARCHAR(64) NOT NULL UNIQUE,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO firm_settings (name, short_name, logo, favicon, email, phone, address, website)
VALUES ('KCI Chartered Accountants', 'KCI Tax', '../../assets/img/logo.svg', '../../assets/img/favicon.svg', 'office@example.com', '+91 98765 43210', 'Main Road, Business District', 'www.example.com');

INSERT INTO roles (name, slug) VALUES
('Admin', 'admin'),
('Manager', 'manager'),
('Executive', 'executive'),
('Accountant', 'accountant'),
('Data Entry', 'data-entry'),
('Viewer', 'viewer');

INSERT INTO permissions (name, slug, module) VALUES
('View Dashboard', 'dashboard.view', 'dashboard'),
('Manage Users', 'users.manage', 'users'),
('Manage Settings', 'settings.manage', 'settings'),
('View Reports', 'reports.view', 'reports'),
('View Profile', 'profile.view', 'profile'),
('Update Profile', 'profile.update', 'profile'),
('Change Own Password', 'profile.password', 'profile');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'admin';

INSERT INTO settings (setting_key, setting_value) VALUES
('otp_login_template', 'OTP Code: {{123456}}. This is your OTP for login to {{$firm_name}}. The OTP is valid for 10 minutes. Call {{$firm_number}} if you did not perform this request. For your security, do not share this code.'),
('forgot_otp_template', '{{123456}} is your password recovery code. Do not share this code. Call {{$firm_number}} for assistance.');
