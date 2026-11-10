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

$pages = [
    'home'            => 'index.php',
    'Login'           => 'login.php',
    'Register'        => 'register.php',
    'about'           => 'about.php',
    'blog'            => 'blog.php',
    'blog-details'    => 'blog-details.php',
    'cart'            => 'cart.php',
    'checkout'        => 'checkout.php',
    'contact'         => 'contact.php',
    'faq'             => 'faq.php',
    'forget-password' => 'forget-password.php',
    'my-account'      => 'my-account.php',
    'privacy-policy'  => 'privacy-policy.php',
    'product-details' => 'product-details.php',
    'products'        => 'products.php',
    'order-details'   => 'order-details.php',
    'services'        => 'services.php',
    'tracking'        => 'tracking.php',
    'wishlist'        => 'wishlist.php',
    '404'             => '404.php',

];

$handlers = [
    'NewRegister'       => 'reisterHandlers.php',
    'CheckLogin'        => 'LoginHandlers.php',
    'Logout'            => 'LogoutHandler.php',
    'AddToCart'         => 'AddToCartHandler.php',
    'RemoveFromCart'    => 'RemoveFromCartHandler.php',
    'UpdateCart'        => 'UpdateCartHandler.php',
    'AddToWishlist'     => 'AddToWishlistHandler.php',
    'RemoveFromWishlist'=> 'RemoveFromWishlistHandler.php',
    'Checkout'          => 'CheckoutHandler.php',
    'UpdateProfile'     => 'UpdateProfileHandler.php',
];

if (isset($pages[$route])) {
    if (in_array($route, ['checkout', 'my-account', 'wishlist', 'tracking', 'order-details'], true)) {
        requireLogin();
    }
    if ($route === 'product-details') {
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
    }
    require __DIR__ . '/views/' . $pages[$route];
} elseif (isset($handlers[$route])) {
    require __DIR__ . '/controller/' . $handlers[$route];
} else {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}
