<?php

declare(strict_types=1);

const DB_DSN  = 'mysql:host=127.0.0.1;port=3306;dbname=buoi9_api;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';

const UPLOADS_FOLDER = 'uploads';

define('UPLOADS_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS_FOLDER);

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(int $status, string $message): void
{
    respond($status, ['success' => false, 'message' => $message]);
}

function connect(): PDO
{
    return new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

function toInteger($value): ?int
{
    // filter_var đổi true thành 1, nhưng true không phải số nên loại bỏ trước.
    if (is_bool($value)) {
        return null;
    }

    $number = filter_var($value, FILTER_VALIDATE_INT);

    return $number === false ? null : $number;
}

/**
 * @throws InvalidArgumentException khi URL không có id hợp lệ
 */
function readId(): int
{
    $id = toInteger($_GET['id'] ?? null);

    if ($id === null || $id <= 0) {
        throw new InvalidArgumentException('URL phải có mã sản phẩm hợp lệ, ví dụ products.php?id=1.');
    }

    return $id;
}

/**
 * Ép kiểu để JSON trả số, vì tùy cấu hình MySQL có thể trả cột số dưới dạng chuỗi.
 */
function mapProduct(array $row): array
{
    return [
        'id'    => (int) $row['id'],
        'name'  => (string) $row['name'],
        'price' => (int) $row['price'],
        'stock' => (int) $row['stock'],
        'image' => $row['image'] === null ? null : (string) $row['image'],
    ];
}

function findProduct(PDO $db, int $id): ?array
{
    $statement = $db->prepare('SELECT id, name, price, stock, image FROM products WHERE id = ?');
    $statement->execute([$id]);
    $product = $statement->fetch();

    return $product === false ? null : mapProduct($product);
}

function deleteImageFile(?string $image): void
{
    if ($image === null || $image === '') {
        return;
    }

    // basename() để đường dẫn lưu trong database không trỏ ra ngoài thư mục uploads.
    $path = UPLOADS_DIR . DIRECTORY_SEPARATOR . basename($image);

    if (is_file($path)) {
        unlink($path);
    }
}
