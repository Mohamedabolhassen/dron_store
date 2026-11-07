<?php

require_once __DIR__ . '/../../config/DbClasses.php';

function admin_password_column()
{
    $result = mysqli_query(db_connect(), 'SHOW COLUMNS FROM admins');
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
    $result = mysqli_query(db_connect(), 'SELECT id FROM admins LIMIT 1');
    return mysqli_fetch_assoc($result) !== null;
}

function admin_create_first_account($name, $email, $password)
{
    $connection = db_connect();
    $lockName = 'dron_store_first_admin_registration';
    $lock = mysqli_prepare($connection, 'SELECT GET_LOCK(?, 10)');
    mysqli_stmt_bind_param($lock, 's', $lockName);
    mysqli_stmt_execute($lock);
    mysqli_stmt_bind_result($lock, $lockAcquired);
    mysqli_stmt_fetch($lock);
    mysqli_stmt_close($lock);

    if ((int) $lockAcquired !== 1) {
        throw new RuntimeException('Unable to acquire the admin registration lock.');
    }

    try {
        if (admin_has_accounts()) {
            return false;
        }

        $passwordColumn = admin_password_column();
        $statement = mysqli_prepare(
            $connection,
            'INSERT INTO admins (name, email, ' . $passwordColumn . ') VALUES (?, ?, ?)'
        );
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($statement, 'sss', $name, $email, $passwordHash);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return true;
    } finally {
        $releaseLock = mysqli_prepare($connection, 'SELECT RELEASE_LOCK(?)');
        mysqli_stmt_bind_param($releaseLock, 's', $lockName);
        mysqli_stmt_execute($releaseLock);
        mysqli_stmt_close($releaseLock);
    }
}

function admin_find_by_email($email)
{
    $passwordColumn = admin_password_column();
    $statement = mysqli_prepare(
        db_connect(),
        'SELECT id, name, email, ' . $passwordColumn . ' AS password_hash FROM admins WHERE email = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $admin = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);

    return $admin ?: false;
}

function admin_find_by_id($id)
{
    $statement = mysqli_prepare(
        db_connect(),
        'SELECT id, name, email FROM admins WHERE id = ? LIMIT 1'
    );
    $adminId = (int) $id;
    mysqli_stmt_bind_param($statement, 'i', $adminId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $admin = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);

    return $admin ?: false;
}

function admin_change_password($id, $currentPassword, $newPassword)
{
    $connection = db_connect();
    $passwordColumn = admin_password_column();
    $adminId = (int) $id;
    $statement = mysqli_prepare(
        $connection,
        'SELECT ' . $passwordColumn . ' AS password_hash FROM admins WHERE id = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'i', $adminId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $admin = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);

    if (!$admin || empty($admin['password_hash']) || !password_verify($currentPassword, $admin['password_hash'])) {
        return false;
    }

    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $statement = mysqli_prepare(
        $connection,
        'UPDATE admins SET ' . $passwordColumn . ' = ? WHERE id = ?'
    );
    mysqli_stmt_bind_param($statement, 'si', $passwordHash, $adminId);
    mysqli_stmt_execute($statement);
    $changed = mysqli_stmt_affected_rows($statement) === 1;
    mysqli_stmt_close($statement);

    return $changed;
}
