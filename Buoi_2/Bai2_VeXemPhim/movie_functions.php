<?php

declare(strict_types=1);

require_once __DIR__ . '/Movie.php';

/**
 * @param Movie[] $movies
 * @return Movie|null null khi danh sách rỗng hoặc không có phim nào trùng mã.
 */
function findMovieById(array $movies, int $id): ?Movie
{
    ensureMovieList($movies);

    foreach ($movies as $movie) {
        if ($movie->getId() === $id) {
            return $movie;
        }
    }

    return null;
}

/**
 * @param Movie[] $movies
 */
function getTotalRevenue(array $movies): float
{
    ensureMovieList($movies);

    $total = 0.0;

    foreach ($movies as $movie) {
        $total += $movie->getRevenue();
    }

    return $total;
}

/**
 * @param Movie[] $movies
 * @return Movie|null null khi danh sách rỗng hoặc chưa phim nào bán được vé.
 */
function getBestSellingMovie(array $movies): ?Movie
{
    ensureMovieList($movies);

    $bestSelling = null;

    foreach ($movies as $movie) {
        if ($movie->getSoldSeats() <= 0) {
            continue;
        }

        if ($bestSelling === null || $movie->getSoldSeats() > $bestSelling->getSoldSeats()) {
            $bestSelling = $movie;
        }
    }

    return $bestSelling;
}

/**
 * @throws InvalidArgumentException
 */
function ensureMovieList(array $movies): void
{
    foreach ($movies as $movie) {
        if (!$movie instanceof Movie) {
            throw new InvalidArgumentException('Danh sách phim chỉ được chứa các object Movie.');
        }
    }
}
