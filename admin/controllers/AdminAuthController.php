<?php

require_once __DIR__ . '/../models/Admin.php';

function admin_auth_register($name, $email, $password)
{
    if (!is_string($name) || !is_string($email) || !is_string($password)) {
        return 'invalid';
    }

    $name = trim($name);
    $email = trim($email);

    if (
        $name === '' ||
        strlen($name) > 50 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 255 ||
        strlen($password) < 8
    ) {
        return 'invalid';
    }

    return admin_create_first_account($name, $email, $password)
        ? 'created'
        : 'closed';
}

function admin_auth_login($email, $password)
{
    if (!is_string($email) || !is_string($password)) {
        return false;
    }

    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        return false;
    }

    $admin = admin_find_by_email($email);
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    return $admin;
}
