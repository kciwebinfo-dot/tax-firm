<?php $pageTitle = $pageTitle ?? 'Dashboard'; ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(app_url('modules/dashboard/index.php')) ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($pageTitle) ?></li>
    </ol>
</nav>
