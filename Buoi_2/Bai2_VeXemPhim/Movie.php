<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

final class Movie
{
    private int $id;
    private string $title;
    private float $price;
    private int $totalSeats;
    private int $availableSeats;

    public function __construct(int $id, string $title, float $price, int $totalSeats)
    {
        $title = trim($title);

        if ($id <= 0) {
            throw new InvalidArgumentException('Mã phim phải là số nguyên lớn hơn 0.');
        }

        if ($title === '') {
            throw new InvalidArgumentException('Tên phim không được để trống.');
        }

        if (!is_finite($price) || $price <= 0) {
            throw new InvalidArgumentException(
                sprintf('Giá vé của phim "%s" phải là một số lớn hơn 0.', $title)
            );
        }

        if ($totalSeats <= 0) {
            throw new InvalidArgumentException(
                sprintf('Tổng số ghế của phim "%s" phải lớn hơn 0.', $title)
            );
        }

        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->totalSeats = $totalSeats;
        $this->availableSeats = $totalSeats;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTotalSeats(): int
    {
        return $this->totalSeats;
    }

    public function getAvailableSeats(): int
    {
        return $this->availableSeats;
    }

    /**
     * @throws InvalidArgumentException Khi số vé không hợp lệ.
     * @throws RuntimeException Khi số vé đặt vượt quá số ghế còn lại.
     */
    public function bookTicket(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Số vé cần đặt phải lớn hơn 0.');
        }

        if ($quantity > $this->availableSeats) {
            throw new RuntimeException(sprintf(
                'Phim "%s" chỉ còn %d ghế, không thể đặt %d vé.',
                $this->title,
                $this->availableSeats,
                $quantity
            ));
        }

        $this->availableSeats -= $quantity;
    }

    /**
     * @throws InvalidArgumentException Khi số vé không hợp lệ.
     * @throws RuntimeException Khi số vé hủy vượt quá số vé đã bán.
     */
    public function cancelTicket(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Số vé cần hủy phải lớn hơn 0.');
        }

        $soldSeats = $this->getSoldSeats();

        if ($quantity > $soldSeats) {
            throw new RuntimeException(sprintf(
                'Phim "%s" mới bán %d vé, không thể hủy %d vé.',
                $this->title,
                $soldSeats,
                $quantity
            ));
        }

        $this->availableSeats += $quantity;
    }

    public function getSoldSeats(): int
    {
        return $this->totalSeats - $this->availableSeats;
    }

    public function getRevenue(): float
    {
        return $this->getSoldSeats() * $this->price;
    }

    public function displayInfo(): void
    {
        printLine(
            padColumn((string) $this->id, 5)
            . padColumn($this->title, 16)
            . padColumn(formatMoney($this->price), 14, true)
            . padColumn((string) $this->totalSeats, 12, true)
            . padColumn((string) $this->availableSeats, 12, true)
            . padColumn((string) $this->getSoldSeats(), 12, true)
            . padColumn(formatMoney($this->getRevenue()), 18, true)
        );
    }
}
