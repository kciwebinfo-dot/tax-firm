<?php
/**
 * Run once after importing schema.sql to create demo records with a valid PHP password hash.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = db();
$pdo->beginTransaction();

try {
    $role = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
    $role->execute(['admin']);
    $adminRoleId = (int) $role->fetchColumn();

    $user = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $user->execute(['admin']);
    $adminId = (int) $user->fetchColumn();

    if (!$adminId) {
        $insert = $pdo->prepare('INSERT INTO users (role_id, name, username, email, mobile, password_hash, theme, mode, session_timeout) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([
            $adminRoleId,
            'Admin User',
            'admin',
            'admin@example.com',
            '+919876543210',
            password_hash('Admin@12345', PASSWORD_DEFAULT),
            'blue',
            'light',
            15,
        ]);
        $adminId = (int) $pdo->lastInsertId();
    }

    $profile = $pdo->prepare('INSERT IGNORE INTO user_profiles (user_id, address, bank_name, bank_account, ifsc, emergency_name, emergency_mobile) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $profile->execute([$adminId, 'Main Road, Business District', 'Demo Bank', '0000000000', 'DEMO0001234', 'Office Manager', '+919900000000']);

    $notification = $pdo->prepare('INSERT INTO notifications (user_id, title, message) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM notifications WHERE user_id = ? AND title = ?)');
    $notification->execute([$adminId, 'Welcome', 'Your tax portal foundation is ready.', $adminId, 'Welcome']);

    $pdo->commit();
    echo 'Seed completed. Demo login: admin / Admin@12345';
} catch (Throwable $exception) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Seed failed: ' . $exception->getMessage();
}
