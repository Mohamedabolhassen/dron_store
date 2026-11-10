<?php

function checkUserinput($input)
{
    return !empty(trim((string) $input));
}

function postString($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}

function postTrimmedString($key)
{
    return trim(postString($key));
}

function postInteger($key, $default = null)
{
    if (!array_key_exists($key, $_POST)) {
        return $default;
    }
    if (!is_string($_POST[$key]) && !is_int($_POST[$key])) {
        return null;
    }

    $value = filter_var($_POST[$key], FILTER_VALIDATE_INT);
    return $value === false ? null : $value;
}

function CheckEmailinput($input)
{
    return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function productImageUrl($imageUrl)
{
    $imageUrl = trim((string) $imageUrl);
    if ($imageUrl === '') {
        return 'assets/img/product/details-1.jpg';
    }

    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $scheme = strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true)
            ? $imageUrl
            : 'assets/img/product/details-1.jpg';
    }

    if (strpos($imageUrl, '..') !== false || !preg_match('/^[A-Za-z0-9_\\.\\/-]+$/', $imageUrl)) {
        return 'assets/img/product/details-1.jpg';
    }

    return $imageUrl;
}

function logDatabaseError($context)
{
    global $con;
    error_log($context . ': ' . mysqli_error($con));
}

function requireLogin()
{
    if (empty($_SESSION['user_id']) || (int) $_SESSION['user_id'] < 1) {
        $_SESSION['error'] = 'Please log in to continue.';
        $_SESSION['return_route'] = isset($_GET['route']) && is_string($_GET['route'])
            ? $_GET['route']
            : 'my-account';
        if ($_SESSION['return_route'] === 'order-details') {
            $_SESSION['return_order_id'] = isset($_GET['id']) && is_scalar($_GET['id'])
                ? (int) $_GET['id']
                : 0;
        }
        header('Location: index.php?route=Login');
        exit;
    }
}

function getCurrentUserId()
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function getProductById($productId)
{
    global $con;
    return productFindById($con, (int) $productId);
}

function getCartItems()
{
    global $con;
    $result = cartItems($con, $_SESSION['cart'] ?? []);

    if ($result === null) {
        return null;
    }

    $_SESSION['cart'] = $result['cart'];
    return $result['items'];
}

function cartSubtotal($items)
{
    return cartCalculateSubtotal($items);
}

function cartItemCount()
{
    $cart = $_SESSION['cart'] ?? [];
    if (!is_array($cart)) {
        return 0;
    }

    $count = 0;
    foreach ($cart as $quantity) {
        if (is_scalar($quantity) && (int) $quantity > 0) {
            $count += (int) $quantity;
        }
    }
    return $count;
}

function requirePostRequest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method not allowed.');
    }
}

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function requireValidCsrfToken()
{
    $submittedToken = postString('csrf_token');
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (!is_string($submittedToken) || !is_string($sessionToken) || $sessionToken === '' ||
        !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(400);
        exit('Invalid form submission. Reload the page and try again.');
    }
}