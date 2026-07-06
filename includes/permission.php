<?php
/**
 * Lightweight RBAC helpers. Database-backed permissions are ready for future modules.
 */
declare(strict_types=1);

function can(string $permission): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }

    if (($user['role'] ?? '') === 'Admin') {
        return true;
    }

    return in_array($permission, $_SESSION['permissions'] ?? [], true);
}
