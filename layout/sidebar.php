<?php
$user = current_user();
$currentPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isDashboard = str_contains($currentPath, '/modules/dashboard/');
$isProfile = str_contains($currentPath, '/modules/profile/');
?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="<?= e($firm['logo'] ?? asset('img/logo.svg')) ?>" alt="Firm logo">
        <div>
            <strong><?= e($firm['short_name'] ?? 'TaxFirm') ?></strong>
            <span><?= e($firm['name'] ?? 'Tax Consultant') ?></span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a class="<?= $isDashboard ? 'active' : '' ?>" href="<?= e(app_url('modules/dashboard/index.php')) ?>"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
        <a href="#"><i class="fa-solid fa-users"></i><span>Clients</span></a>
        <a href="#"><i class="fa-solid fa-file-invoice"></i><span>GST</span></a>
        <a href="#"><i class="fa-solid fa-receipt"></i><span>ITR</span></a>
        <a href="#"><i class="fa-solid fa-wallet"></i><span>Fees</span></a>
        <a href="#"><i class="fa-solid fa-user-shield"></i><span>Roles</span></a>
        <a class="<?= $isProfile ? 'active' : '' ?>" href="<?= e(app_url('modules/profile/index.php')) ?>"><i class="fa-solid fa-user-gear"></i><span>Profile</span></a>
        <a href="#"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </nav>
</aside>
