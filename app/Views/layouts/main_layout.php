<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/bootstrap.min.css') ?>">

    <!-- fontawesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/all.min.css') ?>">
</head>

<body>
    <!-- logo -->
    <div class="d-flex justify-content-center p-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Cig Request Logo" width="120">
    </div>

    <!-- content -->
    <?= $this->renderSection('content') ?>

    <!-- bootstrap -->
    <script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>