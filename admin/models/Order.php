<?php

require_once __DIR__ . '/../../config/db.php';

function admin_order_update_pending_status($id, $status)
{
    global $con;

    $id = (int) $id;
    if (!in_array($status, ['processing', 'cancelled'], true)) {
        return 'invalid_status';
    }

    $escapedStatus = mysqli_real_escape_string($con, $status);
    if (!mysqli_begin_transaction($con)) {
        error_log('Unable to start order status transaction: ' . mysqli_error($con));
        return 'error';
    }

    $result = mysqli_query(
        $con,
        "UPDATE orders SET order_status = '$escapedStatus'
         WHERE id = $id AND order_status = 'pending'"
    );

    if (!$result) {
        error_log('Unable to update order status: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    if (mysqli_affected_rows($con) !== 1) {
        mysqli_rollback($con);
        $exists = mysqli_query($con, "SELECT id FROM orders WHERE id = $id LIMIT 1");
        if (!$exists) {
            error_log('Unable to verify order status: ' . mysqli_error($con));
            return 'error';
        }
        return mysqli_fetch_assoc($exists) ? 'not_pending' : 'not_found';
    }

    if ($status === 'cancelled') {
        $restocked = mysqli_query(
            $con,
            "UPDATE products p
             JOIN (
                 SELECT product_id, SUM(quantity) AS quantity
                 FROM order_items
                 WHERE order_id = $id
                 GROUP BY product_id
             ) oi ON oi.product_id = p.id
             SET p.stock = p.stock + oi.quantity"
        );
        if (!$restocked) {
            error_log('Unable to restore stock for rejected order: ' . mysqli_error($con));
            mysqli_rollback($con);
            return 'error';
        }
    }

    $notes = $status === 'processing' ? 'Order approved by admin' : 'Order rejected by admin';
    $escapedNotes = mysqli_real_escape_string($con, $notes);
    $logged = mysqli_query(
        $con,
        "INSERT INTO tracking_logs (order_id, status_step, notes)
         VALUES ($id, '$escapedStatus', '$escapedNotes')"
    );
    if (!$logged) {
        error_log('Unable to save order status tracking log: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    if (!mysqli_commit($con)) {
        error_log('Unable to commit order status transaction: ' . mysqli_error($con));
        mysqli_rollback($con);
        return 'error';
    }

    return 'updated';
}
