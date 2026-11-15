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
        strlen($password) < 8 ||
        strlen($password) > 4096
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

function admin_auth_send_password_reset($email)
{
    if (!is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        return 'invalid';
    }

    $mailFrom = getenv('DRONE_STORE_MAIL_FROM');
    $baseUrl = getenv('DRONE_STORE_BASE_URL');
    $baseParts = is_string($baseUrl) ? parse_url($baseUrl) : false;
    if (
        !is_string($mailFrom) ||
        !filter_var($mailFrom, FILTER_VALIDATE_EMAIL) ||
        !is_array($baseParts) ||
        empty($baseParts['host']) ||
        !in_array($baseParts['scheme'] ?? '', ['http', 'https'], true) ||
        isset($baseParts['user']) ||
        isset($baseParts['pass']) ||
        isset($baseParts['query']) ||
        isset($baseParts['fragment']) ||
        !is_callable('mail')
    ) {
        return 'mail_unconfigured';
    }

    $token = admin_issue_password_reset_token(trim($email));
    if ($token === false) {
        return 'sent';
    }

    $resetUrl = rtrim($baseUrl, '/')
        . '/?route=admin-pages-reset-password&token='
        . rawurlencode($token);
    $subject = 'Reset your Drophut admin password';
    $message = "A password reset was requested for your admin account.\n\n"
        . "Open this one-time link within one hour to choose a new password:\n"
        . $resetUrl
        . "\n\nIf you did not request this, you can ignore this email.";
    $headers = [
        'From: Drophut Store <' . $mailFrom . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    try {
        $sent = mail(trim($email), $subject, $message, implode("\r\n", $headers));
    } catch (Throwable $exception) {
        error_log('Unable to send admin password reset email: ' . $exception->getMessage());
        admin_delete_password_reset_token($token);
        return 'sent';
    }

    if (!$sent) {
        error_log('Unable to send admin password reset email.');
        admin_delete_password_reset_token($token);
    }

    return 'sent';
}
