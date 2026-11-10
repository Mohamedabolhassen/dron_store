<?php
$userId = getCurrentUserId();
$ordersResult = orderTrackingForUser($con, $userId);
$trackingLoadError = !$ordersResult;
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>Order Tracking</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <h2>Track Your Orders</h2>
        <?php if ($trackingLoadError): ?>
            <p>Tracking information is temporarily unavailable. Please try again later.</p>
        <?php elseif (mysqli_num_rows($ordersResult) === 0): ?>
            <p>You do not have any orders to track.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Order</th><th>Tracking number</th><th>Placed</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
                            <tr>
                                <td><?= escapeHtml($order['order_number']) ?></td>
                                <td><?= escapeHtml($order['tracking_code']) ?></td>
                                <td><?= escapeHtml($order['created_at']) ?></td>
                                <td><?= escapeHtml($order['order_status']) ?></td>
                                <td><a href="?route=order-details&amp;id=<?= (int) $order['id'] ?>">View progress</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
