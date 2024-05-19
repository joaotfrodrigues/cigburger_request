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

    <!-- custom css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">

    <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" type="image/png">
</head>

<body>
    <!-- logo -->
    <div class="d-flex justify-content-center p-3">
        <a href="<?= site_url('order') ?>">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Cig Request Logo" width="120">
        </a>
    </div>

    <!-- content -->
    <?= $this->renderSection('content') ?>

    <!-- footer -->
    <footer class="my-5 text-center text-muted">
        <i class="fa-solid fa-burger me-2"></i>Cigburger &copy; <?= date('Y') ?>
    </footer>

    <!-- bootstrap -->
    <script src="<?= base_url('assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>