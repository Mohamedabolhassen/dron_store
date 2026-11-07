<?php

require_once __DIR__ . '/../../config/DbClasses.php';

function product_model_connection()
{
    return db_connect();
}

function product_model_all()
{
    $result = mysqli_query(
        product_model_connection(),
        'SELECT p.id, p.productName, p.sku, p.product_category, p.price, p.stock, p.Description, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
        . 'FROM products p ORDER BY p.id DESC'
    );

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function product_model_find_by_id($id)
{
    $stmt = mysqli_prepare(
        product_model_connection(),
        'SELECT p.id, p.productName, p.sku, p.product_category, p.price, p.stock, p.Description, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1) AS image_url '
        . 'FROM products p WHERE p.id = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $product ?: false;
}

function product_model_create($product, $imageUrl = null)
{
    $connection = product_model_connection();
    mysqli_begin_transaction($connection);

    try {
        $stmt = mysqli_prepare(
            $connection,
            'INSERT INTO products (productName, sku, product_category, stock, Description, price) VALUES (?, ?, ?, ?, ?, ?)'
        );

        $stock = (int) $product['stock'];
        $price = (int) $product['price'];
        mysqli_stmt_bind_param(
            $stmt,
            'sssisi',
            $product['productName'],
            $product['sku'],
            $product['product_category'],
            $stock,
            $product['Description'],
            $price
        );
        mysqli_stmt_execute($stmt);
        $productId = (int) mysqli_insert_id($connection);
        mysqli_stmt_close($stmt);

        if ($imageUrl !== null) {
            product_model_insert_image($connection, $productId, $imageUrl, $product['productName']);
        }

        mysqli_commit($connection);
        return $productId;
    } catch (Throwable $exception) {
        mysqli_rollback($connection);
        throw $exception;
    }
}

function product_model_update($id, $product, $imageUrl = null)
{
    $connection = product_model_connection();
    mysqli_begin_transaction($connection);

    try {
        $stmt = mysqli_prepare(
            $connection,
            'UPDATE products SET productName = ?, sku = ?, product_category = ?, stock = ?, Description = ?, price = ? WHERE id = ?'
        );

        $stock = (int) $product['stock'];
        $price = (int) $product['price'];
        mysqli_stmt_bind_param(
            $stmt,
            'sssisii',
            $product['productName'],
            $product['sku'],
            $product['product_category'],
            $stock,
            $product['Description'],
            $price,
            $id
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($imageUrl !== null) {
            $stmt = mysqli_prepare(
                $connection,
                'UPDATE product_images SET image_url = ?, alt_text = ?, is_primary = 1 '
                . 'WHERE product_id = ? AND is_primary = 1 ORDER BY id ASC LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'ssi', $imageUrl, $product['productName'], $id);
            mysqli_stmt_execute($stmt);
            $imageUpdated = mysqli_stmt_affected_rows($stmt) > 0;
            mysqli_stmt_close($stmt);

            if (!$imageUpdated) {
                product_model_insert_image($connection, $id, $imageUrl, $product['productName']);
            }
        }

        mysqli_commit($connection);
        return true;
    } catch (Throwable $exception) {
        mysqli_rollback($connection);
        throw $exception;
    }
}

function product_model_delete($id)
{
    $stmt = mysqli_prepare(product_model_connection(), 'DELETE FROM products WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    return $deleted;
}

function product_model_insert_image($connection, $productId, $imageUrl, $altText)
{
    $stmt = mysqli_prepare(
        $connection,
        'INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) '
        . 'VALUES (?, ?, ?, 1, 0)'
    );
    mysqli_stmt_bind_param($stmt, 'iss', $productId, $imageUrl, $altText);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
