<?php
/**
 * User theme helpers.
 */
declare(strict_types=1);

function active_theme(): string
{
    return current_user()['theme'] ?? 'blue';
}

function active_mode(): string
{
    return current_user()['mode'] ?? 'light';
}

function available_themes(): array
{
    return ['blue', 'purple', 'green', 'red', 'orange', 'indigo', 'teal', 'emerald', 'pink', 'slate', 'dark-navy', 'black'];
}
