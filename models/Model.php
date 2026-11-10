<?php

function modelEscape($con, $value)
{
    return mysqli_real_escape_string($con, (string) $value);
}

function modelQuery($con, $sql, $context)
{
    $result = mysqli_query($con, $sql);
    if (!$result) {
        error_log($context . ': ' . mysqli_error($con));
    }

    return $result;
}
