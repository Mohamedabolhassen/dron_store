<?php

require_once __DIR__ . '/../config/storefront_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?route=cart');
    exit;
}

if (!storefront_valid_csrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    storefront_flash('error', 'Your session expired. Please try again.');
    header('Location: ?route=cart');
    exit;
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
$connection = storefront_db();

try {
    if ($action === 'add') {
        $productId = filter_var(isset($_POST['product_id']) ? $_POST['product_id'] : null, FILTER_VALIDATE_INT);
        $quantity = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : null, FILTER_VALIDATE_INT);

        if (!$productId || !$quantity || $quantity < 1 || $quantity > 99) {
            storefront_flash('error', 'Choose a valid product quantity.');
            header('Location: ?route=cart');
            exit;
        }

        mysqli_begin_transaction($connection);
        $product = storefront_row(storefront_execute(
            $connection,
            'SELECT id, productName, price, discount_price, COALESCE(stock, stock_quantity, 0) AS stock_quantity '
            . 'FROM products WHERE id = ? FOR UPDATE',
            'i',
            array($productId)
        ));

        if (!$product || (int) $product['stock_quantity'] < 1) {
            mysqli_rollback($connection);
            storefront_flash('error', 'This product is currently out of stock.');
            header('Location: ?route=cart');
            exit;
        }

        $cartId = storefront_cart_id($connection, true);
        $item = storefront_row(storefront_execute(
            $connection,
            'SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? '
            . 'AND variant_id IS NULL ORDER BY id ASC LIMIT 1 FOR UPDATE',
            'ii',
            array($cartId, $productId)
        ));
        $newQuantity = ($item ? (int) $item['quantity'] : 0) + $quantity;

        if ($newQuantity > (int) $product['stock_quantity'] || $newQuantity > 99) {
            mysqli_rollback($connection);
            storefront_flash('error', 'The requested quantity exceeds available stock.');
            header('Location: ?route=cart');
            exit;
        }

        $price = storefront_product_price($product);
        if ($item) {
            storefront_execute(
                $connection,
                'UPDATE cart_items SET quantity = ?, unit_price = ? WHERE id = ? AND cart_id = ?',
                'idii',
                array($newQuantity, $price, (int) $item['id'], $cartId)
            );
        } else {
            storefront_execute(
                $connection,
                'INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, unit_price) '
                . 'VALUES (?, ?, NULL, ?, ?)',
                'iiid',
                array($cartId, $productId, $quantity, $price)
            );
        }

        mysqli_commit($connection);
        storefront_flash('success', 'Product added to your cart.');
        header('Location: ?route=cart');
        exit;
    }

    if ($action === 'update' || $action === 'remove') {
        $itemId = filter_var(isset($_POST['item_id']) ? $_POST['item_id'] : null, FILTER_VALIDATE_INT);
        $cartId = storefront_cart_id($connection, false);
        if (!$itemId || !$cartId) {
            storefront_flash('error', 'That cart item could not be found.');
            header('Location: ?route=cart');
            exit;
        }

        if ($action === 'remove') {
            storefront_execute(
                $connection,
                'DELETE FROM cart_items WHERE id = ? AND cart_id = ?',
                'ii',
                array($itemId, $cartId)
            );
            storefront_flash('success', 'Item removed from your cart.');
        } else {
            $quantity = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : null, FILTER_VALIDATE_INT);
            if (!$quantity || $quantity < 1 || $quantity > 99) {
                storefront_flash('error', 'Enter a quantity from 1 to 99.');
                header('Location: ?route=cart');
                exit;
            }

            $item = storefront_row(storefront_execute(
                $connection,
                'SELECT ci.product_id, COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity '
                . 'FROM cart_items ci JOIN products p ON p.id = ci.product_id '
                . 'WHERE ci.id = ? AND ci.cart_id = ?',
                'ii',
                array($itemId, $cartId)
            ));

            if (!$item || $quantity > (int) $item['stock_quantity']) {
                storefront_flash('error', 'The requested quantity exceeds available stock.');
                header('Location: ?route=cart');
                exit;
            }

            storefront_execute(
                $connection,
                'UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?',
                'iii',
                array($quantity, $itemId, $cartId)
            );
            storefront_flash('success', 'Your cart was updated.');
        }

        header('Location: ?route=cart');
        exit;
    }

    if ($action === 'wishlist-add' || $action === 'wishlist-remove') {
        storefront_require_customer('Please sign in to manage your wishlist.');
        $productId = filter_var(isset($_POST['product_id']) ? $_POST['product_id'] : null, FILTER_VALIDATE_INT);
        if (!$productId) {
            storefront_flash('error', 'Choose a valid product.');
            header('Location: ?route=wishlist');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        if ($action === 'wishlist-add') {
            $product = storefront_row(storefront_execute(
                $connection,
                'SELECT id FROM products WHERE id = ?',
                'i',
                array($productId)
            ));
            if (!$product) {
                storefront_flash('error', 'That product is no longer available.');
                header('Location: ?route=wishlist');
                exit;
            }

            storefront_execute(
                $connection,
                'INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (?, ?)',
                'ii',
                array($userId, $productId)
            );
            storefront_flash('success', 'Product saved to your wishlist.');
            header('Location: ?route=wishlist');
            exit;
        }

        storefront_execute(
            $connection,
            'DELETE FROM wishlists WHERE user_id = ? AND product_id = ?',
            'ii',
            array($userId, $productId)
        );
        storefront_flash('success', 'Product removed from your wishlist.');
        header('Location: ?route=wishlist');
        exit;
    }

    storefront_flash('error', 'That action is not available.');
    $target = strpos($action, 'wishlist-') === 0 ? 'wishlist' : 'cart';
    header('Location: ?route=' . $target);
    exit;
} catch (Throwable $exception) {
    if (isset($connection)) {
        try {
            mysqli_rollback($connection);
        } catch (Throwable $ignored) {
        }
    }
    error_log('Storefront action failed.');
    storefront_flash('error', 'We could not complete that request. Please try again.');
    $target = strpos($action, 'wishlist-') === 0 ? 'wishlist' : 'cart';
    header('Location: ?route=' . $target);
    exit;
}
