<?php
/**
 * Notification query helper.
 */
declare(strict_types=1);

function latest_notifications(int $limit = 5): array
{
    if (empty($_SESSION['user']['id'])) {
        return [];
    }

    $stmt = db()->prepare('SELECT title, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, (int) $_SESSION['user']['id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
