-- BÀI 1 - QUẢN LÝ GIỎ HÀNG

CREATE DATABASE IF NOT EXISTS shopping_cart
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE shopping_cart;

DROP TABLE IF EXISTS cart_items;

CREATE TABLE cart_items (
    id       INT            NOT NULL AUTO_INCREMENT,
    name     VARCHAR(100)   NOT NULL,
    price    DECIMAL(10, 2) NOT NULL,
    quantity INT            NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_cart_items_price CHECK (price > 0),
    CONSTRAINT chk_cart_items_quantity CHECK (quantity > 0)
);

-- 1. Thêm sản phẩm vào bảng
INSERT INTO cart_items (name, price, quantity) VALUES
    ('Bàn phím cơ',        1250000.00,  2),
    ('Chuột không dây',     350000.00,  7),
    ('Tai nghe Bluetooth',  890000.00,  1),
    ('Màn hình 24 inch',   3200000.00,  2),
    ('Lót chuột cỡ lớn',     95000.00, 10),
    ('Dây cáp USB-C',        75000.00,  6);

-- 2. Hiển thị toàn bộ sản phẩm
SELECT * FROM cart_items;

-- 3. Hiển thị sản phẩm có giá lớn hơn 100000
SELECT * FROM cart_items
WHERE price > 100000;

-- 4. Hiển thị sản phẩm có số lượng lớn hơn 5
SELECT * FROM cart_items
WHERE quantity > 5;

-- 5. Sắp xếp sản phẩm theo giá giảm dần
SELECT * FROM cart_items
ORDER BY price DESC;

-- 6. Cập nhật giá của một sản phẩm (Bàn phím cơ)
UPDATE cart_items
SET price = 1150000.00
WHERE id = 1;

SELECT * FROM cart_items WHERE id = 1;

-- 7. Cập nhật số lượng của một sản phẩm (Chuột không dây)
UPDATE cart_items
SET quantity = 5
WHERE id = 2;

SELECT * FROM cart_items WHERE id = 2;

-- 8. Xóa một sản phẩm (Dây cáp USB-C)
DELETE FROM cart_items
WHERE id = 6;

SELECT * FROM cart_items;

-- 9. Hiển thị tên sản phẩm, giá, số lượng và thành tiền
SELECT
    name,
    price,
    quantity,
    price * quantity AS total_price
FROM cart_items;

-- 10. Tính tổng tiền của toàn bộ giỏ hàng
SELECT COALESCE(SUM(price * quantity), 0) AS cart_total
FROM cart_items;
