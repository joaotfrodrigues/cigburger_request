<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-5">

            <?php if ($total_products == 0) : ?>
                <h4 class="p-5 text-secondary text-center">Não existem produtos no pedido.</h4>

                <div class="text-center mt-5">
                    <h4>
                        <a href="<?= site_url('/order') ?>" class="cig-primary">
                            <i class="fa-solid fa-chevron-left me-2"></i>Voltar
                        </a>
                    </h4>
                </div>
            <?php else : ?>
                <h4 class="text-secondary">Produtos do pedido</h4>

                <hr>

                <?php foreach ($order_products as $product) : ?>
                    <div class="row">
                        <div class="col-lg-4 col-12 d-flex align-items-center">
                            <span id="btn-remove-<?= Encrypt($product['id']) ?>" class="cig-primary px-3" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                            <a href="<?= site_url('/order/add_product/' . Encrypt($product['id'])) ?>" class="cig-primary px-3"><i class="fa-solid fa-gear"></i></a>

                            <img src="<?= API_IMAGES_URL . $product['image'] ?>" alt="<?= $product['image'] ?>" class="img-fluid" width="100">
                            <p class="order-product-title mb-0"><?= $product['name'] ?></p>
                        </div>
                        <div class="col-lg-4 col-7 d-flex align-items-center justify-content-center">
                            <h3>
                                <?= $product['has_promotion'] ? '<span class="discount-percentage">' . number_format($product['promotion'], 0) . '%</span>' : '' ?>
                                <?= $product['quantity'] ?> <span class="text-secondary">x</span> <?= format_currency($product['price']) ?>
                            </h3>
                        </div>
                        <div class="col-lg-4 col-5 d-flex align-items-center justify-content-end">
                            <h3><?= format_currency($product['total_price']) ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr>

                <div class="row">
                    <div class="col-8 text-end">
                        <h3>Total</h3>
                    </div>
                    <div class="col-4 text-end">
                        <h3><?= format_currency($total_price) ?></h3>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap my-5">
                    <h4><a href="<?= site_url('/order/cancel') ?>" class="cig-primary"><i class="fa-solid fa-ban me-3 my-4"></i>Cancelar pedido</a></h4>
                    <h4><a href="<?= site_url('/order') ?>" class="cig-primary"><i class="fa-solid fa-chevron-left me-3 my-4"></i>Voltar</a></h4>
                    <h4><a href="<?= site_url('/order/checkout_payment') ?>" class="cig-primary"><i class="fa-regular fa-credit-card me-3 my-4"></i>Finalizar pedido</a></h4>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>


<!-- Remove item modal -->
<div class="modal fade" id="modal-remove-item" tabindex="-1" aria-labelledby="modal-remove-item-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Remover item</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h1 class="text-danger text-center">
                    <i class="fa-solid fa-exclamation-circle me-2"></i>
                </h1>
                <h5 class="text-center">
                    Deseja remover o item do pedido?
                </h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="cig-primary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-3"></i>Não</button>
                <button type="button" class="cig-primary" id="btn-confirm"><i class="fa-solid fa-check me-3"></i>Sim</button>
            </div>
        </div>
    </div>
</div>

<script>
    var id = null;
    
    var removeButtons = document.querySelectorAll('[id^="btn-remove-"]');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            id = btn.id.split('-')[2];
            
            let modal = new bootstrap.Modal(document.getElementById('modal-remove-item'));
            modal.show();
        });
    });

    var confirmButton = document.getElementById('btn-confirm');
    confirmButton.addEventListener('click', () => {
        window.location.href = `<?= site_url('/order/remove_product/') ?>/${id}`;
    });
</script>

<?= $this->endSection() ?>