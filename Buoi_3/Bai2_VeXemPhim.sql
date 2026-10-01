-- BÀI 2 - QUẢN LÝ VÉ XEM PHIM

CREATE DATABASE IF NOT EXISTS movie_tickets
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE movie_tickets;

DROP TABLE IF EXISTS movies;

CREATE TABLE movies (
    id              INT            NOT NULL AUTO_INCREMENT,
    title           VARCHAR(100)   NOT NULL,
    price           DECIMAL(10, 2) NOT NULL,
    total_seats     INT            NOT NULL,
    available_seats INT            NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_movies_price CHECK (price > 0),
    CONSTRAINT chk_movies_total_seats CHECK (total_seats > 0),
    CONSTRAINT chk_movies_available_seats CHECK (available_seats >= 0 AND available_seats <= total_seats)
);

-- 1. Thêm các phim
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
    ('Avengers',     100000.00, 100,  70),
    ('Avatar',       120000.00,  80,  55),
    ('Batman',        90000.00, 120, 120),
    ('Inside Out 2',  95000.00,  60,  12),
    ('Dune 2',       130000.00, 150,  40);

-- 2. Hiển thị toàn bộ danh sách phim
SELECT * FROM movies;

-- 3. Hiển thị phim có giá vé lớn hơn 100000
SELECT * FROM movies
WHERE price > 100000;

-- 4. Hiển thị phim còn nhiều hơn 50 ghế
SELECT * FROM movies
WHERE available_seats > 50;

-- 5. Sắp xếp phim theo giá vé giảm dần
SELECT * FROM movies
ORDER BY price DESC;

-- 6. Cập nhật số ghế còn lại của một phim (Avengers)
UPDATE movies
SET available_seats = 60
WHERE id = 1;

SELECT * FROM movies WHERE id = 1;

-- 7. Xóa một phim (Batman)
DELETE FROM movies
WHERE id = 3;

SELECT * FROM movies;

-- 8. Hiển thị số vé đã bán của từng phim
SELECT
    id,
    title,
    total_seats,
    available_seats,
    total_seats - available_seats AS sold_seats
FROM movies;

-- 9. Tính doanh thu của từng phim
SELECT
    id,
    title,
    total_seats - available_seats AS sold_seats,
    (total_seats - available_seats) * price AS revenue
FROM movies;

-- 10. Tính tổng doanh thu của tất cả các phim
SELECT COALESCE(SUM((total_seats - available_seats) * price), 0) AS total_revenue
FROM movies;

-- 11. Tìm phim có số vé bán ra nhiều nhất
SELECT
    id,
    title,
    total_seats - available_seats AS sold_seats,
    (total_seats - available_seats) * price AS revenue
FROM movies
WHERE total_seats - available_seats = (
    SELECT MAX(total_seats - available_seats) FROM movies
);
