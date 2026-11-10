<?php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/AdminAuthController.php';
require_once __DIR__ . '/OrderController.php';
require_once __DIR__ . '/ProductController.php';

$adminRoutes = [
    'admin' => 'dashboard/index.php',
    'admin-dashboard' => 'dashboard/index.php',
    'admin-charts-apexcharts' => 'charts/apexcharts.php',
    'admin-charts-chartjs' => 'charts/chartjs.php',
    'admin-charts-echarts' => 'charts/echarts.php',
    'admin-components-accordion' => 'components/accordion.php',
    'admin-components-alerts' => 'components/alerts.php',
    'admin-components-badges' => 'components/badges.php',
    'admin-components-breadcrumbs' => 'components/breadcrumbs.php',
    'admin-components-buttons' => 'components/buttons.php',
    'admin-components-cards' => 'components/cards.php',
    'admin-components-carousel' => 'components/carousel.php',
    'admin-components-list-group' => 'components/list-group.php',
    'admin-components-modal' => 'components/modal.php',
    'admin-components-pagination' => 'components/pagination.php',
    'admin-components-progress' => 'components/progress.php',
    'admin-components-spinners' => 'components/spinners.php',
    'admin-components-tabs' => 'components/tabs.php',
    'admin-components-tooltips' => 'components/tooltips.php',
    'admin-forms-editors' => 'forms/editors.php',
    'admin-forms-elements' => 'pages/storeadmin.php',
    'admin-forms-layouts' => 'forms/layouts.php',
    'admin-forms-validation' => 'forms/validation.php',
    'admin-icons-bootstrap' => 'icons/bootstrap.php',
    'admin-icons-boxicons' => 'icons/boxicons.php',
    'admin-icons-remix' => 'icons/remix.php',
    'admin-pages-blank' => 'pages/blank.php',
    'admin-pages-contact' => 'pages/contact.php',
    'admin-pages-error-404' => 'pages/error-404.php',
    'admin-pages-faq' => 'pages/admin-faq.php',
    'admin-pages-login' => 'pages/login.php',
    'admin-pages-register' => 'pages/register.php',
    'admin-tables-data' => 'tables/data.php',
    'admin-tables-general' => 'tables/general.php',
    'admin-users-profile' => 'users/profile.php',
];

$adminBasePath = 'admin/';
$adminIsAuthenticated = !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id']);

if ($route === 'admin-logout') {
    unset(
        $_SESSION['admin_logged_in'],
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        $_SESSION['admin_email'],
        $_SESSION['admin_csrf_token']
    );
    session_regenerate_id(true);
    header('Location: ?route=admin-pages-login');
    exit;
}

if ($route === 'admin-login-submit' || $route === 'admin-register-submit') {
    $formRoute = $route === 'admin-login-submit' ? 'admin-pages-login' : 'admin-pages-register';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?route=' . $formRoute);
        exit;
    }

    $csrfToken = $_SESSION['admin_csrf_token'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($csrfToken) || !is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $_SESSION['admin_auth_error'] = 'Your session expired. Please try again.';
        header('Location: ?route=' . $formRoute);
        exit;
    }

    try {
        if ($route === 'admin-login-submit') {
            $admin = admin_auth_login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($admin === false) {
                $_SESSION['admin_auth_error'] = 'Invalid email or password.';
                header('Location: ?route=admin-pages-login');
                exit;
            }

            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            unset($_SESSION['admin_csrf_token']);
            unset($_SESSION['admin_auth_error']);

            header('Location: ?route=admin');
            exit;
        }

        $registrationResult = admin_auth_register(
            $_POST['name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        if ($registrationResult !== 'created') {
            $_SESSION['admin_auth_error'] = [
                'invalid' => 'Enter your name, a valid email address, and a password of at least 8 characters.',
                'closed' => 'Admin registration is closed. Sign in with an admin account.',
            ][$registrationResult] ?? 'Unable to create the admin account.';
            header('Location: ?route=admin-pages-register');
            exit;
        }

        $_SESSION['admin_auth_success'] = 'Admin account created. You can now sign in.';
        header('Location: ?route=admin-pages-login');
        exit;
    } catch (mysqli_sql_exception $exception) {
        error_log('Admin authentication database error: ' . $exception->getMessage());
        $_SESSION['admin_auth_error'] = 'Admin authentication is temporarily unavailable. Please try again later.';
        header('Location: ?route=' . $formRoute);
        exit;
    } catch (RuntimeException $exception) {
        $isRegistration = $route === 'admin-register-submit';
        error_log(
            ($isRegistration ? 'Admin registration error: ' : 'Admin authentication error: ')
            . $exception->getMessage()
        );
        $_SESSION['admin_auth_error'] = $isRegistration
            ? 'Admin registration is temporarily unavailable. Please try again later.'
            : 'Admin authentication is temporarily unavailable. Please try again later.';
        header('Location: ?route=' . $formRoute);
        exit;
    }
}

