<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/CartItem.php';
require_once __DIR__ . '/ShoppingCart.php';

function addProduct(ShoppingCart $cart, string $name, float $price, int $quantity): void
{
    try {
        $item = new CartItem($name, $price, $quantity);
        $isNew = $cart->addItem($item);

        if ($isNew) {
            printLine(sprintf(
                '[OK] Đã thêm "%s": %s x %d.',
                $item->getName(),
                formatMoney($item->getPrice()),
                $item->getQuantity()
            ));
        } else {
            printLine(sprintf(
                '[OK] "%s" đã có trong giỏ, cộng dồn thêm %d sản phẩm.',
                $item->getName(),
                $item->getQuantity()
            ));
        }
    } catch (InvalidArgumentException | RangeException $e) {
        printLine('[LỖI] ' . $e->getMessage());
    }
}

function removeProduct(ShoppingCart $cart, string $name): void
{
    try {
        printLine(sprintf('[OK] Đã xóa "%s" khỏi giỏ hàng.', $cart->removeItem($name)->getName()));
    } catch (InvalidArgumentException | OutOfBoundsException $e) {
        printLine('[LỖI] ' . $e->getMessage());
    }
}

function printHeading(string $title): void
{
    printLine();
    printLine('===== ' . $title . ' =====');
}

beginOutput();

$cart = new ShoppingCart();

printLine('BÀI 1 - GIỎ HÀNG MUA SẮM');

printHeading('1. THÊM SẢN PHẨM VÀO GIỎ HÀNG');
addProduct($cart, 'Bàn phím cơ', 1250000, 2);
addProduct($cart, 'Chuột không dây', 350000, 3);
addProduct($cart, 'Tai nghe Bluetooth', 890000, 1);
addProduct($cart, 'Màn hình 24 inch', 3200000, 2);

printHeading('2. THÔNG TIN GIỎ HÀNG');
$cart->displayCart();

printHeading('3. TỔNG TIỀN GIỎ HÀNG');
printLine(sprintf('Giỏ hàng có %d sản phẩm, tổng tiền: %s', $cart->countItems(), formatMoney($cart->calculateTotal())));

printHeading('4. XÓA SẢN PHẨM THEO TÊN');
removeProduct($cart, 'Chuột không dây');

printHeading('5. GIỎ HÀNG SAU KHI XÓA');
$cart->displayCart();

printHeading('6. CÁC TRƯỜNG HỢP KHÔNG HỢP LỆ');
addProduct($cart, 'Sản phẩm giá 0', 0, 1);
addProduct($cart, 'Sản phẩm giá âm', -50000, 2);
addProduct($cart, 'Sản phẩm số lượng 0', 100000, 0);
addProduct($cart, 'Sản phẩm số lượng âm', 100000, -5);
addProduct($cart, '   ', 100000, 1);
addProduct($cart, '  bàn phím cơ  ', 1250000, 1);
removeProduct($cart, 'Laptop Dell');
removeProduct($cart, '   ');
$cart->displayCart();

printHeading('7. GIỎ HÀNG RỖNG');
$emptyCart = new ShoppingCart();
$emptyCart->displayCart();
printLine('Tổng tiền: ' . formatMoney($emptyCart->calculateTotal()));
removeProduct($emptyCart, 'Bàn phím cơ');
