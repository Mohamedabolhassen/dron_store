<?php

require_once __DIR__ . '/../config/storefront_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?route=register');
    exit;
}

if (!storefront_valid_csrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    storefront_flash('error', 'Your session expired. Please submit the form again.');
    header('Location: ?route=register');
    exit;
}

$name = trim(isset($_POST['name']) ? (string) $_POST['name'] : '');
$email = trim(isset($_POST['email']) ? (string) $_POST['email'] : '');
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';

if (strlen($name) < 2 || strlen($name) > 150
    || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150
    || strlen($password) < 8 || strlen($password) > 4096) {
    storefront_flash('error', 'Enter a name, valid email address, and password with at least 8 characters.');
    header('Location: ?route=register');
    exit;
}

try {
    $connection = storefront_db();
    $existing = storefront_row(storefront_execute(
        $connection,
        'SELECT id FROM users WHERE email = ? LIMIT 1',
        's',
        array($email)
    ));

    if ($existing) {
        storefront_flash('error', 'An account already uses that email address.');
        header('Location: ?route=register');
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    storefront_execute(
        $connection,
        'INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, \'customer\', \'active\')',
        'sss',
        array($name, $email, $passwordHash)
    );
    storefront_flash('success', 'Your account is ready. Sign in to continue.');
    header('Location: ?route=login');
    exit;
} catch (Throwable $exception) {
    error_log('Customer registration failed.');
    storefront_flash('error', 'We could not create your account right now. Please try again.');
    header('Location: ?route=register');
    exit;
}
