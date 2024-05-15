<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-4">

            <!-- categories -->
            <?= $this->include('order/order_categories') ?>

            <div class="text-center mt-5">
                <h3><?= $selected_category ?></h3>
            </div>

            <div class="my-5">
                <?= $this->include('order/products') ?>
            </div>

            <div class="text-center my-3">
                <a href="<?= site_url('/order/cancel') ?>">Cancelar pedido</a>
                <span class="mx-5">|</span>
                <a href="<?= site_url('/order/checkout') ?>">Finalizar pedido</a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>