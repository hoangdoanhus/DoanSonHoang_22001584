<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../model/user.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function currentUsername(): string
{
    return isLoggedIn() ? (string) $_SESSION['username'] : '';
}

function currentUserId(): ?int
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    setFlash('Bạn cần đăng nhập để sử dụng chức năng này.', 'error');
    redirect('login.php');
}

/**
 * Trang đăng nhập và đăng ký không dành cho người đã đăng nhập.
 */
function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('product_list.php');
    }
}

function loginUser(array $user): void
{
    // Cấp session id mới để chống session fixation.
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = (string) $user['username'];
}

function logoutUser(): void
{
    // Xóa toàn bộ dữ liệu phiên rồi cấp session id mới, phiên cũ bị hủy trên server.
    $_SESSION = [];
    session_regenerate_id(true);
}
