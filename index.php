<?php
session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controller/functions.php';

require_once __DIR__ . '/models/Model.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/models/CartModel.php';
require_once __DIR__ . '/models/WishlistModel.php';
require_once __DIR__ . '/models/OrderModel.php';

$route = isset($_GET['route']) && is_string($_GET['route']) ? $_GET['route'] : 'home';

if (strpos($route, 'admin') === 0) {
    require __DIR__ . '/admin/controllers/AdminController.php';
    exit;
}

if ($route === 'home') {
    require __DIR__ . '/views/index.php';
} elseif ($route === 'Login') {
    require __DIR__ . '/views/login.php';
} elseif ($route === 'Register') {
    require __DIR__ . '/views/register.php';
} elseif ($route === 'about') {
    require __DIR__ . '/views/about.php';
} elseif ($route === 'blog') {
    require __DIR__ . '/views/blog.php';
} elseif ($route === 'blog-details') {
    require __DIR__ . '/views/blog-details.php';
} elseif ($route === 'cart') {
    require __DIR__ . '/views/cart.php';
} elseif ($route === 'checkout') {
    requireLogin();
    require __DIR__ . '/views/checkout.php';
} elseif ($route === 'contact') {
    require __DIR__ . '/views/contact.php';
} elseif ($route === 'faq') {
    require __DIR__ . '/views/faq.php';
} elseif ($route === 'forget-password') {
    require __DIR__ . '/views/forget-password.php';
} elseif ($route === 'my-account') {
    requireLogin();
    require __DIR__ . '/views/my-account.php';
} elseif ($route === 'privacy-policy') {
    require __DIR__ . '/views/privacy-policy.php';
} elseif ($route === 'product-details') {
    $productId = isset($_GET['id']) && is_scalar($_GET['id']) ? (int) $_GET['id'] : 0;
    $product = getProductById($productId);
    if ($product === null) {
        http_response_code(500);
        require __DIR__ . '/views/404.php';
        exit;
    }
    if (!$product) {
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        exit;
    }
    require __DIR__ . '/views/product-details.php';
} elseif ($route === 'products') {
    require __DIR__ . '/views/products.php';
} elseif ($route === 'order-details') {
    requireLogin();
    require __DIR__ . '/views/order-details.php';
} elseif ($route === 'services') {
    require __DIR__ . '/views/services.php';
} elseif ($route === 'tracking') {
    requireLogin();
    require __DIR__ . '/views/tracking.php';
} elseif ($route === 'wishlist') {
    requireLogin();
    require __DIR__ . '/views/wishlist.php';
} elseif ($route === '404') {
    require __DIR__ . '/views/404.php';
} elseif ($route === 'NewRegister') {
    require __DIR__ . '/controller/reisterHandlers.php';
} elseif ($route === 'CheckLogin') {
    require __DIR__ . '/controller/LoginHandlers.php';
} elseif ($route === 'Logout') {
    require __DIR__ . '/controller/LogoutHandler.php';
} elseif ($route === 'AddToCart') {
    require __DIR__ . '/controller/AddToCartHandler.php';
} elseif ($route === 'RemoveFromCart') {
    require __DIR__ . '/controller/RemoveFromCartHandler.php';
} elseif ($route === 'UpdateCart') {
    require __DIR__ . '/controller/UpdateCartHandler.php';
} elseif ($route === 'AddToWishlist') {
    require __DIR__ . '/controller/AddToWishlistHandler.php';
} elseif ($route === 'RemoveFromWishlist') {
    require __DIR__ . '/controller/RemoveFromWishlistHandler.php';
} elseif ($route === 'Checkout') {
    require __DIR__ . '/controller/CheckoutHandler.php';
} elseif ($route === 'UpdateProfile') {
    require __DIR__ . '/controller/UpdateProfileHandler.php';
} else {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}
