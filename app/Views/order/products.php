<?php if ($products) : ?>
    <div class="container-fluid">
        <div class="row">
            <?php foreach ($products as $product) : ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <a href="#" class="no-link">
                        <div class="order-product text-center">
                            <img src="<?= API_IMAGES_URL . $product['image'] ?>" alt="<?= $product['image'] ?>" class="img-fluid" width="160">
                            <p class="order-product-title"><?= $product['name'] ?></p>
                            <div class="d-flex justify-content-center align-items-center">
                                <?php if ($product['out_of_stock']): ?>
                                    <span class="product-out-of-stock">Indisponível</span>
                                <?php else: ?>
                                    <?php if ($product['has_promotion']): ?>
                                        <span class="order-product-old-price"><?= format_currency($product['old_price']) ?></span>
                                        <span class="order-product-price mx-1"><?= format_currency($product['price']) ?></span>
                                        <span class="discount-percentage"><?= number_format($product['promotion'], 0) ?>%</span>
                                    <?php else: ?>
                                        <p class="order-product-price"><?= format_currency($product['price']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>