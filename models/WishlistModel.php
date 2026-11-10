<?php

require_once __DIR__ . '/Model.php';

function wishlistForUser($con, $userId)
{
    $userId = (int) $userId;
    return modelQuery(
        $con,
        "SELECT p.id, p.productName AS name,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = p.id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url,
                COALESCE(p.discount_price, p.price) AS price, p.stock
         FROM wishlists w
         INNER JOIN products p ON p.id = w.product_id
         WHERE w.user_id = $userId
         ORDER BY w.created_at DESC",
        'Unable to load wishlist'
    );
}

function wishlistAdd($con, $userId, $productId)
{
    $userId = (int) $userId;
    $productId = (int) $productId;

    $existing = modelQuery(
        $con,
        "SELECT id FROM wishlists
         WHERE user_id = $userId AND product_id = $productId
         LIMIT 1",
        'Unable to check wishlist item'
    );
    if (!$existing) {
        return null;
    }
    if (mysqli_num_rows($existing) > 0) {
        return true;
    }

    return modelQuery(
        $con,
        "INSERT INTO wishlists (user_id, product_id)
         VALUES ($userId, $productId)",
        'Unable to add wishlist item'
    ) !== false;
}

function wishlistRemove($con, $userId, $productId)
{
    $userId = (int) $userId;
    $productId = (int) $productId;
    $result = modelQuery(
        $con,
        "DELETE FROM wishlists
         WHERE user_id = $userId AND product_id = $productId",
        'Unable to remove wishlist item'
    );

    if (!$result) {
        return null;
    }

    return mysqli_affected_rows($con) > 0;
}
