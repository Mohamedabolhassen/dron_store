<?php

require_once __DIR__ . '/DbClasses.php';

function storefront_db()
{
    return db_connect();
}

function storefront_execute($connection, $sql, $types = '', $params = array())
{
    $statement = mysqli_prepare($connection, $sql);

    if ($types !== '') {
        $arguments = array($statement, $types);
        foreach ($params as $key => &$value) {
            $arguments[] = &$value;
        }
        unset($value);
        call_user_func_array('mysqli_stmt_bind_param', $arguments);
    }

    mysqli_stmt_execute($statement);
    return $statement;
}

function storefront_rows($statement)
{
    $result = mysqli_stmt_get_result($statement);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_free_result($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function storefront_row($statement)
{
    $result = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_stmt_close($statement);
    return $row ? $row : null;
}

function storefront_csrf_token()
{
    if (empty($_SESSION['storefront_csrf'])) {
        $_SESSION['storefront_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['storefront_csrf'];
}

function storefront_valid_csrf($token)
{
    return is_string($token)
        && isset($_SESSION['storefront_csrf'])
        && hash_equals($_SESSION['storefront_csrf'], $token);
}

function storefront_flash($kind, $message)
{
    $_SESSION['storefront_flash'] = array('kind' => $kind, 'message' => $message);
}

function storefront_take_flash()
{
    $flash = isset($_SESSION['storefront_flash']) ? $_SESSION['storefront_flash'] : null;
    unset($_SESSION['storefront_flash']);
    return $flash;
}

function storefront_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function storefront_form_value($values, $key, $default = '')
{
    return storefront_escape(isset($values[$key]) ? $values[$key] : $default);
}

function storefront_image_url($url)
{
    $url = trim((string) $url);
    if ($url === '' || strpos($url, '//') === 0 || preg_match('/^\s*javascript:/i', $url)) {
        return 'assets/img/product/1.png';
    }

    return $url;
}

function storefront_product_price($product)
{
    $discount = isset($product['discount_price']) ? (float) $product['discount_price'] : 0;
    $price = isset($product['price']) ? (float) $product['price'] : 0;
    return $discount > 0 ? $discount : $price;
}

function storefront_cart_id($connection, $create = true)
{
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

    if ($userId > 0) {
        $cart = storefront_row(storefront_execute(
            $connection,
            'SELECT id FROM carts WHERE user_id = ? ORDER BY id ASC LIMIT 1',
            'i',
            array($userId)
        ));

        if ($cart) {
            return (int) $cart['id'];
        }

        if (!$create) {
            return 0;
        }

        storefront_execute($connection, 'INSERT INTO carts (user_id) VALUES (?)', 'i', array($userId));
        return (int) mysqli_insert_id($connection);
    }

    $sessionId = session_id();
    $cart = storefront_row(storefront_execute(
        $connection,
        'SELECT id FROM carts WHERE session_id = ? AND user_id IS NULL ORDER BY id ASC LIMIT 1',
        's',
        array($sessionId)
    ));

    if ($cart) {
        return (int) $cart['id'];
    }

    if (!$create) {
        return 0;
    }

    storefront_execute($connection, 'INSERT INTO carts (session_id) VALUES (?)', 's', array($sessionId));
    return (int) mysqli_insert_id($connection);
}

function storefront_cart_rows($connection, $cartId)
{
    if ($cartId < 1) {
        return array();
    }

    return storefront_rows(storefront_execute(
        $connection,
        'SELECT ci.id AS cart_item_id, ci.quantity, ci.unit_price, p.id AS product_id, '
        . 'p.productName, p.sku, p.price, p.discount_price, COALESCE(p.stock, p.stock_quantity, 0) AS stock_quantity, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
        . 'FROM cart_items ci JOIN products p ON p.id = ci.product_id '
        . 'WHERE ci.cart_id = ? AND ci.variant_id IS NULL ORDER BY ci.id DESC',
        'i',
        array($cartId)
    ));
}

function storefront_cart_subtotal($rows)
{
    $subtotal = 0;
    foreach ($rows as $row) {
        $subtotal += storefront_product_price($row) * (int) $row['quantity'];
    }

    return $subtotal;
}

function storefront_claim_guest_cart($connection, $guestSessionId, $userId)
{
    $guestCart = storefront_row(storefront_execute(
        $connection,
        'SELECT id FROM carts WHERE session_id = ? AND user_id IS NULL ORDER BY id ASC LIMIT 1',
        's',
        array($guestSessionId)
    ));

    if (!$guestCart) {
        return;
    }

    $userCart = storefront_row(storefront_execute(
        $connection,
        'SELECT id FROM carts WHERE user_id = ? ORDER BY id ASC LIMIT 1',
        'i',
        array($userId)
    ));
    $guestCartId = (int) $guestCart['id'];

    if ($userCart) {
        storefront_execute(
            $connection,
            'UPDATE cart_items SET cart_id = ? WHERE cart_id = ?',
            'ii',
            array((int) $userCart['id'], $guestCartId)
        );
        storefront_execute($connection, 'DELETE FROM carts WHERE id = ?', 'i', array($guestCartId));
        return;
    }

    storefront_execute(
        $connection,
        'UPDATE carts SET user_id = ?, session_id = NULL WHERE id = ?',
        'ii',
        array($userId, $guestCartId)
    );
}

function storefront_require_customer($message = 'Please sign in before checkout.')
{
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
        storefront_flash('error', $message);
        header('Location: ?route=login');
        exit;
    }
}
