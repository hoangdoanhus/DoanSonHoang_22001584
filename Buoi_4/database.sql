-- Database cho bài thực hành buổi 4

CREATE DATABASE IF NOT EXISTS shopping_cart
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE shopping_cart;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id       INT            NOT NULL AUTO_INCREMENT,
    name     VARCHAR(100)   NOT NULL,
    price    DECIMAL(10, 2) NOT NULL,
    quantity INT            NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_products_price CHECK (price > 0),
    CONSTRAINT chk_products_quantity CHECK (quantity >= 0)
);

INSERT INTO products (name, price, quantity) VALUES
    ('Bàn phím cơ',        1250000.00,  2),
    ('Chuột không dây',     350000.00,  7),
    ('Tai nghe Bluetooth',  890000.00,  1),
    ('Màn hình 24 inch',   3200000.00,  2),
    ('Lót chuột cỡ lớn',     95000.00, 10),
    ('Dây cáp USB-C',        75000.00,  0);

SELECT * FROM products;
