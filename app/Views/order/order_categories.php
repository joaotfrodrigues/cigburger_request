<?php if (isset($categories)): ?>
    <div class="d-flex justify-content-center flex-col flex-wrap gap-1">
        <?php foreach($categories as $category): ?>
            <a class="category-link category col-lg-3 col-md-5 col-12" href="<?= site_url('/order/set_filter/' . Encrypt($category['category'])) ?>">
                <img src="<?= base_url('assets/images/categories/' . strtolower($category['category'])) . '.png' ?>" alt="<?= $category['category'] ?>">
                <p class="p-0 m-0 ms-2"><?= $category['category'] ?></p>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>