$isAdminAuthPage = $route === 'admin-pages-login' || $route === 'admin-pages-register';
if ($isAdminAuthPage) {
    if ($adminIsAuthenticated) {
        header('Location: ?route=admin');
        exit;
    }

    try {
        $adminRegistrationEnabled = !admin_has_accounts();
    } catch (RuntimeException $exception) {
        error_log('Admin authentication database error: ' . $exception->getMessage());
        $adminRegistrationEnabled = false;
        $_SESSION['admin_auth_error'] = 'Admin authentication is not configured. Please contact the site administrator.';
    }

    if ($route === 'admin-pages-register' && !$adminRegistrationEnabled) {
        $_SESSION['admin_auth_error'] = $_SESSION['admin_auth_error']
            ?? 'Admin registration is closed. Sign in with an admin account.';
        header('Location: ?route=admin-pages-login');
        exit;
    }

    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
} elseif (!$adminIsAuthenticated) {
    $_SESSION['admin_auth_error'] = 'Please sign in to access the admin area.';
    header('Location: ?route=admin-pages-login');
    exit;
}

$productActionRoutes = [
    'admin-product-create',
    'admin-product-update',
    'admin-product-delete',
];

if ($adminIsAuthenticated && empty($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

if ($route === 'admin-order-action') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?route=admin');
        exit;
    }

    $csrfToken = $_SESSION['admin_csrf_token'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($csrfToken) || !is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $_SESSION['order_message'] = [
            'type' => 'danger',
            'text' => 'Your session expired. Please try again.',
        ];
        header('Location: ?route=admin');
        exit;
    }

    $result = admin_order_controller_update_status(
        $_POST['order_id'] ?? null,
        $_POST['order_action'] ?? null
    );
    $messages = [
        'updated' => ['success', 'Order status updated.'],
        'not_pending' => ['warning', 'This order has already been processed.'],
        'not_found' => ['danger', 'The selected order was not found.'],
        'invalid_order' => ['danger', 'Invalid order.'],
        'invalid_action' => ['danger', 'Invalid order action.'],
        'error' => ['danger', 'The order status could not be updated. Please try again.'],
    ];
    $message = $messages[$result] ?? ['danger', 'The order status could not be updated. Please try again.'];
    $_SESSION['order_message'] = ['type' => $message[0], 'text' => $message[1]];
    header('Location: ?route=admin');
    exit;
}

if ($route === 'admin-profile-password') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?route=admin-users-profile');
        exit;
    }

    $csrfToken = $_SESSION['admin_csrf_token'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($csrfToken) || !is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $_SESSION['admin_password_message'] = [
            'type' => 'danger',
            'text' => 'Your session expired. Please try again.',
        ];
        header('Location: ?route=admin-users-profile');
        exit;
    }

    $currentPassword = $_POST['password'] ?? null;
    $newPassword = $_POST['newpassword'] ?? null;
    $confirmPassword = $_POST['renewpassword'] ?? null;

    if (!is_string($currentPassword) || !is_string($newPassword) || !is_string($confirmPassword)
        || strlen($newPassword) < 8 || strlen($newPassword) > 4096
        || $newPassword !== $confirmPassword) {
        $_SESSION['admin_password_message'] = [
            'type' => 'danger',
            'text' => 'Enter matching new passwords with at least 8 characters.',
        ];
    } else {
        try {
            $changed = admin_change_password((int) $_SESSION['admin_id'], $currentPassword, $newPassword);
            $_SESSION['admin_password_message'] = $changed
                ? ['type' => 'success', 'text' => 'Your password was changed.']
                : ['type' => 'danger', 'text' => 'The current password is incorrect.'];
        } catch (mysqli_sql_exception $exception) {
            error_log('Admin password update failed: ' . $exception->getMessage());
            $_SESSION['admin_password_message'] = [
                'type' => 'danger',
                'text' => 'Your password could not be changed. Please try again.',
            ];
        } catch (RuntimeException $exception) {
            error_log('Admin password update failed: ' . $exception->getMessage());
            $_SESSION['admin_password_message'] = [
                'type' => 'danger',
                'text' => 'Your password could not be changed. Please try again.',
            ];
        }
    }

    header('Location: ?route=admin-users-profile');
    exit;
}

