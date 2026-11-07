<?php
if (session_status() !== PHP_SESSION_ACTIVE) {  // - search for it
    session_start();
}
require_once __DIR__ . '/config/DbClasses.php';
$route = $_GET['route'] ?? 'home';

if ($route === 'admin' || strpos($route, 'admin-') === 0) {
    require __DIR__ . '/admin/controllers/AdminController.php';
    exit;
}

if ($route === 'cart-action' || $route === 'wishlist-action') {
    require __DIR__ . '/controllers/storefront_actions.php';
    exit;
}

if ($route === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/controllers/checkout.php';
    exit;
}

if ($route === 'home') {
    require __DIR__ . '/views/index.php';

} elseif ($route === 'index') {
    require __DIR__ . '/views/index.php';

} elseif ($route === 'index-2') {
    require __DIR__ . '/views/index-2.php';

} elseif ($route === 'create') {
    require __DIR__ . '/views/create.php';

} elseif ($route === 'edit') {
    require __DIR__ . '/views/edit.php';

} elseif ($route === 'about') {
    require __DIR__ . '/views/about.php';

} elseif ($route === 'blog') {
    require __DIR__ . '/views/blog.php';

} elseif ($route === 'blog-details') {
    require __DIR__ . '/views/blog-details.php';

} elseif ($route === 'cart') {
    require __DIR__ . '/views/cart.php';

} elseif ($route === 'checkout') {
    require __DIR__ . '/views/checkout.php';

} elseif ($route === 'contact') {
    require __DIR__ . '/views/contact.php';

} elseif ($route === 'faq') {
    require __DIR__ . '/views/faq.php';

} elseif ($route === 'forget-password') {
    require __DIR__ . '/views/forget-password.php';

} elseif ($route === 'login') {
    if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
        header('Location: ?route=my-account');
        exit;
    }

    require __DIR__ . '/views/login.php';

} elseif ($route === 'my-account') {
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
        $_SESSION['login_error'] = 'Please log in to view your account';
        header('Location: ?route=login');
        exit;
    }

    require __DIR__ . '/views/my-account.php';

} elseif ($route === 'privacy-policy') {
    require __DIR__ . '/views/privacy-policy.php';

} elseif ($route === 'product-details') {
    require __DIR__ . '/views/product-details.php';

} elseif ($route === 'products') {
    require __DIR__ . '/views/products.php';

} elseif ($route === 'register') {
    require __DIR__ . '/views/register.php';

} elseif ($route === 'services') {
    require __DIR__ . '/views/services.php';

} elseif ($route === 'tracking') {
    require __DIR__ . '/views/tracking.php';

} elseif ($route === 'wishlist') {
    require __DIR__ . '/views/wishlist.php';

} elseif ($route === '404') {
    http_response_code(404);
    require __DIR__ . '/views/404.php';       
}
elseif($route ==='newuser')
{
require __DIR__ . '/controllers/userreg.php' ;
}
elseif ($route === 'newlogin' || $route === 'logout') {
    require __DIR__ . '/controllers/usersvaild.php';
}


elseif ($route === 'AddProducts') {
    require __DIR__ . "/admin/controllers/AddProducts.php";
}

elseif ($route === 'EditProducts') {
    require __DIR__ . "/admin/controllers/AddProducts.php";
}

 else {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}