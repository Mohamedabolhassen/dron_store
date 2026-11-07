<?php

require_once __DIR__ . '/../config/storefront_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?route=checkout');
    exit;
}

storefront_require_customer();

if (!storefront_valid_csrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    storefront_flash('error', 'Your session expired. Please review your order and try again.');
    header('Location: ?route=checkout');
    exit;
}

$fields = array(
    'recipient_name' => trim(isset($_POST['recipient_name']) ? (string) $_POST['recipient_name'] : ''),
    'phone' => trim(isset($_POST['phone']) ? (string) $_POST['phone'] : ''),
    'country' => trim(isset($_POST['country']) ? (string) $_POST['country'] : ''),
    'city' => trim(isset($_POST['city']) ? (string) $_POST['city'] : ''),
    'street_address' => trim(isset($_POST['street_address']) ? (string) $_POST['street_address'] : ''),
    'postal_code' => trim(isset($_POST['postal_code']) ? (string) $_POST['postal_code'] : '')
);
$_SESSION['checkout_old'] = $fields;

$valid = strlen($fields['recipient_name']) >= 2 && strlen($fields['recipient_name']) <= 150
    && strlen($fields['phone']) >= 6 && strlen($fields['phone']) <= 30
    && preg_match('/^[0-9+(). -]+$/', $fields['phone'])
    && strlen($fields['country']) >= 2 && strlen($fields['country']) <= 100
    && strlen($fields['city']) >= 2 && strlen($fields['city']) <= 100
    && strlen($fields['street_address']) >= 5 && strlen($fields['street_address']) <= 255
    && strlen($fields['postal_code']) <= 20
    && (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cod');

if (!$valid) {
    storefront_flash('error', 'Please check the required shipping details. Cash on delivery is the only payment option.');
    header('Location: ?route=checkout');
    exit;
}

$connection = storefront_db();
$cartId = storefront_cart_id($connection, false);

if (!$cartId) {
    storefront_flash('error', 'Your cart is empty.');
    header('Location: ?route=cart');
    exit;
}

try {
    mysqli_begin_transaction($connection);

    $items = storefront_rows(storefront_execute(
        $connection,
        'SELECT ci.id AS cart_item_id, ci.product_id, ci.quantity, p.productName, p.price, p.discount_price, '
        . 'COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity '
        . 'FROM cart_items ci JOIN products p ON p.id = ci.product_id '
        . 'WHERE ci.cart_id = ? AND ci.variant_id IS NULL ORDER BY ci.id ASC FOR UPDATE',
        'i',
        array($cartId)
    ));

    if (!$items) {
        mysqli_rollback($connection);
        storefront_flash('error', 'Your cart is empty.');
        header('Location: ?route=cart');
        exit;
    }

    $subtotal = 0;
    foreach ($items as $item) {
        if ((int) $item['quantity'] < 1 || (int) $item['quantity'] > (int) $item['stock_quantity']) {
            mysqli_rollback($connection);
            storefront_flash('error', 'One or more items no longer have enough stock. Please update your cart.');
            header('Location: ?route=cart');
            exit;
        }
        $subtotal += storefront_product_price($item) * (int) $item['quantity'];
    }

    $userId = (int) $_SESSION['user_id'];
    storefront_execute(
        $connection,
        'INSERT INTO shipping_addresses (user_id, recipient_name, phone, country, city, street_address, postal_code) '
        . 'VALUES (?, ?, ?, ?, ?, ?, ?)',
        'issssss',
        array(
            $userId,
            $fields['recipient_name'],
            $fields['phone'],
            $fields['country'],
            $fields['city'],
            $fields['street_address'],
            $fields['postal_code'] === '' ? null : $fields['postal_code']
        )
    );
    $addressId = (int) mysqli_insert_id($connection);

    $orderNumber = 'ORD-' . strtoupper(bin2hex(random_bytes(5)));
    $trackingCode = 'TRK-' . strtoupper(bin2hex(random_bytes(6)));
    $shippingCost = 0.00;
    $total = $subtotal + $shippingCost;
    storefront_execute(
        $connection,
        'INSERT INTO orders (user_id, shipping_address_id, order_number, tracking_code, subtotal, '
        . 'shipping_cost, discount_amount, total_amount, order_status, payment_status) '
        . 'VALUES (?, ?, ?, ?, ?, ?, 0, ?, \'pending\', \'unpaid\')',
        'iissddd',
        array($userId, $addressId, $orderNumber, $trackingCode, $subtotal, $shippingCost, $total)
    );
    $orderId = (int) mysqli_insert_id($connection);

    foreach ($items as $item) {
        $productId = (int) $item['product_id'];
        $quantity = (int) $item['quantity'];
        $unitPrice = storefront_product_price($item);
        $lineSubtotal = $unitPrice * $quantity;
        $stockAfter = (int) $item['stock_quantity'] - $quantity;

        $updated = storefront_execute(
            $connection,
            'UPDATE products SET stock = ?, stock_quantity = ? '
            . 'WHERE id = ? AND COALESCE(stock, stock_quantity, 0) >= ?',
            'iiii',
            array($stockAfter, $stockAfter, $productId, $quantity)
        );
        if (mysqli_stmt_affected_rows($updated) !== 1) {
            mysqli_stmt_close($updated);
            mysqli_rollback($connection);
            storefront_flash('error', 'Stock changed while placing your order. Please try again.');
            header('Location: ?route=checkout');
            exit;
        }
        mysqli_stmt_close($updated);

        storefront_execute(
            $connection,
            'INSERT INTO order_items (order_id, product_id, variant_id, product_name_snap, quantity, unit_price, subtotal) '
            . 'VALUES (?, ?, NULL, ?, ?, ?, ?)',
            'iisidd',
            array(
                $orderId,
                $productId,
                (string) $item['productName'],
                $quantity,
                $unitPrice,
                $lineSubtotal
            )
        );
    }

    storefront_execute(
        $connection,
        'INSERT INTO payments (order_id, payment_method, amount, status) VALUES (?, \'cod\', ?, \'pending\')',
        'id',
        array($orderId, $total)
    );
    storefront_execute(
        $connection,
        'INSERT INTO tracking_logs (order_id, status_step, notes) VALUES (?, \'Order placed\', \'Cash on delivery order received\')',
        'i',
        array($orderId)
    );
    storefront_execute($connection, 'DELETE FROM cart_items WHERE cart_id = ?', 'i', array($cartId));

    mysqli_commit($connection);
    unset($_SESSION['checkout_old']);
    storefront_flash('success', 'Order ' . $orderNumber . ' placed. Your tracking number is ' . $trackingCode . '. Pay cash on delivery.');
    header('Location: ?route=my-account');
    exit;
} catch (Throwable $exception) {
    mysqli_rollback($connection);
    error_log('Checkout failed.');
    storefront_flash('error', 'We could not place your order. No payment was taken; please try again.');
    header('Location: ?route=checkout');
    exit;
}
