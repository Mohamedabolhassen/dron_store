<?php

requirePostRequest();
requireValidCsrfToken();

$email = postTrimmedString('email');
$password = postString('password');

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Email and password are required.';
    header('Location: index.php?route=Login');
    exit;
}

$user = userAuthenticate($con, $email, $password);

if ($user === null) {
    $_SESSION['error'] = 'Login is temporarily unavailable. Please try again.';
    header('Location: index.php?route=Login');
    exit;
}

if ($user) {
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['name'];
    $_SESSION['email'] = $user['email'];

    $returnRoute = $_SESSION['return_route'] ?? 'home';
    $returnOrderId = (int) ($_SESSION['return_order_id'] ?? 0);
    unset($_SESSION['return_route']);
    unset($_SESSION['return_order_id']);
    $allowedReturnRoutes = ['checkout', 'my-account', 'wishlist', 'tracking', 'order-details'];
    if (!in_array($returnRoute, $allowedReturnRoutes, true)) {
        $returnRoute = 'home';
    }

    $returnUrl = 'index.php?route=' . rawurlencode($returnRoute);
    if ($returnRoute === 'order-details' && $returnOrderId > 0) {
        $returnUrl .= '&id=' . $returnOrderId;
    }
    header('Location: ' . $returnUrl);
    exit;
}

$_SESSION['error'] = 'Email or password is incorrect.';
header('Location: index.php?route=Login');
exit;
