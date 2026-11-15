<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('DRONE_STORE_HOST');
$host = $host === false || $host === '' ? 'localhost' : $host;
$username = getenv('DRONE_STORE_USER');
$username = $username === false || $username === '' ? 'root' : $username;
$password = getenv('DRONE_STORE_PASSWORD');
$password = $password === false ? '' : $password;
$databaseName = getenv('DRONE_STORE_DB') ?: 'drone_store';
$port = getenv('DRONE_STORE_PORT');
$port = $port === false || $port === '' ? 3306 : (int) $port;
$skipDemoData = in_array('--skip-demo-data', $argv, true);

if (!preg_match('/\A[a-zA-Z0-9_]{1,64}\z/', $databaseName)) {
    throw new InvalidArgumentException('DRONE_STORE_DB must contain only letters, numbers, and underscores.');
}

if ($port < 1 || $port > 65535) {
    throw new InvalidArgumentException('DRONE_STORE_PORT must be between 1 and 65535.');
}

try {
    $connection = mysqli_connect($host, $username, $password, '', $port);
    mysqli_set_charset($connection, 'utf8mb4');

    mysqli_query(
        $connection,
        "CREATE DATABASE IF NOT EXISTS `$databaseName` "
        . 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );
    mysqli_select_db($connection, $databaseName);

    mysqli_query(
        $connection,
        'CREATE TABLE IF NOT EXISTS schema_migrations ('
        . 'migration VARCHAR(191) NOT NULL, '
        . 'applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, '
        . 'PRIMARY KEY (migration)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $migrationName = '001_initial_schema';
    $checkMigration = mysqli_prepare(
        $connection,
        'SELECT migration FROM schema_migrations WHERE migration = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($checkMigration, 's', $migrationName);
    mysqli_stmt_execute($checkMigration);
    mysqli_stmt_store_result($checkMigration);
    $migrationApplied = mysqli_stmt_num_rows($checkMigration) > 0;
    mysqli_stmt_close($checkMigration);

    if (!$migrationApplied) {
        $schemaPath = __DIR__ . '/../database.sql';
        $schema = file_get_contents($schemaPath);
        if ($schema === false) {
            throw new RuntimeException('Unable to read database.sql.');
        }

        $schemaHeader = '/\A\s*CREATE DATABASE IF NOT EXISTS drone_store\s+'
            . 'CHARACTER SET utf8mb4\s+COLLATE utf8mb4_unicode_ci;\s*'
            . 'USE drone_store;\s*/i';
        $schema = preg_replace($schemaHeader, '', $schema, 1, $headerMatches);
        if ($schema === null || $headerMatches !== 1) {
            throw new RuntimeException('database.sql does not have the expected database header.');
        }

        mysqli_multi_query($connection, $schema);
        do {
            $result = mysqli_store_result($connection);
            if ($result instanceof mysqli_result) {
                mysqli_free_result($result);
            }
        } while (mysqli_more_results($connection) && mysqli_next_result($connection));

        $recordMigration = mysqli_prepare(
            $connection,
            'INSERT INTO schema_migrations (migration) VALUES (?)'
        );
        mysqli_stmt_bind_param($recordMigration, 's', $migrationName);
        mysqli_stmt_execute($recordMigration);
        mysqli_stmt_close($recordMigration);
        echo "Applied database migration: $migrationName." . PHP_EOL;
    } else {
        echo "Database migration already applied: $migrationName." . PHP_EOL;
    }

    mysqli_close($connection);

    if ($skipDemoData) {
        echo 'Demo data seeding skipped.' . PHP_EOL;
    } else {
        require __DIR__ . '/seed_demo_data.php';
    }

    echo "Database setup complete for `$databaseName`." . PHP_EOL;
} catch (Throwable $exception) {
    if (isset($connection) && $connection instanceof mysqli) {
        mysqli_close($connection);
    }

    fwrite(STDERR, 'Database setup failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
