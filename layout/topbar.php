<?php
$user = current_user();
$notifications = latest_notifications();
?>
<header class="topbar">
    <button class="icon-btn d-lg-none" id="sidebarToggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
    <div class="global-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" placeholder="Search clients, returns, invoices...">
    </div>
    <div class="topbar-actions">
        <button class="icon-btn" id="modeToggle" title="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>
        <select class="form-select form-select-sm theme-select" id="themeSelect" title="Theme">
            <?php foreach (available_themes() as $theme): ?>
                <option value="<?= e($theme) ?>" <?= active_theme() === $theme ? 'selected' : '' ?>><?= e(ucwords(str_replace('-', ' ', $theme))) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="dropdown">
            <button class="icon-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-bell"></i></button>
            <div class="dropdown-menu dropdown-menu-end notification-menu">
                <?php if (!$notifications): ?>
                    <span class="dropdown-item-text text-muted">No notifications</span>
                <?php endif; ?>
                <?php foreach ($notifications as $notification): ?>
                    <span class="dropdown-item-text">
                        <strong><?= e($notification['title']) ?></strong>
                        <small><?= e($notification['message']) ?></small>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="dropdown">
            <button class="profile-chip" data-bs-toggle="dropdown" aria-expanded="false">
                <span><?= e(user_initials($user['name'] ?? 'User')) ?></span>
                <strong><?= e($user['name'] ?? 'User') ?></strong>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= e(app_url('modules/profile/index.php')) ?>">My Account</a></li>
                <li><a class="dropdown-item" href="#">Activity</a></li>
                <li><a class="dropdown-item" href="#">Wallet</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= e(app_url('modules/auth/logout.php')) ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</header>
