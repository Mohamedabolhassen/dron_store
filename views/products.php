<?php
$productsResult = productAll($con);
$productsLoadError = !$productsResult;
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>Products</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <div class="row"><div class="col-12">
            <div class="section-title text-center"><h2>Our Products</h2></div>
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if ($productsLoadError): ?>
                <p>Products are temporarily unavailable. Please try again later.</p>
            <?php elseif (mysqli_num_rows($productsResult) === 0): ?>
                <p>No products are available right now.</p>
            <?php else: ?>
                <div class="row justify-content-center">
                    <?php while ($product = mysqli_fetch_assoc($productsResult)): ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                            <article class="single-tranding mb-30">
                                <a href="?route=product-details&amp;id=<?= (int) $product['id'] ?>">
                                    <div class="tranding-pro-img">
                                        <img src="<?= escapeHtml(productImageUrl($product['image_url'])) ?>"
                                             alt="<?= escapeHtml($product['name']) ?>">
                                    </div>
                                    <div class="tranding-pro-title">
                                        <h3><?= escapeHtml($product['name']) ?></h3>
                                    </div>
                                    <div class="tranding-pro-price">
                                        <div class="price_box">
                                            <span class="current_price">$<?= number_format((float) $product['price'], 2) ?></span>
                                        </div>
                                    </div>
                                </a>
                                <p><?= (int) $product['stock'] > 0
                                    ? 'In stock: ' . (int) $product['stock']
                                    : 'Out of stock' ?></p>
                                <?php if ((int) $product['stock'] > 0): ?>
                                    <form action="?route=AddToCart" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <input type="hidden" name="qty" value="1">
                                        <button class="button" type="submit">Add to cart</button>
                                    </form>
                                <?php endif; ?>
                                <form action="?route=AddToWishlist" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <button class="button" type="submit">Add to wishlist</button>
                                </form>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div></div>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
