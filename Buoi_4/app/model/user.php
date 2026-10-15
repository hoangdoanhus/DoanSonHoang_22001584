<?php

declare(strict_types=1);

require_once __DIR__ . '/../common/dbConnect.php';

function findUserByUsername(string $username): ?array
{
    $statement = getConnection()->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $statement->execute([$username]);
    $user = $statement->fetch();

    return $user === false ? null : $user;
}

function isUsernameTaken(string $username): bool
{
    $statement = getConnection()->prepare('SELECT 1 FROM users WHERE username = ?');
    $statement->execute([$username]);

    return $statement->fetchColumn() !== false;
}

/**
 * @param string $passwordHash Kết quả của password_hash(), không bao giờ là mật khẩu thuần.
 */
function createUser(string $username, string $passwordHash): int
{
    $connection = getConnection();
    $statement = $connection->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $statement->execute([$username, $passwordHash]);

    return (int) $connection->lastInsertId();
}
