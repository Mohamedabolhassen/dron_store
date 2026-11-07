<?php
require_once __DIR__ . '/../config/storefront_db.php';
storefront_require_customer('Please sign in to track your orders.');

$order = null;
$logs = array();
$message = '';
$trackingCode = trim(isset($_POST['tracking_code']) ? (string) $_POST['tracking_code'] : '');

if ($trackingCode !== '') {
    if (strlen($trackingCode) > 50 || !preg_match('/^[A-Za-z0-9-]+$/', $trackingCode)) {
        $message = 'Enter a valid tracking number.';
    } else {
        try {
            $order = storefront_row(storefront_execute(
                storefront_db(),
                'SELECT id, order_number, tracking_code, order_status, payment_status, created_at '
                . 'FROM orders WHERE tracking_code = ? AND user_id = ? LIMIT 1',
                'si',
                array($trackingCode, (int) $_SESSION['user_id'])
            ));

            if ($order) {
                $logs = storefront_rows(storefront_execute(
                    storefront_db(),
                    'SELECT status_step, location, notes, logged_at FROM tracking_logs '
                    . 'WHERE order_id = ? ORDER BY logged_at ASC, id ASC',
                    'i',
                    array((int) $order['id'])
                ));
            } else {
                $message = 'No order with that tracking number was found in your account.';
            }
        } catch (Throwable $exception) {
            error_log('Order tracking lookup failed.');
            $message = 'Tracking is temporarily unavailable.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track order | Drophut</title>
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding: 50px 15px;">
    <h1>Track your order</h1>
    <p>Enter the tracking number shown in your account's order list.</p>
    <form action="?route=tracking" method="post">
        <label for="tracking_code">Tracking number</label>
        <input id="tracking_code" name="tracking_code" type="text" maxlength="50"
               value="<?php echo storefront_escape($trackingCode); ?>" required>
        <button type="submit">Track order</button>
    </form>
    <?php if ($message !== ''): ?><p role="status"><?php echo storefront_escape($message); ?></p><?php endif; ?>
    <?php if ($order): ?>
        <h2><?php echo storefront_escape($order['order_number']); ?></h2>
        <p>Status: <?php echo storefront_escape(ucfirst($order['order_status'])); ?> |
           Payment: <?php echo storefront_escape(ucfirst($order['payment_status'])); ?> (COD)</p>
        <p>Placed: <?php echo storefront_escape($order['created_at']); ?></p>
        <h3>Tracking history</h3>
        <?php if (!$logs): ?>
            <p>No tracking updates have been recorded yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($logs as $log): ?>
                    <li><?php echo storefront_escape($log['logged_at']); ?> —
                        <?php echo storefront_escape($log['status_step']); ?>
                        <?php if (!empty($log['location'])): ?> (<?php echo storefront_escape($log['location']); ?>)<?php endif; ?>
                        <?php if (!empty($log['notes'])): ?>: <?php echo storefront_escape($log['notes']); ?><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
    <p><a href="?route=my-account">Back to my account</a></p>
</main>
<?php require __DIR__ . '/../inc/fotter.php'; ?>
</body>
</html>
