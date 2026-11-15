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

function admin_issue_password_reset_token($email)
{
    $admin = admin_find_by_email($email);
    if (!$admin) {
        return false;
    }

    $connection = admin_db_connection();
    $adminId = (int) $admin['id'];
    $cooldown = mysqli_prepare(
        $connection,
        'SELECT id FROM admin_password_resets '
        . 'WHERE admin_id = ? AND created_at >= CURRENT_TIMESTAMP - INTERVAL 60 SECOND LIMIT 1'
    );
    if (!$cooldown) {
        error_log('Unable to prepare admin reset throttling: ' . mysqli_error($connection));
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_bind_param($cooldown, 'i', $adminId);
    if (!mysqli_stmt_execute($cooldown)) {
        error_log('Unable to check admin reset throttling: ' . mysqli_stmt_error($cooldown));
        mysqli_stmt_close($cooldown);
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_store_result($cooldown);
    $isCoolingDown = mysqli_stmt_num_rows($cooldown) > 0;
    mysqli_stmt_close($cooldown);
    if ($isCoolingDown) {
        return false;
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = gmdate('Y-m-d H:i:s', time() + 3600);
    $statement = mysqli_prepare(
        $connection,
        'INSERT INTO admin_password_resets (admin_id, token_hash, expires_at) VALUES (?, ?, ?) '
        . 'ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), '
        . 'expires_at = VALUES(expires_at), created_at = CURRENT_TIMESTAMP'
    );
    if (!$statement) {
        error_log('Unable to prepare admin password reset: ' . mysqli_error($connection));
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_bind_param($statement, 'iss', $adminId, $tokenHash, $expiresAt);
    if (!mysqli_stmt_execute($statement)) {
        error_log('Unable to create admin password reset: ' . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_close($statement);
    return $token;
}

function admin_password_reset_token_is_valid($token)
{
    if (!is_string($token) || !preg_match('/\A[a-f0-9]{64}\z/', $token)) {
        return false;
    }

    $connection = admin_db_connection();
    $tokenHash = hash('sha256', $token);
    $statement = mysqli_prepare(
        $connection,
        'SELECT id FROM admin_password_resets '
        . 'WHERE token_hash = ? AND expires_at > UTC_TIMESTAMP() LIMIT 1'
    );
    if (!$statement) {
        error_log('Unable to prepare admin reset-token lookup: ' . mysqli_error($connection));
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_bind_param($statement, 's', $tokenHash);
    if (!mysqli_stmt_execute($statement)) {
        error_log('Unable to check admin reset token: ' . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_store_result($statement);
    $isValid = mysqli_stmt_num_rows($statement) === 1;
    mysqli_stmt_close($statement);
    return $isValid;
}

function admin_delete_password_reset_token($token)
{
    if (!is_string($token) || !preg_match('/\A[a-f0-9]{64}\z/', $token)) {
        return false;
    }

    $connection = admin_db_connection();
    $tokenHash = hash('sha256', $token);
    $statement = mysqli_prepare(
        $connection,
        'DELETE FROM admin_password_resets WHERE token_hash = ?'
    );
    if (!$statement) {
        error_log('Unable to prepare admin reset-token deletion: ' . mysqli_error($connection));
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    mysqli_stmt_bind_param($statement, 's', $tokenHash);
    if (!mysqli_stmt_execute($statement)) {
        error_log('Unable to delete admin reset token: ' . mysqli_stmt_error($statement));
        mysqli_stmt_close($statement);
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    $deleted = mysqli_stmt_affected_rows($statement) === 1;
    mysqli_stmt_close($statement);
    return $deleted;
}

function admin_reset_password_with_token($token, $newPassword)
{
    if (!is_string($token) || !preg_match('/\A[a-f0-9]{64}\z/', $token)) {
        return false;
    }

    $connection = admin_db_connection();
    $passwordColumn = admin_password_column();
    $tokenHash = hash('sha256', $token);

    if (!mysqli_begin_transaction($connection)) {
        error_log('Unable to begin admin password reset: ' . mysqli_error($connection));
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }

    try {
        $lookup = mysqli_prepare(
            $connection,
            'SELECT admin_id FROM admin_password_resets '
            . 'WHERE token_hash = ? AND expires_at > UTC_TIMESTAMP() LIMIT 1 FOR UPDATE'
        );
        if (!$lookup) {
            throw new RuntimeException('Unable to prepare reset-token lookup: ' . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($lookup, 's', $tokenHash);
        if (!mysqli_stmt_execute($lookup)) {
            throw new RuntimeException('Unable to read reset token: ' . mysqli_stmt_error($lookup));
        }

        mysqli_stmt_bind_result($lookup, $adminId);
        $found = mysqli_stmt_fetch($lookup);
        mysqli_stmt_close($lookup);

        if (!$found) {
            mysqli_rollback($connection);
            return false;
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = mysqli_prepare(
            $connection,
            "UPDATE admins SET `$passwordColumn` = ? WHERE id = ?"
        );
        if (!$update) {
            throw new RuntimeException('Unable to prepare password update: ' . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($update, 'si', $passwordHash, $adminId);
        if (!mysqli_stmt_execute($update) || mysqli_stmt_affected_rows($update) !== 1) {
            throw new RuntimeException('Unable to update admin password: ' . mysqli_stmt_error($update));
        }
        mysqli_stmt_close($update);

        $delete = mysqli_prepare(
            $connection,
            'DELETE FROM admin_password_resets WHERE token_hash = ?'
        );
        if (!$delete) {
            throw new RuntimeException('Unable to prepare reset-token cleanup: ' . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($delete, 's', $tokenHash);
        if (!mysqli_stmt_execute($delete) || mysqli_stmt_affected_rows($delete) !== 1) {
            throw new RuntimeException('Unable to consume reset token: ' . mysqli_stmt_error($delete));
        }
        mysqli_stmt_close($delete);

        if (!mysqli_commit($connection)) {
            throw new RuntimeException('Unable to commit admin password reset: ' . mysqli_error($connection));
        }

        return true;
    } catch (Throwable $exception) {
        mysqli_rollback($connection);
        error_log('Admin password reset failed: ' . $exception->getMessage());
        throw new RuntimeException('Password reset is temporarily unavailable.');
    }
}
