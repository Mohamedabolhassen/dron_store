<?php

require_once __DIR__ . '/../../config/db.php';

function admin_db_connection()
{
    global $con;
    return $con;
}

function admin_password_column()
{
    $result = mysqli_query(admin_db_connection(), 'SHOW COLUMNS FROM admins');
    if (!$result) {
        error_log('Unable to inspect admin table: ' . mysqli_error(admin_db_connection()));
        throw new RuntimeException('Admin authentication is not configured.');
    }

    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }

    if (in_array('password_hash', $columns, true)) {
        return 'password_hash';
    }

    if (in_array('password', $columns, true)) {
        return 'password';
    }

    throw new RuntimeException('The admins table must contain a password or password_hash column.');
}

function admin_has_accounts()
{
    $result = mysqli_query(admin_db_connection(), 'SELECT id FROM admins LIMIT 1');
    if (!$result) {
        error_log('Unable to check admin accounts: ' . mysqli_error(admin_db_connection()));
        throw new RuntimeException('Admin authentication is not configured.');
    }

    return mysqli_fetch_assoc($result) !== null;
}

function admin_create_first_account($name, $email, $password)
{
    $connection = admin_db_connection();
    $lockName = 'dron_store_first_admin_registration';
    $escapedLockName = mysqli_real_escape_string($connection, $lockName);
    $lockResult = mysqli_query($connection, "SELECT GET_LOCK('$escapedLockName', 10) AS acquired");
    if (!$lockResult) {
        error_log('Unable to acquire admin registration lock: ' . mysqli_error($connection));
        throw new RuntimeException('Admin registration is temporarily unavailable.');
    }

    $lock = mysqli_fetch_assoc($lockResult);
    if (!$lock || (int) $lock['acquired'] !== 1) {
        throw new RuntimeException('Unable to acquire the admin registration lock.');
    }

    try {
        if (admin_has_accounts()) {
            return false;
        }

        $passwordColumn = admin_password_column();
        $name = mysqli_real_escape_string($connection, $name);
        $email = mysqli_real_escape_string($connection, $email);
        $passwordHash = mysqli_real_escape_string($connection, password_hash($password, PASSWORD_DEFAULT));
        $created = mysqli_query(
            $connection,
            "INSERT INTO admins (name, email, $passwordColumn) VALUES ('$name', '$email', '$passwordHash')"
        );
        if (!$created) {
            error_log('Unable to create admin account: ' . mysqli_error($connection));
            throw new RuntimeException('Admin registration is temporarily unavailable.');
        }

        return true;
    } finally {
        if (!mysqli_query($connection, "SELECT RELEASE_LOCK('$escapedLockName')")) {
            error_log('Unable to release admin registration lock: ' . mysqli_error($connection));
        }
    }
}

function admin_find_by_email($email)
{
    $connection = admin_db_connection();
    $passwordColumn = admin_password_column();
    $email = mysqli_real_escape_string($connection, $email);
    $result = mysqli_query(
        $connection,
        "SELECT id, name, email, $passwordColumn AS password_hash
         FROM admins WHERE email = '$email' LIMIT 1"
    );
    if (!$result) {
        error_log('Unable to find admin account: ' . mysqli_error($connection));
        throw new RuntimeException('Admin authentication is temporarily unavailable.');
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function admin_find_by_id($id)
{
    $id = (int) $id;
    $result = mysqli_query(
        admin_db_connection(),
        "SELECT id, name, email FROM admins WHERE id = $id LIMIT 1"
    );
    if (!$result) {
        error_log('Unable to load admin account: ' . mysqli_error(admin_db_connection()));
        throw new RuntimeException('Admin account is temporarily unavailable.');
    }

    return mysqli_fetch_assoc($result) ?: false;
}

function admin_change_password($id, $currentPassword, $newPassword)
{
    $connection = admin_db_connection();
    $passwordColumn = admin_password_column();
    $adminId = (int) $id;
    $result = mysqli_query(
        $connection,
        "SELECT $passwordColumn AS password_hash FROM admins WHERE id = $adminId LIMIT 1"
    );
    if (!$result) {
        error_log('Unable to load admin password: ' . mysqli_error($connection));
        throw new RuntimeException('Admin password could not be changed.');
    }

    $admin = mysqli_fetch_assoc($result);
    if (!$admin || empty($admin['password_hash']) || !password_verify($currentPassword, $admin['password_hash'])) {
        return false;
    }

    $passwordHash = mysqli_real_escape_string(
        $connection,
        password_hash($newPassword, PASSWORD_DEFAULT)
    );
    $updated = mysqli_query(
        $connection,
        "UPDATE admins SET $passwordColumn = '$passwordHash' WHERE id = $adminId"
    );
    if (!$updated) {
        error_log('Unable to update admin password: ' . mysqli_error($connection));
        throw new RuntimeException('Admin password could not be changed.');
    }

    return mysqli_affected_rows($connection) === 1;
}
