<?php
require_once __DIR__ . '/../config/storefront_db.php';

$productId = filter_var(isset($_GET['id']) ? $_GET['id'] : null, FILTER_VALIDATE_INT);
$product = null;
$pageError = '';

try {
    if ($productId) {
        $product = storefront_row(storefront_execute(
            storefront_db(),
            'SELECT p.id, p.productName, p.sku, p.short_description, p.Description, p.price, p.discount_price, '
            . 'COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity, p.product_category, '
            . '(SELECT image_url FROM product_images WHERE product_id = p.id '
            . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
            . 'FROM products p WHERE p.id = ? LIMIT 1',
            'i',
            array($productId)
        ));
    } else {
        $product = storefront_row(storefront_execute(
            storefront_db(),
            'SELECT p.id, p.productName, p.sku, p.short_description, p.Description, p.price, p.discount_price, '
            . 'COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity, p.product_category, '
            . '(SELECT image_url FROM product_images WHERE product_id = p.id '
            . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
            . 'FROM products p ORDER BY p.created_at DESC, p.id DESC LIMIT 1'
        ));
    }
} catch (Throwable $exception) {
    error_log('Product detail failed.');
    $pageError = 'This product is temporarily unavailable.';
}

$flash = storefront_take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo storefront_escape($product ? $product['productName'] : 'Product details'); ?> | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <p><a href="?route=products">← All products</a> | <a href="?route=cart">View cart</a></p>
    <?php if ($flash): ?>
        <p role="status"><?php echo storefront_escape($flash['message']); ?></p>
    <?php endif; ?>
    <?php if ($pageError !== ''): ?>
        <p role="alert"><?php echo storefront_escape($pageError); ?></p>
    <?php elseif (!$product): ?>
        <h1>Product not found</h1>
        <p>There are no products to show yet. <a href="?route=products">Browse products</a>.</p>
    <?php else: ?>
        <?php $price = storefront_product_price($product); ?>
        <div class="row">
            <div class="col-lg-6">
                <img src="<?php echo storefront_escape(storefront_image_url($product['image_url'])); ?>"
                     alt="<?php echo storefront_escape($product['productName']); ?>"
                     style="max-width:100%;height:auto;">
            </div>
            <div class="col-lg-6">
                <h1><?php echo storefront_escape($product['productName'] ?: 'Drone'); ?></h1>
                <?php if (!empty($product['sku'])): ?><p>SKU: <?php echo storefront_escape($product['sku']); ?></p><?php endif; ?>
                <?php if (!empty($product['product_category'])): ?><p>Category: <?php echo storefront_escape($product['product_category']); ?></p><?php endif; ?>
                <p><strong>$<?php echo number_format($price, 2); ?></strong></p>
                <p><?php echo (int) $product['stock_quantity'] > 0
                    ? (int) $product['stock_quantity'] . ' in stock'
                    : 'Out of stock'; ?></p>
                <?php if (!empty($product['short_description'])): ?>
                    <p><?php echo nl2br(storefront_escape($product['short_description'])); ?></p>
                <?php endif; ?>
                <?php if ((int) $product['stock_quantity'] > 0): ?>
                    <form action="?route=cart-action" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" name="quantity" type="number" min="1"
                               max="<?php echo min(99, (int) $product['stock_quantity']); ?>" value="1" required>
                        <button class="button" type="submit">Add to cart</button>
                    </form>
                <?php endif; ?>
                <form action="?route=cart-action" method="post" style="margin-top: 12px;">
                    <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                    <input type="hidden" name="action" value="wishlist-add">
                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                    <button class="button" type="submit">Save to wishlist</button>
                    <?php if (empty($_SESSION['user_id'])): ?>
                        <small>Sign in is required to use a wishlist.</small>
                    <?php endif; ?>
                </form>
                <?php if (!empty($product['Description'])): ?>
                    <section style="margin-top: 30px;">
                        <h2>Description</h2>
                        <div><?php echo nl2br(storefront_escape($product['Description'])); ?></div>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
