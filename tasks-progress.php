<?php

require_once __DIR__ . '/config/DbClasses.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

function respond_with_json($statusCode, array $data)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function ensure_task_progress_table()
{
    mysqli_query(
        db_connect(),
        'CREATE TABLE IF NOT EXISTS project_task_progress (
            task_id VARCHAR(64) NOT NULL PRIMARY KEY,
            completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}

function get_completed_tasks()
{
    ensure_task_progress_table();
    $result = mysqli_query(db_connect(), 'SELECT task_id FROM project_task_progress ORDER BY task_id');
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return array_column($rows, 'task_id');
}

function set_task_completed($taskId, $completed)
{
    ensure_task_progress_table();

    if ($completed) {
        $statement = mysqli_prepare(
            db_connect(),
            'INSERT INTO project_task_progress (task_id) VALUES (?)
            ON DUPLICATE KEY UPDATE completed_at = CURRENT_TIMESTAMP'
        );
        mysqli_stmt_bind_param($statement, 's', $taskId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        return;
    }

    $statement = mysqli_prepare(db_connect(), 'DELETE FROM project_task_progress WHERE task_id = ?');
    mysqli_stmt_bind_param($statement, 's', $taskId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

$taskIds = [
    'project-setup',
    'product-catalog',
    'customer-accounts',
    'cart-wishlist',
    'checkout-orders',
    'payments-fulfillment',
    'admin-operations',
    'launch-readiness',
];

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET' && $method !== 'POST') {
    header('Allow: GET, POST');
    respond_with_json(405, ['error' => 'Method not allowed.']);
}

if ($method === 'POST') {
    $request = json_decode(file_get_contents('php://input'));
    if (
        json_last_error() !== JSON_ERROR_NONE ||
        !is_object($request) ||
        !isset($request->taskId, $request->completed) ||
        !is_string($request->taskId) ||
        !is_bool($request->completed) ||
        !in_array($request->taskId, $taskIds, true)
    ) {
        respond_with_json(400, ['error' => 'Invalid task progress data.']);
    }
}

try {
    if ($method === 'GET') {
        respond_with_json(200, ['completedTasks' => get_completed_tasks()]);
    }

    set_task_completed($request->taskId, $request->completed);
    respond_with_json(200, ['success' => true]);
} catch (mysqli_sql_exception $exception) {
    error_log('Task progress database error: ' . $exception->getMessage());
    respond_with_json(500, ['error' => 'Shared task progress is temporarily unavailable.']);
}
