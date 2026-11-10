<?php

requireLogin();
requirePostRequest();
requireValidCsrfToken();

$userId = getCurrentUserId();
$name = postTrimmedString('name');
$email = postTrimmedString('email');

if (!checkUserinput($name) || !checkUserinput($email) || !CheckEmailinput($email)) {
    $_SESSION['error'] = 'Enter a name and a valid email address.';
    header('Location: index.php?route=my-account');
    exit;
}
if (strlen($name) > 150 || strlen($email) > 150) {
    $_SESSION['error'] = 'Name or email address is too long.';
    header('Location: index.php?route=my-account');
    exit;
}

$existingUser = userEmailExists($con, $email, $userId);

if ($existingUser === null) {
    $_SESSION['error'] = 'Your profile could not be updated. Please try again.';
    header('Location: index.php?route=my-account');
    exit;
}

if ($existingUser) {
    $_SESSION['error'] = 'That email address is already in use.';
    header('Location: index.php?route=my-account');
    exit;
}

if (!userUpdateProfile($con, $userId, $name, $email)) {
    $_SESSION['error'] = 'Your profile could not be updated. Please try again.';
} else {
    $_SESSION['username'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['success'] = 'Your profile has been updated.';
}

header('Location: index.php?route=my-account');
exit;
