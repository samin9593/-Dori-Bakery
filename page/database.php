<?php

function connect_database(string $database): mysqli
{
    $file = __DIR__ . '/../config.local.php';
    if (!is_file($file)) {
        http_response_code(503);
        exit('Copy config.example.php to config.local.php and configure the database.');
    }

    $config = require $file;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $connection = new mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config[$database],
            $config['port']
        );
        $connection->set_charset('utf8mb4');
        return $connection;
    } catch (mysqli_sql_exception $exception) {
        error_log($exception->getMessage());
        http_response_code(503);
        exit('Database connection unavailable. Check the local configuration.');
    }
}
