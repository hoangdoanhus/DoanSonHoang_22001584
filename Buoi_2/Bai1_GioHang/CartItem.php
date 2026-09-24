<?php

declare(strict_types=1);

final class CartItem
{
    private string $name;
    private float $price;
    private int $quantity;

    public function __construct(string $name, float $price, int $quantity)
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Tên sản phẩm không được để trống.');
        }

        if (!is_finite($price) || $price <= 0) {
            throw new InvalidArgumentException(
                sprintf('Đơn giá của sản phẩm "%s" phải là một số lớn hơn 0.', $name)
            );
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                sprintf('Số lượng của sản phẩm "%s" phải lớn hơn 0.', $name)
            );
        }

        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function increaseQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Số lượng cộng thêm phải lớn hơn 0.');
        }

        if ($quantity > PHP_INT_MAX - $this->quantity) {
            throw new RangeException('Số lượng sản phẩm vượt quá giới hạn cho phép.');
        }

        $this->quantity += $quantity;
    }

    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }
}
