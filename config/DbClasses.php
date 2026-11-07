<?php

function db_connect()
{
    static $connection = null;

    if ($connection === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = mysqli_connect('localhost', 'root', '', 'dropuhut_ecommerce');
        mysqli_set_charset($connection, 'utf8mb4');
    }

    return $connection;
}
