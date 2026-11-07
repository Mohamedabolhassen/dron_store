<?php
require_once __DIR__ . '/../config/storefront_db.php';
storefront_require_customer();

$items = array();
$pageError = '';
try {
    $connection = storefront_db();
    $cartId = storefront_cart_id($connection, false);
    $items = storefront_cart_rows($connection, $cartId);
} catch (Throwable $exception) {
    error_log('Checkout cart load failed.');
    $pageError = 'Your cart is temporarily unavailable.';
}

if (!$items && $pageError === '') {
    storefront_flash('error', 'Your cart is empty.');
    header('Location: ?route=cart');
    exit;
}

$flash = storefront_take_flash();
$old = isset($_SESSION['checkout_old']) ? $_SESSION['checkout_old'] : array();
$subtotal = storefront_cart_subtotal($items);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <h1>Checkout</h1>
    <p>Signed in as <?php echo storefront_escape(isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''); ?>.
       <a href="?route=cart">Return to cart</a></p>
    <?php if ($flash): ?>
        <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
            <?php echo storefront_escape($flash['message']); ?>
        </p>
    <?php endif; ?>
    <?php if ($pageError !== ''): ?>
        <p role="alert"><?php echo storefront_escape($pageError); ?></p>
    <?php else: ?>
        <form action="?route=checkout" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
            <h2>Shipping address</h2>
            <p><label for="recipient_name">Recipient name *</label><br>
                <input id="recipient_name" name="recipient_name" maxlength="150" required
                       value="<?php echo storefront_form_value($old, 'recipient_name', isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''); ?>"></p>
            <p><label for="phone">Phone *</label><br>
                <input id="phone" name="phone" type="tel" maxlength="30" required value="<?php echo storefront_form_value($old, 'phone'); ?>"></p>
            <p><label for="country">Country *</label><br>
                <input id="country" name="country" maxlength="100" required value="<?php echo storefront_form_value($old, 'country', 'Egypt'); ?>"></p>
            <p><label for="city">City *</label><br>
                <input id="city" name="city" maxlength="100" required value="<?php echo storefront_form_value($old, 'city'); ?>"></p>
            <p><label for="street_address">Street address *</label><br>
                <textarea id="street_address" name="street_address" maxlength="255" required><?php echo storefront_form_value($old, 'street_address'); ?></textarea></p>
            <p><label for="postal_code">Postal code</label><br>
                <input id="postal_code" name="postal_code" maxlength="20" value="<?php echo storefront_form_value($old, 'postal_code'); ?>"></p>

            <h2>Your order</h2>
            <ul>
                <?php foreach ($items as $item): ?>
                    <?php $lineTotal = storefront_product_price($item) * (int) $item['quantity']; ?>
                    <li><?php echo storefront_escape($item['productName']); ?>
                        × <?php echo (int) $item['quantity']; ?>
                        — $<?php echo number_format($lineTotal, 2); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?><br>
               Shipping: Free<br>
               <strong>Total: $<?php echo number_format($subtotal, 2); ?></strong></p>
            <h2>Payment</h2>
            <input type="hidden" name="payment_method" value="cod">
            <p>Cash on delivery (COD). Pay the courier when your order arrives. No online payment is collected.</p>
            <button class="button" type="submit">Place order</button>
        </form>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
