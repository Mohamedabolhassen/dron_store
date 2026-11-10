<?php

$host     = "localhost";
$username = "root";
$password = "";
$dbName   = getenv("DRONE_STORE_DB") ?: "drone_store";

mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect($host, $username, $password, $dbName);

if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("The store is temporarily unavailable.");
}

if (!mysqli_set_charset($con, "utf8mb4")) {
    error_log("Unable to set database connection character set: " . mysqli_error($con));
    http_response_code(500);
    exit("The store is temporarily unavailable.");
}
