-- Database cho bài thực hành buổi 8: API sản phẩm bằng PHP + Vue.js

CREATE DATABASE IF NOT EXISTS buoi8_api
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE buoi8_api;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id    INT          NOT NULL AUTO_INCREMENT,
    name  VARCHAR(100) NOT NULL,
    price INT          NOT NULL,
    stock INT          NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_products_price CHECK (price > 0),
    CONSTRAINT chk_products_stock CHECK (stock >= 0)
);

INSERT INTO products (name, price, stock) VALUES
    ('Bút bi',    5000, 20),
    ('Vở ô ly',  12000, 15),
    ('Thước kẻ',  8000,  0);

SELECT * FROM products;
