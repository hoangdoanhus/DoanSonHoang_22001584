<?php

declare(strict_types=1);

const MAX_PRICE = 99999999.99;
const MAX_QUANTITY = 2147483647;
const MAX_NAME_LENGTH = 100;

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
    $value = $_POST[$key] ?? '';

    return is_scalar($value) ? trim((string) $value) : '';
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
