<?php
require_once __DIR__ . '/../config/storefront_db.php';

$products = array();
$pageError = '';
try {
    $products = storefront_rows(storefront_execute(
        storefront_db(),
        'SELECT p.id, p.productName, p.short_description, p.product_category, p.price, p.discount_price, '
        . 'COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
        . 'FROM products p ORDER BY p.created_at DESC, p.id DESC'
    ));
} catch (Throwable $exception) {
    error_log('Product listing failed.');
    $pageError = 'Products are temporarily unavailable. Please try again later.';
}
$flash = storefront_take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<section class="products-page">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <div class="section-title">
                    <h2>All Products</h2>
                </div>
                <p class="products-page-intro">Browse our available drones and accessories.</p>
                <!-- <a class="button products-cart-link" href="?route=cart">View Cart</a> -->
            </div>
        </div>
        <?php if ($flash): ?>
            <div class="row">
                <div class="col-12 products-page-message">
                    <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
                        <?php echo storefront_escape($flash['message']); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pageError !== ''): ?>
            <div class="products-page-empty" role="alert"><?php echo storefront_escape($pageError); ?></div>
        <?php elseif (!$products): ?>
            <div class="products-page-empty">No products are available right now.</div>
        <?php else: ?>
            <div class="row products-grid">
                <?php foreach ($products as $product): ?>
                    <?php
                    $price = storefront_product_price($product);
                    $regularPrice = isset($product['price']) ? (float) $product['price'] : 0;
                    $stock = (int) $product['stock_quantity'];
                    ?>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 products-grid-column">
                        <article class="single-tranding products-page-card">
                            <a href="?route=product-details&amp;id=<?php echo (int) $product['id']; ?>">
                                <div class="tranding-pro-img">
                                    <img src="<?php echo storefront_escape(storefront_image_url($product['image_url'])); ?>"
                                         alt="<?php echo storefront_escape($product['productName'] ?: 'Drone'); ?>">
                                </div>
                                <div class="tranding-pro-title">
                                    <h3><?php echo storefront_escape($product['productName'] ?: 'Drone'); ?></h3>
                                    <h4><?php echo storefront_escape($product['product_category'] ?: 'Drone'); ?></h4>
                                </div>
                                <div class="tranding-pro-price">
                                    <div class="price_box">
                                        <span class="current_price">$<?php echo number_format($price, 2); ?></span>
                                        <?php if ($regularPrice > $price): ?>
                                            <span class="old_price">$<?php echo number_format($regularPrice, 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            <p class="products-stock <?php echo $stock > 0 ? 'is-available' : 'is-unavailable'; ?>">
                                <?php echo $stock > 0 ? $stock . ' in stock' : 'Out of stock'; ?>
                            </p>
                            <?php if ($stock > 0): ?>
                                <form class="products-add-form" action="?route=cart-action" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="button" type="submit">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
