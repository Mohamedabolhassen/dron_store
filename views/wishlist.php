<?php
require_once __DIR__ . '/../config/storefront_db.php';
storefront_require_customer('Please sign in to view your wishlist.');

$products = array();
$pageError = '';
try {
    $products = storefront_rows(storefront_execute(
        storefront_db(),
        'SELECT p.id, p.productName, p.price, p.discount_price, COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
        . 'FROM wishlists w JOIN products p ON p.id = w.product_id '
        . 'WHERE w.user_id = ? ORDER BY w.created_at DESC',
        'i',
        array((int) $_SESSION['user_id'])
    ));
} catch (Throwable $exception) {
    error_log('Wishlist display failed.');
    $pageError = 'Your wishlist is temporarily unavailable.';
}
$flash = storefront_take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Wishlist | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <h1>My wishlist</h1>
    <?php if ($flash): ?>
        <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
            <?php echo storefront_escape($flash['message']); ?>
        </p>
    <?php endif; ?>
    <?php if ($pageError !== ''): ?>
        <p role="alert"><?php echo storefront_escape($pageError); ?></p>
    <?php elseif (!$products): ?>
        <p>Your wishlist is empty. <a href="?route=products">Browse products</a>.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6" style="margin-bottom: 25px;">
                    <a href="?route=product-details&amp;id=<?php echo (int) $product['id']; ?>">
                        <img src="<?php echo storefront_escape(storefront_image_url($product['image_url'])); ?>"
                             alt="<?php echo storefront_escape($product['productName']); ?>"
                             style="max-width:100%;height:auto;">
                        <h2><?php echo storefront_escape($product['productName'] ?: 'Drone'); ?></h2>
                    </a>
                    <p>$<?php echo number_format(storefront_product_price($product), 2); ?></p>
                    <form action="?route=cart-action" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                        <input type="hidden" name="action" value="wishlist-remove">
                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                        <button type="submit">Remove from wishlist</button>
                    </form>
                    <?php if ((int) $product['stock_quantity'] > 0): ?>
                        <form action="?route=cart-action" method="post" style="margin-top: 8px;">
                            <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit">Add to cart</button>
                        </form>
                    <?php else: ?>
                        <p>Out of stock</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
