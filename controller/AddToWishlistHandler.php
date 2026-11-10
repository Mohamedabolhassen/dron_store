<?php

requireLogin();
requirePostRequest();
requireValidCsrfToken();

$productId = (int) postInteger('product_id', 0);
$product = getProductById($productId);

if ($product === null) {
    $_SESSION['error'] = 'The wishlist could not be updated. Please try again.';
} elseif (!$product) {
    $_SESSION['error'] = 'That product is not available.';
} else {
    $added = wishlistAdd($con, getCurrentUserId(), $productId);
    if ($added === null || $added === false) {
        $_SESSION['error'] = 'The wishlist could not be updated. Please try again.';
    } else {
        $_SESSION['success'] = 'Product added to your wishlist.';
    }
}

header('Location: index.php?route=wishlist');
exit;
