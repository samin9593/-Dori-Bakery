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
    'INSERT INTO cart (user_email, product_id, quantity) VALUES (?, ?, 1)
     ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + 1, ?)',
    [$_SESSION['user_email'], $productId, MAX_CART_QUANTITY]
);

if ($conn_search->affected_rows === 0) {
    http_response_code(409);
    exit;
}

$result = $conn_search->execute_query(
    'SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_email = ?',
    [$_SESSION['user_email']]
);
header('Content-Type: application/json');
echo json_encode(['cartCount' => (int) $result->fetch_assoc()['total']]);
