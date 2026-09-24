<?php

declare(strict_types=1);

function beginOutput(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<pre style="font-family: Consolas, monospace; font-size: 14px;">';
    register_shutdown_function(static function (): void {
        echo '</pre>';
    });
}

function printLine(string $text = ''): void
{
    echo PHP_SAPI === 'cli' ? $text : htmlspecialchars($text, ENT_QUOTES, 'UTF-8'), PHP_EOL;
}

function formatMoney(float $amount): string
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

function padColumn(string $text, int $width, bool $alignRight = false): string
{
    $padding = str_repeat(' ', max(0, $width - mb_strlen($text, 'UTF-8')));

    return $alignRight ? $padding . $text : $text . $padding;
}
