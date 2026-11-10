<?php

requirePostRequest();
requireValidCsrfToken();

$productId = (int) postInteger('product_id', 0);
$quantity = postInteger('qty');
$product = getProductById($productId);

if ($product === null) {
    $_SESSION['error'] = 'The cart could not be updated. Please try again.';
} elseif (!$product || $quantity === null || $quantity < 1) {
    $_SESSION['error'] = 'Choose a valid product and quantity.';
} elseif ($quantity > (int) $product['stock']) {
    $_SESSION['error'] = 'The requested quantity exceeds available stock.';
} else {
    $cart = cartUpdateItem($_SESSION['cart'] ?? [], $productId, $quantity, $product['stock']);
    if ($cart === false) {
        $_SESSION['error'] = 'That product is not in your cart.';
    } else {
        $_SESSION['cart'] = $cart;
        $_SESSION['success'] = 'Cart quantity updated.';
    }
}

header('Location: index.php?route=cart');
exit;