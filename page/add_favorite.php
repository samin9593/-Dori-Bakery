<?php
require __DIR__ . '/common.php';
if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    exit;
}
require_post();

$productId = positive_id($_POST['product_id'] ?? null);
require __DIR__ . '/db_search.php';

$result = $conn_search->execute_query('SELECT id FROM products WHERE id = ?', [$productId]);
if (!$result->num_rows) {
    http_response_code(404);
    exit;
}

$conn_search->execute_query(
    'INSERT INTO favorites (user_email, product_id) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE product_id = VALUES(product_id)',
    [$_SESSION['user_email'], $productId]
);
http_response_code(204);
