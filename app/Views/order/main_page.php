<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-4">

        <?= $this->include('order/order_categories') ?>

        <!-- categories -->
        <div class="text-center my-3">
            produtos
        </div>

        <div class="text-center my-3">
            cancelar | finalizar
        </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>