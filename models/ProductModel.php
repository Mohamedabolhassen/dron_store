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

function productCatalogSearch($con, $search, $category, $sort, $page, $perPage = 12)
{
    $sortOptions = [
        'newest' => 'p.created_at DESC, p.id DESC',
        'price_asc' => 'COALESCE(p.discount_price, p.price) ASC, p.id DESC',
        'price_desc' => 'COALESCE(p.discount_price, p.price) DESC, p.id DESC',
        'name_asc' => 'p.productName ASC, p.id DESC',
    ];
    if (!isset($sortOptions[$sort])) {
        $sort = 'newest';
    }

    $search = trim(substr((string) $search, 0, 100));
    $category = trim(substr((string) $category, 0, 100));
    $perPage = max(1, min(48, (int) $perPage));
    $requestedPage = max(1, (int) $page);

    $categoriesResult = modelQuery(
        $con,
        "SELECT DISTINCT product_category
         FROM products
         WHERE product_category IS NOT NULL AND product_category <> ''
         ORDER BY product_category",
        'Unable to load product categories'
    );
    if (!$categoriesResult) {
        return null;
    }
    $categories = [];
    while ($row = mysqli_fetch_assoc($categoriesResult)) {
        $categories[] = $row['product_category'];
    }

    $conditions = [];
    if ($search !== '') {
        $escapedSearch = modelEscape($con, $search);
        $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $escapedSearch);
        $conditions[] = "(p.productName LIKE '%$escapedSearch%'"
            . " OR p.product_category LIKE '%$escapedSearch%'"
            . " OR p.short_description LIKE '%$escapedSearch%'"
            . " OR p.Description LIKE '%$escapedSearch%')";
    }
    if ($category !== '') {
        $conditions[] = "p.product_category = '" . modelEscape($con, $category) . "'";
    }
    $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

    $countResult = modelQuery(
        $con,
        "SELECT COUNT(*) AS total FROM products p$where",
        'Unable to count catalog products'
    );
    if (!$countResult) {
        return null;
    }
    $total = (int) (mysqli_fetch_assoc($countResult)['total'] ?? 0);
    $pages = max(1, (int) ceil($total / $perPage));
    $currentPage = min($requestedPage, $pages);
    $offset = ($currentPage - 1) * $perPage;

    $products = modelQuery(
        $con,
        "SELECT p.id, p.productName AS name, p.product_category,
                COALESCE(NULLIF(p.short_description, ''), p.Description, '') AS description,
                (SELECT pi.image_url
                 FROM product_images pi
                 WHERE pi.product_id = p.id
                 ORDER BY pi.is_primary DESC, pi.id ASC
                 LIMIT 1) AS image_url,
                COALESCE(p.discount_price, p.price) AS price, p.stock
         FROM products p$where
         ORDER BY {$sortOptions[$sort]}
         LIMIT $perPage OFFSET $offset",
        'Unable to load catalog products'
    );
    if (!$products) {
        return null;
    }

    return [
        'products' => $products,
        'categories' => $categories,
        'total' => $total,
        'page' => $currentPage,
        'pages' => $pages,
    ];
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
