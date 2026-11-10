<?php

requirePostRequest();
requireValidCsrfToken();

$productId = (int) postInteger('product_id', 0);
if (($cart = cartRemoveItem($_SESSION['cart'] ?? [], $productId)) !== false) {
    $_SESSION['cart'] = $cart;
    $_SESSION['success'] = 'Product removed from your cart.';
} else {
    $_SESSION['error'] = 'That product is not in your cart.';
}

header('Location: index.php?route=cart');
exit;