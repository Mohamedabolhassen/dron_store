<?php

require_once __DIR__ . '/../models/Order.php';

function admin_order_controller_update_status($id, $action)
{
    if (!is_string($id) || !ctype_digit($id) || (int) $id < 1) {
        return 'invalid_order';
    }

    $statuses = [
        'approve' => 'processing',
        'reject' => 'cancelled',
    ];
    if (!is_string($action) || !isset($statuses[$action])) {
        return 'invalid_action';
    }

    return admin_order_update_pending_status((int) $id, $statuses[$action]);
}
