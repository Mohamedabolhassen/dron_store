<?php

requirePostRequest();
requireValidCsrfToken();

$name = postTrimmedString('name');
$email = postTrimmedString('email');
$password = postString('password');

if (!checkUserinput($name) || !checkUserinput($email) || !checkUserinput($password)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: index.php?route=Register');
    exit;
}

if (!CheckEmailinput($email)) {
    $_SESSION['error'] = 'Enter a valid email address.';
    header('Location: index.php?route=Register');
    exit;
}

if (strlen($name) > 150 || strlen($email) > 150) {
    $_SESSION['error'] = 'Name or email address is too long.';
    header('Location: index.php?route=Register');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters.';
    header('Location: index.php?route=Register');
    exit;
}

$duplicate = userEmailExists($con, $email);

if ($duplicate === null) {
    $_SESSION['error'] = 'Registration is temporarily unavailable. Please try again.';
    header('Location: index.php?route=Register');
    exit;
}

if ($duplicate) {
    $_SESSION['error'] = 'An account already uses this email address.';
    header('Location: index.php?route=Register');
    exit;
}

if (!userRegister($con, $name, $email, $password)) {
    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: index.php?route=Register');
    exit;
}

$_SESSION['success'] = 'Registration successful. You can now log in.';
header('Location: index.php?route=Login');
exit;
