<?php

requireLogin();
requirePostRequest();
requireValidCsrfToken();

$cart = $_SESSION['cart'] ?? [];
if (!is_array($cart) || !$cart) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: index.php?route=cart');
    exit;
}

$address = [
    'recipient_name' => postTrimmedString('recipient_name'),
    'phone' => postTrimmedString('phone'),
    'country' => postTrimmedString('country'),
    'state' => postTrimmedString('state'),
    'city' => postTrimmedString('city'),
    'street_address' => postTrimmedString('street_address'),
    'address_line2' => postTrimmedString('address_line2'),
    'postal_code' => postTrimmedString('postal_code'),
];

foreach (['recipient_name', 'phone', 'country', 'city', 'street_address'] as $requiredField) {
    if (!checkUserinput($address[$requiredField])) {
        $_SESSION['error'] = 'Complete all required shipping fields.';
        header('Location: index.php?route=checkout');
        exit;
    }
}

$fieldLimits = [
    'recipient_name' => 150,
    'phone' => 30,
    'country' => 100,
    'state' => 100,
    'city' => 100,
    'street_address' => 255,
    'address_line2' => 255,
    'postal_code' => 20,
];
foreach ($fieldLimits as $field => $limit) {
    if (strlen($address[$field]) > $limit) {
        $_SESSION['error'] = 'One or more shipping fields are too long.';
        header('Location: index.php?route=checkout');
        exit;
    }
}

$result = orderPlace($con, getCurrentUserId(), $address, $cart);
if (!$result['success']) {
    $_SESSION['error'] = $result['message'];
    header('Location: index.php?route=checkout');
    exit;
}

$_SESSION['cart'] = [];
$_SESSION['success'] = 'Your order has been placed.';
header('Location: index.php?route=order-details&id=' . (int) $result['order_id']);
exit;
