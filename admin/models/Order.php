<?php

require_once __DIR__ . '/../../config/db.php';

function admin_order_update_status($id, $status)
{
    global $con;

    $id = (int) $id;
    if (!in_array($status, ['processing', 'shipped', 'delivered', 'cancelled'], true)) {
        return 'invalid_status';
    }

    $escapedStatus = mysqli_real_escape_string($con, $status);
    if (!mysqli_begin_transaction($con)) {
        error_log('Unable to start order status transaction: ' . mysqli_error($con));
        return 'error';
    }

    $currentResult = mysqli_query($con, "SELECT order_status FROM orders WHERE id = $id FOR UPDATE");
    if (!$currentResult) {
        error_log('Unable to lock order before status update: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }
    $currentOrder = mysqli_fetch_assoc($currentResult);
    if (!$currentOrder) {
        mysqli_rollback($con);
        return 'not_found';
    }

    $allowedTransitions = [
        'pending' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
    ];
    $currentStatus = $currentOrder['order_status'];
    if (!isset($allowedTransitions[$currentStatus])
        || !in_array($status, $allowedTransitions[$currentStatus], true)) {
        mysqli_rollback($con);
        return 'not_allowed';
    }

    $result = mysqli_query(
        $con,
        "UPDATE orders SET order_status = '$escapedStatus' WHERE id = $id"
    );

    if (!$result) {
        error_log('Unable to update order status: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    if ($status === 'cancelled') {
        $restocked = mysqli_query(
            $con,
            "UPDATE products p
             JOIN (
                 SELECT product_id, SUM(quantity) AS quantity
                 FROM order_items
                 WHERE order_id = $id
                 GROUP BY product_id
             ) oi ON oi.product_id = p.id
             SET p.stock = p.stock + oi.quantity"
        );
        if (!$restocked) {
            error_log('Unable to restore stock for cancelled order: ' . mysqli_error($con));
            mysqli_rollback($con);
            return 'error';
        }
    }

    if ($status === 'delivered') {
        $paymentUpdated = mysqli_query(
            $con,
            "UPDATE payments SET status = 'paid'
             WHERE order_id = $id AND payment_method = 'cash_on_delivery' AND status = 'pending'"
        );
        $orderPaymentUpdated = mysqli_query(
            $con,
            "UPDATE orders SET payment_status = 'paid' WHERE id = $id"
        );
        if (!$paymentUpdated || !$orderPaymentUpdated) {
            error_log('Unable to mark cash-on-delivery payment as paid: ' . mysqli_error($con));
            mysqli_rollback($con);
            return 'error';
        }
    }

    $notes = [
        'processing' => 'Order approved by admin',
        'shipped' => 'Order shipped',
        'delivered' => 'Order delivered and cash collected',
        'cancelled' => $currentStatus === 'processing'
            ? 'Order cancelled by admin'
            : 'Order rejected by admin',
    ][$status];
    $escapedNotes = mysqli_real_escape_string($con, $notes);
    $logged = mysqli_query(
        $con,
        "INSERT INTO tracking_logs (order_id, status_step, notes)
         VALUES ($id, '$escapedStatus', '$escapedNotes')"
    );
    if (!$logged) {
        error_log('Unable to save order status tracking log: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    if (!mysqli_commit($con)) {
        error_log('Unable to commit order status transaction: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    return 'updated';
}

function admin_dashboard_get_metrics()
{
    global $con;

    $result = mysqli_query(
        $con,
        "SELECT
            (SELECT COUNT(*) FROM orders) AS total_orders,
            (SELECT COUNT(*) FROM orders WHERE order_status = 'pending') AS pending_orders,
            (SELECT COUNT(*) FROM orders
             WHERE created_at >= CURDATE()
               AND created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS orders_today,
            (SELECT COALESCE(SUM(total_amount), 0)
             FROM orders
             WHERE order_status <> 'cancelled'
               AND created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY)
               AND created_at < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)) AS order_value_month,
            (SELECT COUNT(*) FROM users) AS total_customers,
            (SELECT COUNT(*) FROM products WHERE stock = 0) AS out_of_stock_products"
    );
    if (!$result) {
        error_log('Unable to load Admin dashboard metrics: ' . mysqli_error($con));
        return false;
    }

    return mysqli_fetch_assoc($result);
}

function admin_dashboard_get_analytics($days = 30)
{
    global $con;

    $days = in_array((int) $days, [7, 30, 90], true) ? (int) $days : 30;
    $firstDayOffset = $days - 1;
    $dailyResult = mysqli_query(
        $con,
        "SELECT DATE(created_at) AS report_date,
                SUM(CASE WHEN order_status <> 'cancelled' THEN 1 ELSE 0 END) AS order_count,
                SUM(CASE WHEN order_status <> 'cancelled' THEN total_amount ELSE 0 END) AS order_value,
                SUM(CASE WHEN order_status <> 'cancelled' AND payment_status = 'paid'
                         THEN total_amount ELSE 0 END) AS collected_revenue
         FROM orders
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $firstDayOffset DAY)
           AND created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
         GROUP BY DATE(created_at)
         ORDER BY report_date"
    );
    if (!$dailyResult) {
        error_log('Unable to load Admin analytics trends: ' . mysqli_error($con));
        return false;
    }

    $dailyData = [];
    while ($row = mysqli_fetch_assoc($dailyResult)) {
        $dailyData[$row['report_date']] = $row;
    }

    $labels = [];
    $orders = [];
    $orderValues = [];
    $collectedRevenue = [];
    for ($offset = $firstDayOffset; $offset >= 0; $offset--) {
        $day = date('Y-m-d', strtotime('-' . $offset . ' days'));
        $record = $dailyData[$day] ?? [];
        $labels[] = date('M j', strtotime($day));
        $orders[] = (int) ($record['order_count'] ?? 0);
        $orderValues[] = (float) ($record['order_value'] ?? 0);
        $collectedRevenue[] = (float) ($record['collected_revenue'] ?? 0);
    }

    $statusCounts = array_fill_keys(
        ['pending', 'processing', 'shipped', 'delivered', 'cancelled'],
        0
    );
    $statusResult = mysqli_query(
        $con,
        'SELECT order_status, COUNT(*) AS order_count FROM orders GROUP BY order_status'
    );
    if (!$statusResult) {
        error_log('Unable to load Admin analytics order statuses: ' . mysqli_error($con));
        return false;
    }
    while ($row = mysqli_fetch_assoc($statusResult)) {
        if (array_key_exists($row['order_status'], $statusCounts)) {
            $statusCounts[$row['order_status']] = (int) $row['order_count'];
        }
    }

    $categoryResult = mysqli_query(
        $con,
        "SELECT COALESCE(NULLIF(p.product_category, ''), 'Uncategorized') AS category,
                SUM(oi.line_total) AS order_value
         FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
         JOIN products p ON p.id = oi.product_id
         WHERE o.order_status <> 'cancelled'
           AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL $firstDayOffset DAY)
           AND o.created_at < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
         GROUP BY COALESCE(NULLIF(p.product_category, ''), 'Uncategorized')
         ORDER BY order_value DESC
         LIMIT 8"
    );
    if (!$categoryResult) {
        error_log('Unable to load Admin analytics categories: ' . mysqli_error($con));
        return false;
    }
    $categories = [];
    $categoryValues = [];
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row['category'];
        $categoryValues[] = (float) $row['order_value'];
    }

    $lowStockResult = mysqli_query(
        $con,
        "SELECT p.id, p.productName, p.product_category, p.stock,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = p.id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url
         FROM products p
         WHERE p.stock <= 5
         ORDER BY p.stock ASC, p.productName ASC
         LIMIT 8"
    );
    if (!$lowStockResult) {
        error_log('Unable to load Admin analytics stock alerts: ' . mysqli_error($con));
        return false;
    }

    return [
        'days' => $days,
        'labels' => $labels,
        'orders' => $orders,
        'order_values' => $orderValues,
        'collected_revenue' => $collectedRevenue,
        'statuses' => $statusCounts,
        'categories' => $categories,
        'category_values' => $categoryValues,
        'low_stock' => mysqli_fetch_all($lowStockResult, MYSQLI_ASSOC),
    ];
}

function admin_dashboard_get_recent_orders($limit = 20)
{
    global $con;
    $limit = max(1, min(100, (int) $limit));
    $result = mysqli_query(
        $con,
        "SELECT o.id AS order_id, o.order_number, o.total_amount, o.order_status,
                o.created_at, sa.recipient_name, sa.phone, sa.city, sa.street_address,
                GROUP_CONCAT(CONCAT(oi.product_name, ' x', oi.quantity)
                             ORDER BY oi.id SEPARATOR ', ') AS products
         FROM orders o
         JOIN shipping_addresses sa ON sa.id = o.shipping_address_id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         GROUP BY o.id, o.order_number, o.total_amount, o.order_status, o.created_at,
                  sa.recipient_name, sa.phone, sa.city, sa.street_address
         ORDER BY o.created_at DESC
         LIMIT $limit"
    );
    if (!$result) {
        error_log('Unable to load Admin dashboard orders: ' . mysqli_error($con));
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function admin_dashboard_get_top_products($limit = 5)
{
    global $con;
    $limit = max(1, min(20, (int) $limit));
    $result = mysqli_query(
        $con,
        "SELECT oi.product_id, oi.product_name,
                SUM(oi.quantity) AS quantity_sold,
                SUM(oi.line_total) AS total_value,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = oi.product_id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url
         FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
         WHERE o.order_status <> 'cancelled'
         GROUP BY oi.product_id, oi.product_name
         ORDER BY quantity_sold DESC, total_value DESC
         LIMIT $limit"
    );
    if (!$result) {
        error_log('Unable to load Admin dashboard top products: ' . mysqli_error($con));
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
