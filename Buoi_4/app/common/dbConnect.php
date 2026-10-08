<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'shopping_cart';
const DB_USER = 'root';
const DB_PASS = '';

function getConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

    try {
        $connection = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('Ket noi database that bai: ' . $e->getMessage());

        http_response_code(500);
        exit(sprintf(
            'Không thể kết nối tới database "%s". Hãy kiểm tra MySQL đã chạy chưa '
            . 'và thông tin kết nối trong common/dbConnect.php.',
            DB_NAME
        ));
    }

    return $connection;
}
