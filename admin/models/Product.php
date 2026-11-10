<?php

require_once __DIR__ . '/../../config/db.php';

function product_model_connection()
{
    global $con;
    return $con;
}

function product_model_escape($value)
{
    return mysqli_real_escape_string(product_model_connection(), (string) $value);
}

function product_model_all()
{
    $result = mysqli_query(
        product_model_connection(),
        'SELECT p.id, p.productName, p.sku, p.product_category, p.price, p.stock, p.Description, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, id ASC LIMIT 1) AS image_url '
        . 'FROM products p ORDER BY p.id DESC'
    );

    if (!$result) {
        error_log('Unable to load admin products: ' . mysqli_error(product_model_connection()));
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function product_model_find_by_id($id)
{
    $id = (int) $id;
    $result = mysqli_query(
        product_model_connection(),
        'SELECT p.id, p.productName, p.sku, p.product_category, p.price, p.stock, p.Description, '
        . '(SELECT image_url FROM product_images WHERE product_id = p.id '
        . 'ORDER BY is_primary DESC, id ASC LIMIT 1) AS image_url '
        . "FROM products p WHERE p.id = $id LIMIT 1"
    );

    if (!$result) {
        error_log('Unable to find admin product: ' . mysqli_error(product_model_connection()));
        return null;
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function product_model_image_urls($id)
{
    $id = (int) $id;
    $result = mysqli_query(
        product_model_connection(),
        "SELECT image_url FROM product_images WHERE product_id = $id ORDER BY is_primary DESC, id ASC"
    );

    if (!$result) {
        error_log('Unable to load product images: ' . mysqli_error(product_model_connection()));
        return null;
    }

    return array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'image_url');
}

function product_model_create($product, $imageUrl = null)
{
    $connection = product_model_connection();
    $name = product_model_escape($product['productName']);
    $sku = product_model_escape($product['sku']);
    $category = product_model_escape($product['product_category']);
    $description = product_model_escape($product['Description']);
    $price = product_model_escape($product['price']);
    $stock = (int) $product['stock'];

    if (!mysqli_begin_transaction($connection)) {
        error_log('Unable to start product creation transaction: ' . mysqli_error($connection));
        return false;
    }

    $created = mysqli_query(
        $connection,
        "INSERT INTO products (productName, sku, product_category, stock, Description, price)
         VALUES ('$name', '$sku', '$category', $stock, '$description', '$price')"
    );
    if (!$created) {
        error_log('Unable to create product: ' . mysqli_error($connection));
        mysqli_rollback($connection);
        return false;
    }

    $productId = (int) mysqli_insert_id($connection);
    if ($imageUrl !== null && !product_model_insert_image($connection, $productId, $imageUrl)) {
        mysqli_rollback($connection);
        return false;
    }

    if (!mysqli_commit($connection)) {
        error_log('Unable to commit product creation: ' . mysqli_error($connection));
        mysqli_rollback($connection);
        return false;
    }

    return $productId;
}

function product_model_update($id, $product, $imageUrl = null)
{
    $connection = product_model_connection();
    $id = (int) $id;
    $name = product_model_escape($product['productName']);
    $sku = product_model_escape($product['sku']);
    $category = product_model_escape($product['product_category']);
    $description = product_model_escape($product['Description']);
    $price = product_model_escape($product['price']);
    $stock = (int) $product['stock'];

    if (!mysqli_begin_transaction($connection)) {
        error_log('Unable to start product update transaction: ' . mysqli_error($connection));
        return false;
    }

    $updated = mysqli_query(
        $connection,
        "UPDATE products
         SET productName = '$name', sku = '$sku', product_category = '$category',
             stock = $stock, Description = '$description', price = '$price'
         WHERE id = $id"
    );
    if (!$updated) {
        error_log('Unable to update product: ' . mysqli_error($connection));
        mysqli_rollback($connection);
        return false;
    }

    if ($imageUrl !== null) {
        $removedImages = mysqli_query(
            $connection,
            "DELETE FROM product_images WHERE product_id = $id"
        );
        if (!$removedImages || !product_model_insert_image($connection, $id, $imageUrl)) {
            mysqli_rollback($connection);
            return false;
        }
    }

    if (!mysqli_commit($connection)) {
        error_log('Unable to commit product update: ' . mysqli_error($connection));
        mysqli_rollback($connection);
        return false;
    }

    return true;
}

function product_model_delete($id)
{
    $id = (int) $id;
    $result = mysqli_query(
        product_model_connection(),
        "DELETE FROM products WHERE id = $id"
    );

    if (!$result) {
        error_log('Unable to delete product: ' . mysqli_error(product_model_connection()));
        return null;
    }

    return mysqli_affected_rows(product_model_connection()) > 0;
}

function product_model_insert_image($connection, $productId, $imageUrl)
{
    $productId = (int) $productId;
    $imageUrl = mysqli_real_escape_string($connection, (string) $imageUrl);
    $result = mysqli_query(
        $connection,
        "INSERT INTO product_images (product_id, image_url, is_primary)
         VALUES ($productId, '$imageUrl', 1)"
    );

    if (!$result) {
        error_log('Unable to save product image: ' . mysqli_error($connection));
    }

    return (bool) $result;
}
