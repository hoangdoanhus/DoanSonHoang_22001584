<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/CartItem.php';

final class ShoppingCart
{
    /** @var CartItem[] */
    private array $items = [];

    /**
     * @return bool true nếu thêm mới, false nếu sản phẩm đã có và chỉ được cộng dồn số lượng.
     */
    public function addItem(CartItem $item): bool
    {
        $existing = $this->findItemByName($item->getName());

        if ($existing !== null) {
            $existing->increaseQuantity($item->getQuantity());

            return false;
        }

        $this->items[] = $item;

        return true;
    }

    /**
     * @return CartItem Sản phẩm vừa bị xóa.
     * @throws OutOfBoundsException Khi không có sản phẩm nào trùng tên.
     */
    public function removeItem(string $name): CartItem
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Tên sản phẩm cần xóa không được để trống.');
        }

        foreach ($this->items as $index => $item) {
            if ($this->isSameName($item->getName(), $name)) {
                unset($this->items[$index]);
                $this->items = array_values($this->items);

                return $item;
            }
        }

        throw new OutOfBoundsException(
            sprintf('Không tìm thấy sản phẩm "%s" trong giỏ hàng.', $name)
        );
    }

    public function calculateTotal(): float
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function displayCart(): void
    {
        if ($this->isEmpty()) {
            printLine('Giỏ hàng đang trống.');

            return;
        }

        printLine(
            padColumn('STT', 5)
            . padColumn('Tên sản phẩm', 24)
            . padColumn('Đơn giá', 16, true)
            . padColumn('Số lượng', 12, true)
            . padColumn('Thành tiền', 18, true)
        );
        printLine(str_repeat('-', 75));

        foreach ($this->items as $index => $item) {
            printLine(
                padColumn((string) ($index + 1), 5)
                . padColumn($item->getName(), 24)
                . padColumn(formatMoney($item->getPrice()), 16, true)
                . padColumn((string) $item->getQuantity(), 12, true)
                . padColumn(formatMoney($item->getTotal()), 18, true)
            );
        }

        printLine(str_repeat('-', 75));
        printLine(padColumn('TỔNG TIỀN:', 57, true) . padColumn(formatMoney($this->calculateTotal()), 18, true));
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function countItems(): int
    {
        return count($this->items);
    }

    private function findItemByName(string $name): ?CartItem
    {
        foreach ($this->items as $item) {
            if ($this->isSameName($item->getName(), $name)) {
                return $item;
            }
        }

        return null;
    }

    private function isSameName(string $first, string $second): bool
    {
        return mb_strtolower($first, 'UTF-8') === mb_strtolower(trim($second), 'UTF-8');
    }
}
