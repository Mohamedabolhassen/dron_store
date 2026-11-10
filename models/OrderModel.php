<?php

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/ProductModel.php';

function orderRollback($con, $message)
{
    mysqli_rollback($con);
    return ['success' => false, 'message' => $message];
}

function orderPlace($con, $userId, $address, $cart)
{
    $userId = (int) $userId;
    $itemsInCart = [];
    foreach ((array) $cart as $productId => $quantity) {
        $productId = (int) $productId;
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if ($productId > 0 && $quantity !== false && $quantity > 0) {
            $itemsInCart[$productId] = (int) $quantity;
        }
    }

    if (!$itemsInCart) {
        return ['success' => false, 'message' => 'Your cart has no valid items.'];
    }

    if (!mysqli_begin_transaction($con)) {
        error_log('Unable to start checkout transaction: ' . mysqli_error($con));
        return ['success' => false, 'message' => 'Checkout is temporarily unavailable. Please try again.'];
    }

    $lockedItems = [];
    $subtotalCents = 0;
    foreach ($itemsInCart as $productId => $quantity) {
        $product = productLockById($con, $productId);
        if ($product === null) {
            return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
        }
        if (!$product) {
            return orderRollback($con, 'A product in your cart is no longer available.');
        }
        if ($quantity > (int) $product['stock']) {
            return orderRollback($con, 'There is not enough stock for ' . $product['name'] . '.');
        }

        $unitPriceCents = (int) round((float) $product['price'] * 100);
        $lineTotalCents = $unitPriceCents * $quantity;
        $subtotalCents += $lineTotalCents;
        $lockedItems[] = [
            'id' => $productId,
            'name' => $product['name'],
            'quantity' => $quantity,
            'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
            'line_total' => number_format($lineTotalCents / 100, 2, '.', ''),
        ];
    }

    $subtotal = number_format($subtotalCents / 100, 2, '.', '');
    $recipientName = modelEscape($con, $address['recipient_name']);
    $phone = modelEscape($con, $address['phone']);
    $country = modelEscape($con, $address['country']);
    $state = modelEscape($con, $address['state']);
    $city = modelEscape($con, $address['city']);
    $streetAddress = modelEscape($con, $address['street_address']);
    $addressLine2 = modelEscape($con, $address['address_line2']);
    $postalCode = modelEscape($con, $address['postal_code']);

    $addressInserted = modelQuery(
        $con,
        "INSERT INTO shipping_addresses
            (user_id, recipient_name, phone, country, state, city, street_address, address_line2, postal_code)
         VALUES
            ($userId, '$recipientName', '$phone', '$country', '$state', '$city',
             '$streetAddress', '$addressLine2', '$postalCode')",
        'Unable to save checkout address'
    );
    if (!$addressInserted) {
        return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
    }

    $addressId = (int) mysqli_insert_id($con);
    $orderNumber = modelEscape($con, 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4))));
    $trackingCode = modelEscape($con, 'TRK-' . strtoupper(bin2hex(random_bytes(6))));
    $orderInserted = modelQuery(
        $con,
        "INSERT INTO orders
            (user_id, shipping_address_id, order_number, tracking_code, subtotal, shipping_cost, total_amount)
         VALUES
            ($userId, $addressId, '$orderNumber', '$trackingCode',
             '$subtotal', '0.00', '$subtotal')",
        'Unable to create order'
    );
    if (!$orderInserted) {
        return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
    }

    $orderId = (int) mysqli_insert_id($con);
    foreach ($lockedItems as $item) {
        $productId = (int) $item['id'];
        $quantity = (int) $item['quantity'];
        $productName = modelEscape($con, $item['name']);
        $itemInserted = modelQuery(
            $con,
            "INSERT INTO order_items
                (order_id, product_id, product_name, quantity, unit_price, line_total)
             VALUES
                ($orderId, $productId, '$productName', $quantity,
                 '{$item['unit_price']}', '{$item['line_total']}')",
            'Unable to save order item'
        );
        if (!$itemInserted) {
            return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
        }

        if (!productReduceStock($con, $productId, $quantity)) {
            return orderRollback($con, 'Stock changed while you were checking out. Please review your cart.');
        }
    }

    $paymentInserted = modelQuery(
        $con,
        "INSERT INTO payments (order_id, payment_method, amount, status)
         VALUES ($orderId, 'cash_on_delivery', '$subtotal', 'pending')",
        'Unable to create payment record'
    );
    if (!$paymentInserted) {
        return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
    }

    $trackingInserted = modelQuery(
        $con,
        "INSERT INTO tracking_logs (order_id, status_step, notes)
         VALUES ($orderId, 'pending', 'Order received')",
        'Unable to create initial tracking record'
    );
    if (!$trackingInserted) {
        return orderRollback($con, 'Checkout is temporarily unavailable. Please try again.');
    }

    if (!mysqli_commit($con)) {
        error_log('Unable to commit checkout transaction: ' . mysqli_error($con));
        mysqli_rollback($con);
        return ['success' => false, 'message' => 'Checkout is temporarily unavailable. Please try again.'];
    }

    return ['success' => true, 'order_id' => $orderId];
}

function orderAllForUser($con, $userId)
{
    $userId = (int) $userId;
    return modelQuery(
        $con,
        "SELECT id, order_number, tracking_code, order_status, payment_status, total_amount, created_at
         FROM orders
         WHERE user_id = $userId
         ORDER BY created_at DESC",
        'Unable to load order history'
    );
}

function orderTrackingForUser($con, $userId)
{
    $userId = (int) $userId;
    return modelQuery(
        $con,
        "SELECT id, order_number, tracking_code, order_status, created_at
         FROM orders
         WHERE user_id = $userId
         ORDER BY created_at DESC",
        'Unable to load order tracking'
    );
}

function orderFindOwnedById($con, $orderId, $userId)
{
    $orderId = (int) $orderId;
    $userId = (int) $userId;
    if ($orderId < 1) {
        return false;
    }

    $result = modelQuery(
        $con,
        "SELECT o.id, o.order_number, o.tracking_code, o.subtotal, o.shipping_cost,
                o.total_amount, o.order_status, o.payment_status, o.created_at,
                s.recipient_name, s.phone, s.country, s.state, s.city,
                s.street_address, s.address_line2, s.postal_code
         FROM orders o
         INNER JOIN shipping_addresses s ON s.id = o.shipping_address_id
         WHERE o.id = $orderId AND o.user_id = $userId
         LIMIT 1",
        'Unable to load order details'
    );
    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function orderItemsFor($con, $orderId)
{
    $orderId = (int) $orderId;
    return modelQuery(
        $con,
        "SELECT product_name, quantity, unit_price, line_total
         FROM order_items
         WHERE order_id = $orderId
         ORDER BY id",
        'Unable to load order items'
    );
}

function orderTrackingHistory($con, $orderId)
{
    $orderId = (int) $orderId;
    return modelQuery(
        $con,
        "SELECT status_step, location, notes, logged_at
         FROM tracking_logs
         WHERE order_id = $orderId
         ORDER BY logged_at DESC, id DESC",
        'Unable to load tracking history'
    );
}
