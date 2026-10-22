<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<pre style="font-family: Consolas, monospace; font-size: 14px;">';
    register_shutdown_function(static function (): void {
        echo '</pre>';
    });
}

$productPrices = [
    'Bút bi Thiên Long'   => 5000,
    'Vở ô ly 200 trang'   => 18000,
    'Hộp bút nhựa'        => 45000,
    'Sách Lập trình PHP'  => 150000,
    'Balo học sinh'       => 250000,
    'Máy tính Casio 580'  => 520000,
];

function calculateTotal(array $prices): float
{
    $total = 0.0;

    foreach ($prices as $name => $price) {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException(sprintf('Giá của "%s" không phải là số.', (string) $name));
        }

        if ($price < 0) {
            throw new InvalidArgumentException(sprintf('Giá của "%s" không được âm.', (string) $name));
        }

        $total += (float) $price;
    }

    return $total;
}

function findMaxPrice(array $prices): float
{
    if ($prices === []) {
        throw new UnderflowException('Danh sách sản phẩm đang trống, không có giá cao nhất.');
    }

    $max = null;

    foreach ($prices as $name => $price) {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException(sprintf('Giá của "%s" không phải là số.', (string) $name));
        }

        $price = (float) $price;

        if ($max === null || $price > $max) {
            $max = $price;
        }
    }

    return $max;
}

function formatMoney(float $amount): string
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

echo 'DANH SÁCH GIÁ SẢN PHẨM', PHP_EOL;

foreach ($productPrices as $name => $price) {
    echo '- ', $name, ': ', formatMoney((float) $price), PHP_EOL;
}

echo PHP_EOL;

try {
    $total = calculateTotal($productPrices);
    $maxPrice = findMaxPrice($productPrices);

    // array_search trả về tên sản phẩm ứng với mức giá cao nhất vừa tìm được.
    $maxProduct = array_search($maxPrice, array_map('floatval', $productPrices), true);

    echo 'Số sản phẩm: ', count($productPrices), PHP_EOL;
    echo 'Tổng giá trị: ', formatMoney($total), PHP_EOL;
    echo 'Giá cao nhất: ', formatMoney($maxPrice);
    echo $maxProduct === false ? '' : ' (' . $maxProduct . ')';
    echo PHP_EOL;
} catch (InvalidArgumentException | UnderflowException $e) {
    echo '[LỖI] ', $e->getMessage(), PHP_EOL;
}
