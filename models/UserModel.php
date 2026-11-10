<?php

require_once __DIR__ . '/Model.php';

function userEmailExists($con, $email, $exceptUserId = 0)
{
    $email = modelEscape($con, $email);
    $exceptUserId = (int) $exceptUserId;
    $result = modelQuery(
        $con,
        "SELECT id FROM users
         WHERE email = '$email' AND id <> $exceptUserId
         LIMIT 1",
        'Unable to check user email'
    );

    return $result ? mysqli_num_rows($result) > 0 : null;
}

function userRegister($con, $name, $email, $password)
{
    $name = modelEscape($con, $name);
    $email = modelEscape($con, $email);
    $passwordHash = modelEscape($con, password_hash($password, PASSWORD_DEFAULT));

    return modelQuery(
        $con,
        "INSERT INTO users (name, email, password)
         VALUES ('$name', '$email', '$passwordHash')",
        'Unable to register user'
    ) !== false;
}

function userAuthenticate($con, $email, $password)
{
    $email = modelEscape($con, $email);
    $result = modelQuery(
        $con,
        "SELECT id, name, email, password
         FROM users
         WHERE email = '$email'
         LIMIT 1",
        'Unable to authenticate user'
    );

    if (!$result) {
        return null;
    }

    $user = mysqli_fetch_assoc($result);
    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    return $user;
}

function userFindById($con, $userId)
{
    $userId = (int) $userId;
    $result = modelQuery(
        $con,
        "SELECT id, name, email
         FROM users
         WHERE id = $userId
         LIMIT 1",
        'Unable to load user profile'
    );

    if (!$result) {
        return null;
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function userUpdateProfile($con, $userId, $name, $email)
{
    $userId = (int) $userId;
    $name = modelEscape($con, $name);
    $email = modelEscape($con, $email);

    return modelQuery(
        $con,
        "UPDATE users
         SET name = '$name', email = '$email'
         WHERE id = $userId",
        'Unable to update user profile'
    ) !== false;
}
