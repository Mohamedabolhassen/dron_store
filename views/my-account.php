<?php
require_once __DIR__ . '/../config/storefront_db.php';

$orders = array();
$ordersError = '';
$flash = storefront_take_flash();
try {
    $orders = storefront_rows(storefront_execute(
        storefront_db(),
        'SELECT id, order_number, tracking_code, subtotal, shipping_cost, total_amount, order_status, payment_status, created_at '
        . 'FROM orders WHERE user_id = ? ORDER BY created_at DESC, id DESC',
        'i',
        array((int) $_SESSION['user_id'])
    ));
} catch (Throwable $exception) {
    error_log('Customer orders could not be loaded.');
    $ordersError = 'Your orders are temporarily unavailable.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <h1>Welcome, <?php echo storefront_escape(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Customer'); ?></h1>
    <p>Signed in as <?php echo storefront_escape(isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''); ?>.</p>
    <?php if ($flash): ?>
        <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
            <?php echo storefront_escape($flash['message']); ?>
        </p>
    <?php endif; ?>
    <p><a href="?route=products">Continue shopping</a> | <a href="?route=cart">View cart</a> |
       <a href="?route=wishlist">My wishlist</a></p>
    <form action="?route=logout" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
        <button type="submit">Log out</button>
    </form>

    <h2 style="margin-top: 35px;">My orders</h2>
    <?php if ($ordersError !== ''): ?>
        <p role="alert"><?php echo storefront_escape($ordersError); ?></p>
    <?php elseif (!$orders): ?>
        <p>You have not placed any orders yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th>Tracking</th></tr></thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo storefront_escape($order['order_number']); ?></td>
                            <td><?php echo storefront_escape($order['created_at']); ?></td>
                            <td>$<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                            <td><?php echo storefront_escape(ucfirst($order['order_status'])); ?></td>
                            <td><?php echo storefront_escape(ucfirst($order['payment_status'])); ?> (COD)</td>
                            <td>
                                <form action="?route=tracking" method="post">
                                    <input type="hidden" name="tracking_code" value="<?php echo storefront_escape($order['tracking_code']); ?>">
                                    <button type="submit">Track <?php echo storefront_escape($order['tracking_code']); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
