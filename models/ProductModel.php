<?php

require_once __DIR__ . '/Model.php';

function productAll($con)
{
    return modelQuery(
        $con,
        "SELECT p.id, p.productName AS name, p.product_category,
                COALESCE(NULLIF(p.short_description, ''), p.Description, '') AS description,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = p.id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url,
                COALESCE(p.discount_price, p.price) AS price, p.stock
         FROM products p
         ORDER BY p.created_at DESC, p.id DESC",
        'Unable to load product listing'
    );
}

function productFindById($con, $productId)
{
    $productId = (int) $productId;
    if ($productId < 1) {
        return false;
    }

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
         WHERE p.id = $productId
         LIMIT 1",
        'Unable to load product'
    );

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function productLockById($con, $productId)
{
    $productId = (int) $productId;
    $result = modelQuery(
        $con,
        "SELECT id, productName AS name, COALESCE(discount_price, price) AS price, stock
         FROM products
         WHERE id = $productId
         FOR UPDATE",
        'Unable to lock product during checkout'
    );

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function productReduceStock($con, $productId, $quantity)
{
    $productId = (int) $productId;
    $quantity = (int) $quantity;
    $result = modelQuery(
        $con,
        "UPDATE products
         SET stock = stock - $quantity
         WHERE id = $productId AND stock >= $quantity",
        'Unable to update product stock'
    );

    return $result && mysqli_affected_rows($con) === 1;
}
