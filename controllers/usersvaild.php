<?php

require_once __DIR__ . '/../config/storefront_db.php';

$route = isset($_GET['route']) ? (string) $_GET['route'] : '';

if ($route === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
        || !storefront_valid_csrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        storefront_flash('error', 'Please use the sign-out button to end your session.');
        header('Location: ?route=my-account');
        exit;
    }

    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly']
        );
    }
    session_destroy();
    header('Location: ?route=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?route=login');
    exit;
}

if (!storefront_valid_csrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    storefront_flash('error', 'Your session expired. Please submit the form again.');
    header('Location: ?route=login');
    exit;
}

$email = trim(isset($_POST['email']) ? (string) $_POST['email'] : '');
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    storefront_flash('error', 'Enter a valid email and password.');
    header('Location: ?route=login');
    exit;
}

try {
    $connection = storefront_db();
    $user = storefront_row(storefront_execute(
        $connection,
        'SELECT id, name, email, password_hash FROM users '
        . 'WHERE email = ? AND role = \'customer\' AND status = \'active\' LIMIT 1',
        's',
        array($email)
    ));

    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        storefront_flash('error', 'Invalid email or password.');
        header('Location: ?route=login');
        exit;
    }

    $oldSessionId = session_id();
    storefront_claim_guest_cart($connection, $oldSessionId, (int) $user['id']);
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    unset($_SESSION['storefront_flash']);

    header('Location: ?route=my-account');
    exit;
} catch (Throwable $exception) {
    error_log('Customer login failed.');
    storefront_flash('error', 'Sign-in is temporarily unavailable. Please try again later.');
    header('Location: ?route=login');
    exit;
}
