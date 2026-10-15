<?php

declare(strict_types=1);

const MAX_PRICE = 99999999.99;
const MAX_QUANTITY = 2147483647;
const MAX_NAME_LENGTH = 100;
const MIN_USERNAME_LENGTH = 3;
const MAX_USERNAME_LENGTH = 50;
const MIN_PASSWORD_LENGTH = 6;
const MAX_PASSWORD_LENGTH = 72;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatMoney(float|int|string $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' đ';
}

function redirect(string $page): void
{
    header('Location: ' . $page);
    exit;
}

function postString(string $key): string
{
    return trim(postRaw($key));
}

/**
 * Lấy dữ liệu POST mà không cắt khoảng trắng, dùng cho mật khẩu.
 */
function postRaw(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_scalar($value) ? (string) $value : '';
}

function parseId(mixed $value): ?int
{
    if (!is_scalar($value)) {
        return null;
    }

    $id = filter_var((string) $value, FILTER_VALIDATE_INT);

    return ($id === false || $id <= 0) ? null : $id;
}

function setFlash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}

/**
 * @return array{errors: string[], name: string, price: float, quantity: int}
 */
function validateProductInput(string $name, string $price, string $quantity): array
{
    $errors = [];

    if ($name === '') {
        $errors[] = 'Tên sản phẩm không được để trống.';
    } elseif (mb_strlen($name, 'UTF-8') > MAX_NAME_LENGTH) {
        $errors[] = 'Tên sản phẩm không được dài hơn ' . MAX_NAME_LENGTH . ' ký tự.';
    }

    $parsedPrice = 0.0;

    if ($price === '') {
        $errors[] = 'Giá sản phẩm không được để trống.';
    } elseif (!is_numeric($price)) {
        $errors[] = 'Giá sản phẩm phải là một số.';
    } else {
        $parsedPrice = (float) $price;

        if ($parsedPrice <= 0) {
            $errors[] = 'Giá sản phẩm phải lớn hơn 0.';
        } elseif ($parsedPrice > MAX_PRICE) {
            $errors[] = 'Giá sản phẩm không được vượt quá ' . formatMoney(MAX_PRICE) . '.';
        }
    }

    $parsedQuantity = 0;

    if ($quantity === '') {
        $errors[] = 'Số lượng không được để trống.';
    } elseif (filter_var($quantity, FILTER_VALIDATE_INT) === false) {
        $errors[] = 'Số lượng phải là số nguyên.';
    } else {
        $parsedQuantity = (int) $quantity;

        if ($parsedQuantity < 0) {
            $errors[] = 'Số lượng không được nhỏ hơn 0.';
        } elseif ($parsedQuantity > MAX_QUANTITY) {
            $errors[] = 'Số lượng không được vượt quá ' . number_format(MAX_QUANTITY, 0, ',', '.') . '.';
        }
    }

    return [
        'errors'   => $errors,
        'name'     => $name,
        'price'    => $parsedPrice,
        'quantity' => $parsedQuantity,
    ];
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function isCsrfValid(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    return is_string($token) && $token !== '' && hash_equals(csrfToken(), $token);
}

/**
 * @return string[]
 */
function validateRegistration(string $username, string $password, string $passwordConfirm): array
{
    $errors = [];

    if ($username === '') {
        $errors[] = 'Tên đăng nhập không được để trống.';
    } elseif (preg_match('/^[A-Za-z0-9_]+$/', $username) !== 1) {
        $errors[] = 'Tên đăng nhập chỉ được gồm chữ cái, chữ số và dấu gạch dưới.';
    } elseif (strlen($username) < MIN_USERNAME_LENGTH || strlen($username) > MAX_USERNAME_LENGTH) {
        $errors[] = sprintf(
            'Tên đăng nhập phải dài từ %d đến %d ký tự.',
            MIN_USERNAME_LENGTH,
            MAX_USERNAME_LENGTH
        );
    }

    if ($password === '') {
        $errors[] = 'Mật khẩu không được để trống.';
    } elseif (strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors[] = 'Mật khẩu phải có ít nhất ' . MIN_PASSWORD_LENGTH . ' ký tự.';
    } elseif (strlen($password) > MAX_PASSWORD_LENGTH) {
        // bcrypt chỉ dùng 72 byte đầu, mật khẩu dài hơn sẽ bị cắt âm thầm.
        $errors[] = 'Mật khẩu không được dài hơn ' . MAX_PASSWORD_LENGTH . ' byte.';
    }

    if ($passwordConfirm !== $password) {
        $errors[] = 'Mật khẩu nhập lại không khớp.';
    }

    return $errors;
}
