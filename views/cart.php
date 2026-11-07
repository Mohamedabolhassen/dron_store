<?php
require_once __DIR__ . '/../config/storefront_db.php';

$items = array();
$pageError = '';
$subtotal = 0;
try {
    $connection = storefront_db();
    $cartId = storefront_cart_id($connection, false);
    $items = storefront_cart_rows($connection, $cartId);
    $subtotal = storefront_cart_subtotal($items);
} catch (Throwable $exception) {
    error_log('Cart display failed.');
    $pageError = 'Your cart is temporarily unavailable.';
}
$flash = storefront_take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <h1>Your cart</h1>
    <?php if ($flash): ?>
        <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
            <?php echo storefront_escape($flash['message']); ?>
        </p>
    <?php endif; ?>
    <?php if ($pageError !== ''): ?>
        <p role="alert"><?php echo storefront_escape($pageError); ?></p>
    <?php elseif (!$items): ?>
        <p>Your cart is empty. <a href="?route=products">Browse products</a>.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <?php $price = storefront_product_price($item); ?>
                        <tr>
                            <td>
                                <a href="?route=product-details&amp;id=<?php echo (int) $item['product_id']; ?>">
                                    <img src="<?php echo storefront_escape(storefront_image_url($item['image_url'])); ?>"
                                         alt="" style="width:60px;height:auto;">
                                    <?php echo storefront_escape($item['productName'] ?: 'Drone'); ?>
                                </a>
                            </td>
                            <td>$<?php echo number_format($price, 2); ?></td>
                            <td>
                                <form action="?route=cart-action" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="item_id" value="<?php echo (int) $item['cart_item_id']; ?>">
                                    <input type="number" name="quantity" min="1"
                                           max="<?php echo min(99, (int) $item['stock_quantity']); ?>"
                                           value="<?php echo (int) $item['quantity']; ?>" required>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($price * (int) $item['quantity'], 2); ?></td>
                            <td>
                                <form action="?route=cart-action" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?php echo (int) $item['cart_item_id']; ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p><strong>Subtotal: $<?php echo number_format($subtotal, 2); ?></strong></p>
        <p>Shipping: Free</p>
        <p>Total: $<?php echo number_format($subtotal, 2); ?></p>
        <p><a class="button" href="?route=checkout">Proceed to checkout</a></p>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
