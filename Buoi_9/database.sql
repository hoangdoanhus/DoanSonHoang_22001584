-- Database cho bài thực hành buổi 9: upload ảnh và phân trang sản phẩm

CREATE DATABASE IF NOT EXISTS buoi9_api
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE buoi9_api;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id    INT          NOT NULL AUTO_INCREMENT,
    name  VARCHAR(100) NOT NULL,
    price INT          NOT NULL,
    stock INT          NOT NULL,
    image VARCHAR(255)     NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_products_price CHECK (price > 0),
    CONSTRAINT chk_products_stock CHECK (stock >= 0)
);

INSERT INTO products (name, price, stock) VALUES
    ('Bút bi',            5000, 20),
    ('Vở ô ly',          12000, 15),
    ('Thước kẻ',          8000,  0),
    ('Bút chì 2B',        4000, 35),
    ('Tẩy chì',           3000, 40),
    ('Hộp bút',          45000, 12),
    ('Bìa hồ sơ',         6000, 60),
    ('Giấy A4 một ram',  65000,  8),
    ('Bút dạ quang',      9000, 25),
    ('Kéo thủ công',     18000, 10),
    ('Keo dán',           7000,  0),
    ('Compa',            22000,  6);

SELECT * FROM products ORDER BY id DESC;
