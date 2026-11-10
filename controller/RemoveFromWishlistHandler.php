<?php

requireLogin();
requirePostRequest();
requireValidCsrfToken();

$productId = (int) postInteger('product_id', 0);
if ($productId < 1) {
    $_SESSION['error'] = 'Choose a valid wishlist item.';
    header('Location: index.php?route=wishlist');
    exit;
}

$removed = wishlistRemove($con, getCurrentUserId(), $productId);

if ($removed === null) {
    $_SESSION['error'] = 'The wishlist could not be updated. Please try again.';
} elseif ($removed) {
    $_SESSION['success'] = 'Product removed from your wishlist.';
} else {
    $_SESSION['error'] = 'That product is not in your wishlist.';
}

header('Location: index.php?route=wishlist');
exit;
