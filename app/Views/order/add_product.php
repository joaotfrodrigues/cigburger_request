<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-5">
            <div class="row">
                <div class="col-lg-6 col-12 text-center p-3">
                    <img src="<?= API_IMAGES_URL . $product['image'] ?>" alt="<?= $product['image'] ?>">
                    <p class="order-product-title-large"><?= $product['name'] ?></p>
                    <p class="order-product-description"><?= $product['description'] ?></p>
                </div>

                <div class="col-lg-6 col-12 text-center">
                    <div class="d-flex justify-content-center align-items-center border rounded-5 bg-light p-4 gap-2">
                        <?php if($product['has_promotion']): ?>
                            <span class="order-product-old-price"><?= format_currency($product['old_price']) ?></span>
                            <span class="order-product-price mx-1"><?= format_currency($product['price']) ?></span>
                            <span class="discount-percentage"><?= number_format($product['promotion'], 0) . '%' ?></span>
                            <?php else: ?>
                                <span class="order-product-price"><?= format_currency($product['price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="row my-5">
                        <div class="col-lg-4 col-12 text-end p-0">
                            <button id="decrease_quantity" class="col-12 cig-primary px-5">
                                <h3><i class="fa-solid fa-minus"></i></h3>
                            </button>
                        </div>
                        <div class="col-lg-4 col-12 text-center align-items-center p-0">
                            <p class="col-12 order-product-quantity"><?= $quantity ?></p>
                        </div>
                        <div class="col-lg-4 col-12 text-start p-0">
                            <button id="increase_quantity" class="col-12 cig-primary px-5">
                                <h3><i class="fa-solid fa-plus"></i></h3>
                            </button>
                        </div>
                    </div>

                    <div class="row my-3 d-flex justify-content-between gap-3">
                        <a href="<?= site_url('/order') ?>" class="cig-primary col-lg-5 col-12"><h3><i class="fa-solid fa-ban me-3"></i>Cancelar</h3></a>
                        <button id="accept" class="cig-primary col-lg-5 col-12">
                            <h3><i class="fa-solid fa-check me-3"></i>Aceitar</h3>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const decreaseBtn = document.getElementById('decrease_quantity');
    const increaseBtn = document.getElementById('increase_quantity');
    const addProductBtn = document.getElementById('accept');
    const productQuantity = document.querySelector('.order-product-quantity');
    
    const maxQuantity = <?= MAX_QUANTITY_PER_PRODUCT ?>;
    var quantity = <?= $quantity ?>;

    decreaseBtn.addEventListener('click', () => {
        if (quantity > 0) {
            quantity--;
            productQuantity.textContent = quantity;
        }
    });

    increaseBtn.addEventListener('click', () => {
        if (quantity < maxQuantity) {
            quantity++;
            productQuantity.textContent = quantity;
        }
    });

    addProductBtn.addEventListener('click', () => {
        window.location.href = `<?= site_url('order/add_product_confirm/' . Encrypt($product['id'])) ?>/${quantity}`;
    });
</script>

<?= $this->endSection() ?>