<?php
$userId = getCurrentUserId();
$orderId = isset($_GET['id']) && is_scalar($_GET['id']) ? (int) $_GET['id'] : 0;
$order = orderFindOwnedById($con, $orderId, $userId);
$itemsResult = false;
$trackingResult = false;
$orderLoadError = $order === null;

if ($order) {
    $itemsResult = orderItemsFor($con, $orderId);
    $trackingResult = orderTrackingHistory($con, $orderId);
    if (!$itemsResult || !$trackingResult) {
        $orderLoadError = true;
    }
}

include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul>
                <li><a href="?route=home">Home</a></li>
                <li><a href="?route=my-account">My Account</a></li>
                <li>Order Details</li>
            </ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if ($orderLoadError): ?>
            <p>Order details are temporarily unavailable. Please try again later.</p>
        <?php elseif (!$order): ?>
            <h2>Order not found</h2>
            <p>This order does not exist or is not available in your account.</p>
        <?php else: ?>
            <h2>Order <?= escapeHtml($order['order_number']) ?></h2>
            <p>Status: <?= escapeHtml($order['order_status']) ?></p>
            <p>Payment: <?= escapeHtml($order['payment_status']) ?></p>
            <p>Tracking number: <?= escapeHtml($order['tracking_code']) ?></p>
            <p>Placed: <?= escapeHtml($order['created_at']) ?></p>

            <h3>Items</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
                            <tr>
                                <td><?= escapeHtml($item['product_name']) ?></td>
                                <td>$<?= number_format((float) $item['unit_price'], 2) ?></td>
                                <td><?= (int) $item['quantity'] ?></td>
                                <td>$<?= number_format((float) $item['line_total'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3">Subtotal</th><td>$<?= number_format((float) $order['subtotal'], 2) ?></td></tr>
                        <tr><th colspan="3">Shipping</th><td>$<?= number_format((float) $order['shipping_cost'], 2) ?></td></tr>
                        <tr><th colspan="3">Total</th><td>$<?= number_format((float) $order['total_amount'], 2) ?></td></tr>
                    </tfoot>
                </table>
            </div>

            <h3>Shipping address</h3>
            <p>
                <?= escapeHtml($order['recipient_name']) ?><br>
                <?= escapeHtml($order['street_address']) ?>
                <?= $order['address_line2'] ? ', ' . escapeHtml($order['address_line2']) : '' ?><br>
                <?= escapeHtml($order['city']) ?>
                <?= $order['state'] ? ', ' . escapeHtml($order['state']) : '' ?>
                <?= $order['postal_code'] ? ' ' . escapeHtml($order['postal_code']) : '' ?><br>
                <?= escapeHtml($order['country']) ?><br>
                <?= escapeHtml($order['phone']) ?>
            </p>

            <h3>Tracking history</h3>
            <?php if (mysqli_num_rows($trackingResult) === 0): ?>
                <p>No tracking updates are available yet.</p>
            <?php else: ?>
                <ul>
                    <?php while ($tracking = mysqli_fetch_assoc($trackingResult)): ?>
                        <li>
                            <?= escapeHtml($tracking['logged_at']) ?> —
                            <?= escapeHtml($tracking['status_step']) ?>
                            <?= $tracking['location'] ? ' (' . escapeHtml($tracking['location']) . ')' : '' ?>
                            <?= $tracking['notes'] ? ': ' . escapeHtml($tracking['notes']) : '' ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
