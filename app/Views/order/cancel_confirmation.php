<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-5">
            <div class="text-center">
                
                <h4 class="my-5 text-center">Deseja cancelar o seu pedido?</h4>
                
                <div class="d-flex justify-content-center gap-3 mb-5">
                    <h5><a href="<?= site_url('/order') ?>" class="cig-primary"><i class="fa-solid fa-times me-2"></i>Não</a></h5>
                    <h5><a href="<?= site_url('/') ?>" class="cig-primary"><i class="fa-solid fa-check me-2"></i>Sim</a></h5>
                </div>

                <div class="text-center">
                    Produtos no pedido: <strong><?= $total_items ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>