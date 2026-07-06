<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../../layout/header.php';

$stats = [
    ['label' => "Today's Collection", 'value' => 'Rs. 48,500', 'icon' => 'fa-indian-rupee-sign', 'trend' => '+12%'],
    ['label' => 'Pending Fees', 'value' => 'Rs. 2.8L', 'icon' => 'fa-wallet', 'trend' => '-4%'],
    ['label' => 'GST Due', 'value' => '36', 'icon' => 'fa-file-invoice', 'trend' => 'This week'],
    ['label' => 'ITR Due', 'value' => '128', 'icon' => 'fa-receipt', 'trend' => 'This month'],
];
?>
<?php require __DIR__ . '/../../layout/sidebar.php'; ?>
<div class="main-panel">
<?php require __DIR__ . '/../../layout/topbar.php'; ?>
<main class="content">
    <?php require __DIR__ . '/../../layout/breadcrumbs.php'; ?>
    <div class="dashboard-grid">
        <?php foreach ($stats as $stat): ?>
            <article class="metric-card">
                <div>
                    <span><?= e($stat['label']) ?></span>
                    <strong><?= e($stat['value']) ?></strong>
                    <small><?= e($stat['trend']) ?></small>
                </div>
                <i class="fa-solid <?= e($stat['icon']) ?>"></i>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-xl-8">
            <section class="panel">
                <div class="panel-header">
                    <h2>Monthly Collection</h2>
                    <button class="btn btn-sm btn-outline-primary">Export</button>
                </div>
                <div id="collectionChart" class="chart-box"></div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="panel">
                <div class="panel-header"><h2>Status Mix</h2></div>
                <div id="statusChart" class="chart-box"></div>
            </section>
        </div>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-xl-7">
            <section class="panel">
                <div class="panel-header"><h2>Today's Work</h2></div>
                <table class="table align-middle datatable">
                    <thead><tr><th>Client</th><th>Task</th><th>Status</th><th>Due</th></tr></thead>
                    <tbody>
                        <tr><td>Aarav Traders</td><td>GSTR-3B Review</td><td><span class="badge text-bg-warning">Pending</span></td><td>Today</td></tr>
                        <tr><td>Mehta Foods</td><td>ITR Draft</td><td><span class="badge text-bg-success">Ready</span></td><td>Tomorrow</td></tr>
                        <tr><td>Prime Textiles</td><td>Fee Follow-up</td><td><span class="badge text-bg-info">Called</span></td><td>Today</td></tr>
                    </tbody>
                </table>
            </section>
        </div>
        <div class="col-xl-5">
            <section class="panel">
                <div class="panel-header"><h2>Recent Activities</h2></div>
                <ul class="activity-list">
                    <li><i class="fa-solid fa-check"></i><span>GST return marked ready for Aarav Traders</span></li>
                    <li><i class="fa-solid fa-bell"></i><span>Fee reminder sent to Prime Textiles</span></li>
                    <li><i class="fa-solid fa-user"></i><span>New client profile created</span></li>
                </ul>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
