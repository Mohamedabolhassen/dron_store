<?php

requirePostRequest();
requireValidCsrfToken();

$productId = (int) postInteger('product_id', 0);
$quantity = postInteger('qty', 1);
$product = getProductById($productId);

if ($product === null) {
    $_SESSION['error'] = 'The product could not be loaded. Please try again.';
    header('Location: index.php?route=products');
    exit;
}

if (!$product) {
    $_SESSION['error'] = 'That product is not available.';
    header('Location: index.php?route=products');
    exit;
}

if ($quantity === null || $quantity < 1) {
    $_SESSION['error'] = 'Choose a valid quantity.';
    header('Location: index.php?route=product-details&id=' . $productId);
    exit;
}

$cart = cartAddItem(
    $_SESSION['cart'] ?? [],
    $productId,
    $quantity,
    $product['stock']
);
if ($cart === false) {
    $_SESSION['error'] = 'The requested quantity exceeds available stock.';
    header('Location: index.php?route=product-details&id=' . $productId);
    exit;
}

$_SESSION['cart'] = $cart;
$_SESSION['success'] = 'Product added to your cart.';

header('Location: index.php?route=cart');
exit;