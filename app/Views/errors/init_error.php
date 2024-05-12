<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-center mt-3">
    <div class="alert alert-warning p-4 text-center shadow border border-danger">
        <h3 class="mb-3">
            <i class="fa-solid fa-triangle-exclamation text-danger"></i>
            <span class="d-block mt-2">
                <?= $error ?>
            </span>
        </h3>
    </div>
</div>
<?= $this->endSection() ?>