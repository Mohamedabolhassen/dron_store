<?php

require_once __DIR__ . '/../models/Product.php';

function product_controller_all()
{
    return product_model_all();
}

function product_controller_find_by_id($id, &$error)
{
    $id = product_controller_validate_id($id, $error);
    if ($id === false) {
        return false;
    }

    return product_model_find_by_id($id);
}

function product_controller_create($input, &$error, $imageFile = null)
{
    $product = product_controller_validate_product($input, $error);
    if ($product === false) {
        return false;
    }

    $newImage = product_controller_store_image($imageFile, $error);
    if ($newImage === false) {
        return false;
    }

    if (!product_model_create($product, $newImage)) {
        product_controller_remove_image($newImage);
        $error = 'The product could not be saved. Check that the SKU is unique and try again.';
        return false;
    }

    return true;
}

function product_controller_update($id, $input, &$error, $imageFile = null)
{
    $id = product_controller_validate_id($id, $error);
    if ($id === false) {
        return false;
    }

    $existingProduct = product_model_find_by_id($id);
    if ($existingProduct === null) {
        $error = 'The product could not be loaded. Please try again.';
        return false;
    }
    if ($existingProduct === false) {
        $error = 'The selected product was not found.';
        return false;
    }
    $existingImages = product_model_image_urls($id);
    if ($existingImages === null) {
        $error = 'The product images could not be loaded. Please try again.';
        return false;
    }

    $product = product_controller_validate_product($input, $error);
    if ($product === false) {
        return false;
    }

    $newImage = product_controller_store_image($imageFile, $error);
    if ($newImage === false) {
        return false;
    }

    if (!product_model_update($id, $product, $newImage)) {
        product_controller_remove_image($newImage);
        $error = 'The product could not be updated. Check that the SKU is unique and try again.';
        return false;
    }

    if ($newImage !== null) {
        foreach ($existingImages as $imageUrl) {
            product_controller_remove_image($imageUrl);
        }
    }

    return true;
}

function product_controller_delete($id, &$error)
{
    $id = product_controller_validate_id($id, $error);
    if ($id === false) {
        return false;
    }

    $product = product_model_find_by_id($id);
    if ($product === null) {
        $error = 'The product could not be loaded. Please try again.';
        return false;
    }

    if ($product === false) {
        $error = 'The selected product was not found.';
        return false;
    }

    $imageUrls = product_model_image_urls($id);
    if ($imageUrls === null) {
        $error = 'The product images could not be loaded. Please try again.';
        return false;
    }

    $deleted = product_model_delete($id);
    if ($deleted === null) {
        $error = 'The product could not be deleted. It may be included in an existing order.';
        return false;
    }
    if (!$deleted) {
        $error = 'The selected product was not found.';
        return false;
    }

    foreach ($imageUrls as $imageUrl) {
        product_controller_remove_image($imageUrl);
    }

    return true;
}

function product_controller_store_image($file, &$error)
{
    if ($file === null) {
        return null;
    }

    if (!is_array($file) || !isset($file['error'])) {
        $error = 'Choose a valid product image.';
        return false;
    }

    if ((int) $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $error = 'The image upload failed. Please choose the image again.';
        return false;
    }

    $maximumBytes = 5 * 1024 * 1024;
    if (!isset($file['size']) || (int) $file['size'] < 1 || (int) $file['size'] > $maximumBytes) {
        $error = 'Choose an image smaller than 5 MB.';
        return false;
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    $mimeToExtension = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $mimeType = is_array($imageInfo) && isset($imageInfo['mime']) ? $imageInfo['mime'] : '';
    if (!isset($mimeToExtension[$mimeType])) {
        $error = 'Use a valid JPEG, PNG, GIF, or WebP image.';
        return false;
    }

    $uploadDirectory = dirname(__DIR__, 2) . '/assets/img/uploads/products';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        $error = 'The product image folder could not be created.';
        return false;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $mimeToExtension[$mimeType];
    $destination = $uploadDirectory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $error = 'The product image could not be saved. Check the uploads folder permissions.';
        return false;
    }

    return 'assets/img/uploads/products/' . $filename;
}

function product_controller_remove_image($imageUrl)
{
    $imagePrefix = 'assets/img/uploads/products/';
    if (!is_string($imageUrl) || strpos($imageUrl, $imagePrefix) !== 0) {
        return;
    }

    $filename = substr($imageUrl, strlen($imagePrefix));
    if ($filename === '' || basename($filename) !== $filename) {
        return;
    }

    $imagePath = dirname(__DIR__, 2) . '/assets/img/uploads/products/' . $filename;
    if (is_file($imagePath) && !unlink($imagePath)) {
        error_log('Unable to remove replaced product image: ' . $imagePath);
    }
}

function product_controller_validate_id($id, &$error)
{
    if (!is_scalar($id) || is_bool($id)) {
        $error = 'Invalid product.';
        return false;
    }

    $validatedId = filter_var($id, FILTER_VALIDATE_INT);
    if ($validatedId === false || $validatedId < 1) {
        $error = 'Invalid product.';
        return false;
    }

    return $validatedId;
}

function product_controller_validate_product($input, &$error)
{
    if (!is_array($input)) {
        $error = 'Please complete the product form.';
        return false;
    }

    $fields = ['productName', 'sku', 'product_category', 'stock', 'price'];
    foreach ($fields as $field) {
        if (!isset($input[$field]) || !is_string($input[$field])) {
            $error = 'Please complete all required product fields.';
            return false;
        }
    }

    $product = [
        'productName' => trim($input['productName']),
        'sku' => trim($input['sku']),
        'product_category' => trim($input['product_category']),
        'stock' => trim($input['stock']),
        'price' => trim($input['price']),
        'Description' => isset($input['Description']) && is_string($input['Description'])
            ? trim($input['Description'])
            : '',
    ];

    $allowedCategories = ['Camera Drones', 'FPV Drones', 'Accessories'];
    if (
        $product['productName'] === '' ||
        $product['sku'] === '' ||
        !in_array($product['product_category'], $allowedCategories, true) ||
        strlen($product['product_category']) > 100 ||
        !ctype_digit($product['stock']) ||
        (float) $product['stock'] > 4294967295 ||
        !preg_match('/^\d{1,8}(\.\d{1,2})?$/', $product['price']) ||
        strlen($product['productName']) > 255 ||
        strlen($product['sku']) > 100 ||
        (isset($input['Description']) && !is_string($input['Description']))
    ) {
        $error = 'Enter a name, SKU, valid category, non-negative price, and whole-number stock. Check the field lengths.';
        return false;
    }

    return $product;
}
