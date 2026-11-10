<?php
require __DIR__ . '/common.php';
require_post();
$email = require_login();
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 3600,
    'path' => $params['path'],
    'domain' => $params['domain'],
    'secure' => $params['secure'],
    'httponly' => $params['httponly'],
    'samesite' => $params['samesite'],
]);
session_destroy();

require __DIR__ . '/db.php';
try {
    $conn->execute_query("INSERT INTO user_logs (user_email, action_type) VALUES (?, 'logout')", [$email]);
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
}

header('Location: ../video/mainpage.php', true, 303);
exit;