if (in_array($route, $productActionRoutes, true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?route=admin-forms-elements');
        exit;
    }

    $csrfToken = $_SESSION['admin_csrf_token'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($csrfToken) || !is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
        $_SESSION['product_message'] = [
            'type' => 'danger',
            'text' => 'Your session expired. Please try again.',
        ];
        header('Location: ?route=admin-forms-elements');
        exit;
    }

    try {
        $actionError = '';
        if ($route === 'admin-product-create') {
            $actionSucceeded = product_controller_create($_POST, $actionError, $_FILES['image'] ?? null);
            $message = 'Product added successfully.';
        } elseif ($route === 'admin-product-update') {
            $actionSucceeded = product_controller_update(
                $_POST['id'] ?? null,
                $_POST,
                $actionError,
                $_FILES['image'] ?? null
            );
            $message = 'Product updated successfully.';
        } else {
            $actionSucceeded = product_controller_delete($_POST['id'] ?? null, $actionError);
            $message = 'Product deleted successfully.';
        }

        $_SESSION['product_message'] = $actionSucceeded
            ? ['type' => 'success', 'text' => $message]
            : ['type' => 'danger', 'text' => $actionError];
    } catch (Throwable $exception) {
        error_log('Admin product operation failed: ' . $exception->getMessage());
        $_SESSION['product_message'] = [
            'type' => 'danger',
            'text' => 'The product could not be saved. Please try again.',
        ];
    }

    header('Location: ?route=admin-forms-elements');
    exit;
}

$editingProduct = null;
if ($route === 'admin-product-edit') {
    $actionError = '';
    try {
        $editingProduct = product_controller_find_by_id($_GET['id'] ?? null, $actionError);
        if ($editingProduct === false || $editingProduct === null) {
            $_SESSION['product_message'] = [
                'type' => 'danger',
                'text' => $actionError !== ''
                    ? $actionError
                    : ($editingProduct === null
                        ? 'The product could not be loaded. Please try again.'
                        : 'The selected product was not found.'),
            ];
            header('Location: ?route=admin-forms-elements');
            exit;
        }
    } catch (mysqli_sql_exception $exception) {
        error_log('Admin product lookup failed: ' . $exception->getMessage());
        $_SESSION['product_message'] = [
            'type' => 'danger',
            'text' => 'The product could not be loaded. Please try again.',
        ];
        header('Location: ?route=admin-forms-elements');
        exit;
    }

    $route = 'admin-forms-elements';
}

$productForm = $editingProduct ?? [
    'id' => '',
    'productName' => '',
    'sku' => '',
    'product_category' => '',
    'price' => '',
    'stock' => '',
    'Description' => '',
];

$adminView = $adminRoutes[$route] ?? null;

if ($adminView !== null && is_file(__DIR__ . '/../views/' . $adminView)) {
    if ($route === 'admin-users-profile') {
        try {
            $adminProfile = admin_find_by_id($_SESSION['admin_id']);
        } catch (RuntimeException $exception) {
            error_log('Admin profile lookup failed: ' . $exception->getMessage());
            http_response_code(500);
            echo 'The admin profile could not be loaded.';
            return;
        }
        $adminPasswordMessage = $_SESSION['admin_password_message'] ?? null;
        unset($_SESSION['admin_password_message']);

        if ($adminProfile === false) {
            unset(
                $_SESSION['admin_logged_in'],
                $_SESSION['admin_id'],
                $_SESSION['admin_name'],
                $_SESSION['admin_email']
            );
            $_SESSION['admin_auth_error'] = 'Your admin account could not be found. Please sign in again.';
            header('Location: ?route=admin-pages-login');
            exit;
        }
    }

    if ($route === 'admin-forms-elements') {
        $products = product_controller_all();
        if ($products === false) {
            http_response_code(500);
            echo 'The product list could not be loaded.';
            return;
        }
    }

    require __DIR__ . '/../views/' . $adminView;
    return;
}

http_response_code(404);
require dirname(__DIR__, 2) . '/views/404.php';
