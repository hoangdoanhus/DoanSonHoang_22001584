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

-- Bảng tài khoản đăng nhập

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id       INT          NOT NULL AUTO_INCREMENT,
    username VARCHAR(50)  NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
);

-- Tài khoản mẫu dùng để thử chức năng đăng nhập:
--   username: admin
--   password: admin123
-- Giá trị bên dưới là kết quả của password_hash('admin123', PASSWORD_DEFAULT),
-- database không lưu mật khẩu dạng văn bản thuần.
INSERT INTO users (username, password) VALUES
    ('admin', '$2y$10$6nwlrYuAxcZgweJ8HeI5XuzwZTEo64t1QPkQ6lcHVa11cMQD8NphC');

SELECT id, username, LEFT(password, 7) AS password_prefix FROM users;
