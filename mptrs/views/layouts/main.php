<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= APP_NAME ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/header.php'; ?>
<div class="d-flex">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="flex-grow-1 p-4 content-area">
        <?php if ($msg = \Session::flash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($msg) ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($msg = \Session::flash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($msg) ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>