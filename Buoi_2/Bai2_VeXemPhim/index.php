<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Movie.php';
require_once __DIR__ . '/movie_functions.php';

/**
 * @param Movie[] $movies
 */
function bookTicketById(array $movies, int $movieId, int $quantity): void
{
    $movie = findMovieById($movies, $movieId);

    if ($movie === null) {
        printLine(sprintf('[LỖI] Không tìm thấy phim có mã %d.', $movieId));

        return;
    }

    try {
        $movie->bookTicket($quantity);
        printLine(sprintf(
            '[OK] Đã đặt %d vé phim "%s", còn lại %d ghế.',
            $quantity,
            $movie->getTitle(),
            $movie->getAvailableSeats()
        ));
    } catch (InvalidArgumentException | RuntimeException $e) {
        printLine('[LỖI] ' . $e->getMessage());
    }
}

/**
 * @param Movie[] $movies
 */
function cancelTicketById(array $movies, int $movieId, int $quantity): void
{
    $movie = findMovieById($movies, $movieId);

    if ($movie === null) {
        printLine(sprintf('[LỖI] Không tìm thấy phim có mã %d.', $movieId));

        return;
    }

    try {
        $movie->cancelTicket($quantity);
        printLine(sprintf(
            '[OK] Đã hủy %d vé phim "%s", còn lại %d ghế.',
            $quantity,
            $movie->getTitle(),
            $movie->getAvailableSeats()
        ));
    } catch (InvalidArgumentException | RuntimeException $e) {
        printLine('[LỖI] ' . $e->getMessage());
    }
}

/**
 * @param Movie[] $movies
 */
function displayMovieList(array $movies): void
{
    ensureMovieList($movies);

    if ($movies === []) {
        printLine('Danh sách phim đang trống.');

        return;
    }

    printLine(
        padColumn('Mã', 5)
        . padColumn('Tên phim', 16)
        . padColumn('Giá vé', 14, true)
        . padColumn('Tổng ghế', 12, true)
        . padColumn('Còn lại', 12, true)
        . padColumn('Đã bán', 12, true)
        . padColumn('Doanh thu', 18, true)
    );
    printLine(str_repeat('-', 89));

    foreach ($movies as $movie) {
        $movie->displayInfo();
    }
}

function printHeading(string $title): void
{
    printLine();
    printLine('===== ' . $title . ' =====');
}

/**
 * @param Movie[] $movies
 */
function printBestSellingMovie(array $movies): void
{
    $bestSelling = getBestSellingMovie($movies);

    if ($bestSelling === null) {
        printLine('Chưa có phim nào bán được vé.');

        return;
    }

    printLine(sprintf(
        'Phim bán chạy nhất: "%s" - %d vé - doanh thu %s.',
        $bestSelling->getTitle(),
        $bestSelling->getSoldSeats(),
        formatMoney($bestSelling->getRevenue())
    ));
}

beginOutput();

printLine('BÀI 2 - QUẢN LÝ VÉ XEM PHIM');

printHeading('1. DANH SÁCH PHIM BAN ĐẦU');

$movies = [];

foreach ([[1, 'Avengers', 100000, 100], [2, 'Avatar', 120000, 80], [3, 'Batman', 90000, 120]] as [$id, $title, $price, $totalSeats]) {
    try {
        $movies[] = new Movie($id, $title, (float) $price, $totalSeats);
    } catch (InvalidArgumentException $e) {
        printLine('[LỖI] ' . $e->getMessage());
    }
}

displayMovieList($movies);

printHeading('2. ĐẶT VÉ');
bookTicketById($movies, 1, 30);
bookTicketById($movies, 2, 25);

printHeading('3. HỦY VÉ');
cancelTicketById($movies, 1, 10);

printHeading('4. THÔNG TIN TẤT CẢ CÁC PHIM');
displayMovieList($movies);

printHeading('5. TỔNG DOANH THU');
printLine('Tổng doanh thu của tất cả các phim: ' . formatMoney(getTotalRevenue($movies)));

printHeading('6. PHIM BÁN CHẠY NHẤT');
printBestSellingMovie($movies);

printHeading('7. CÁC TRƯỜNG HỢP KHÔNG HỢP LỆ');
bookTicketById($movies, 1, 0);
bookTicketById($movies, 1, -5);
bookTicketById($movies, 2, 1000);
cancelTicketById($movies, 3, 0);
cancelTicketById($movies, 3, 5);
bookTicketById($movies, 99, 2);

$movie = findMovieById($movies, 99);
printLine($movie === null ? '[OK] findMovieById(99) trả về null như mong đợi.' : '[LỖI] Kết quả tìm kiếm không đúng.');

try {
    $invalidList = $movies;
    $invalidList[] = 'Không phải object Movie';
    getTotalRevenue($invalidList);
} catch (InvalidArgumentException $e) {
    printLine('[LỖI] ' . $e->getMessage());
}

try {
    new Movie(4, 'Phim không có ghế', 80000, 0);
} catch (InvalidArgumentException $e) {
    printLine('[LỖI] ' . $e->getMessage());
}

printHeading('8. DANH SÁCH PHIM RỖNG');
$emptyMovies = [];
displayMovieList($emptyMovies);
printLine('Tổng doanh thu: ' . formatMoney(getTotalRevenue($emptyMovies)));
printLine(sprintf(
    'findMovieById(1) trên danh sách rỗng: %s',
    findMovieById($emptyMovies, 1) === null ? 'null' : 'có kết quả (sai)'
));
printBestSellingMovie($emptyMovies);

printHeading('9. DANH SÁCH PHIM CHƯA BÁN VÉ NÀO');
$newMovies = [new Movie(10, 'Inside Out 2', 95000, 50)];
printBestSellingMovie($newMovies);
printLine('Tổng doanh thu: ' . formatMoney(getTotalRevenue($newMovies)));
