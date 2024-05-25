<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-4">

            <div class="mb-5">
                <?= $this->include('order/order_categories') ?>
            </div>

            <div class="mb-5">
                <?= $this->include('order/order_info') ?>
            </div>

            <div class="mb-5">
                <?= $this->include('order/products') ?>
            </div>

            <div class="d-flex gap-5 justify-content-center flex-wrap mt-4 mb-5">
                <h4><a href="<?= site_url('/order/cancel') ?>" class="cig-primary p-4"><i class="fa-solid fa-ban me-3 mt-4"></i>Cancelar pedido</a></h4>
                <h4><a href="<?= site_url('/order/checkout') ?>" class="cig-primary p-4"><i class="fa-solid fa-share me-3 mt-4"></i>Finalizar pedido</a></h4>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>