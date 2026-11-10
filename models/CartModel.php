<?php

require_once __DIR__ . '/Model.php';

function cartAddItem($cart, $productId, $quantity, $stock)
{
    $cart = is_array($cart) ? $cart : [];
    $productId = (int) $productId;
    $quantity = (int) $quantity;
    $stock = (int) $stock;
    $currentQuantity = isset($cart[$productId]) ? (int) $cart[$productId] : 0;

    if ($productId < 1 || $quantity < 1 || $currentQuantity + $quantity > $stock) {
        return false;
    }

    $cart[$productId] = $currentQuantity + $quantity;
    return $cart;
}

function cartUpdateItem($cart, $productId, $quantity, $stock)
{
    $cart = is_array($cart) ? $cart : [];
    $productId = (int) $productId;
    $quantity = (int) $quantity;
    $stock = (int) $stock;

    if (!isset($cart[$productId]) || $quantity < 1 || $quantity > $stock) {
        return false;
    }

    $cart[$productId] = $quantity;
    return $cart;
}

function cartRemoveItem($cart, $productId)
{
    $cart = is_array($cart) ? $cart : [];
    $productId = (int) $productId;
    if ($productId < 1 || !isset($cart[$productId])) {
        return false;
    }

    unset($cart[$productId]);
    return $cart;
}

function cartItems($con, $cart)
{
    if (!is_array($cart) || !$cart) {
        return ['items' => [], 'cart' => []];
    }

    $validCart = [];
    foreach ($cart as $productId => $quantity) {
        $productId = (int) $productId;
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if ($productId > 0 && $quantity !== false && $quantity > 0) {
            $validCart[$productId] = (int) $quantity;
        }
    }

    if (!$validCart) {
        return ['items' => [], 'cart' => []];
    }

    $productIds = implode(',', array_keys($validCart));
    $result = modelQuery(
        $con,
        "SELECT p.id, p.productName AS name,
                COALESCE(NULLIF(p.short_description, ''), p.Description, '') AS description,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = p.id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url,
                COALESCE(p.discount_price, p.price) AS price, p.stock
         FROM products p
         WHERE p.id IN ($productIds)
         ORDER BY FIELD(p.id, $productIds)",
        'Unable to load cart products'
    );

    if (!$result) {
        return null;
    }

    $items = [];
    $remainingCart = [];
    while ($product = mysqli_fetch_assoc($result)) {
        $productId = (int) $product['id'];
        $product['quantity'] = $validCart[$productId];
        $product['line_total'] = (float) $product['price'] * $product['quantity'];
        $items[] = $product;
        $remainingCart[$productId] = $validCart[$productId];
    }

    return ['items' => $items, 'cart' => $remainingCart];
}

function cartCalculateSubtotal($items)
{
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += (float) $item['line_total'];
    }

    return $subtotal;
}
