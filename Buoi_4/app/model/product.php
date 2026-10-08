<?php

declare(strict_types=1);

require_once __DIR__ . '/../common/dbConnect.php';

function getAllProducts(): array
{
    return getConnection()
        ->query('SELECT id, name, price, quantity FROM products ORDER BY id')
        ->fetchAll();
}

function getProductById(int $id): ?array
{
    $statement = getConnection()->prepare('SELECT id, name, price, quantity FROM products WHERE id = ?');
    $statement->execute([$id]);
    $product = $statement->fetch();

    return $product === false ? null : $product;
}

function addProduct(string $name, float $price, int $quantity): int
{
    $connection = getConnection();
    $statement = $connection->prepare('INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)');
    $statement->execute([$name, $price, $quantity]);

    return (int) $connection->lastInsertId();
}

/**
 * @return bool false khi không có dòng nào bị thay đổi (dữ liệu mới giống dữ liệu cũ).
 */
function updateProduct(int $id, string $name, float $price, int $quantity): bool
{
    $statement = getConnection()->prepare(
        'UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?'
    );
    $statement->execute([$name, $price, $quantity, $id]);

    return $statement->rowCount() > 0;
}

function deleteProduct(int $id): bool
{
    $statement = getConnection()->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);

    return $statement->rowCount() > 0;
}
