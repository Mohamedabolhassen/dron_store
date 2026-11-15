<?php

$host = getenv("DRONE_STORE_HOST");
$host = $host === false || $host === '' ? 'localhost' : $host;
$username = getenv("DRONE_STORE_USER");
$username = $username === false || $username === '' ? 'root' : $username;
$password = getenv("DRONE_STORE_PASSWORD");
$password = $password === false ? '' : $password;
$dbName = getenv("DRONE_STORE_DB") ?: 'drone_store';
$port = getenv("DRONE_STORE_PORT");
$port = $port === false || $port === '' ? 3306 : (int) $port;

mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect($host, $username, $password, $dbName, $port);

if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("The store is temporarily unavailable.");
}

if (!mysqli_set_charset($con, "utf8mb4")) {
    error_log("Unable to set database connection character set: " . mysqli_error($con));
    http_response_code(500);  //problem  in server
    exit("The store is temporarily unavailable.");
}
