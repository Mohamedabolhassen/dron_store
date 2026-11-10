<?php include_once __DIR__ . '/../inc/header.php'; ?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul>
                <li><a href="?route=home">Home</a></li>
                <li><a href="?route=products">Products</a></li>
                <li><?= escapeHtml($product['name']) ?></li>
            </ul>
        </div></div></div>
    </div>
</div>

<section class="product_details mt-60 mb-60">
    <div class="container">
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="product-details-tab">
                    <div class="zoomWrapper single-zoom">
                        <img class="img-fluid" src="<?= escapeHtml(productImageUrl($product['image_url'])) ?>"
                             alt="<?= escapeHtml($product['name']) ?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="product_d_right">
                    <h1><?= escapeHtml($product['name']) ?></h1>
                    <div class="price_box">
                        <span class="current_price">$<?= number_format((float) $product['price'], 2) ?></span>
                    </div>
                    <div class="product_desc">
                        <p><?= nl2br(escapeHtml($product['description'] ?? '')) ?></p>
                        <p><?= (int) $product['stock'] > 0
                            ? 'Available stock: ' . (int) $product['stock']
                            : 'Currently out of stock' ?></p>
                    </div>
                    <?php if ((int) $product['stock'] > 0): ?>
                        <form action="?route=AddToCart" method="post">
                            <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <div class="product_variant quantity">
                                <label for="quantity">Quantity</label>
                                <input id="quantity" name="qty" min="1" max="<?= (int) $product['stock'] ?>"
                                       value="1" type="number" required>
                                <button class="button" type="submit">Add to cart</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    <form action="?route=AddToWishlist" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button class="button" type="submit">Add to wishlist</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
