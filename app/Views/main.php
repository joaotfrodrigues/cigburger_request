<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-5 text-center">
            <h3 class="mb-5">Bem-vindo ao <span><strong class="cig-title">Cig Burger</strong></span></h3>
            <h4 class="mb-5 cig-punchline">Hambúrgueres com alma e sabor</h4>
            <div class="d-flex justify-content-center">
                <a href="<?= site_url('/order') ?>" class="cig-primary">
                    <h3 class="p-5">
                        <i class="fa-solid fa-utensils me-3"></i>Iniciar pedido
                    </h3>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>