<?php

declare(strict_types=1);

const DB_DSN  = 'mysql:host=127.0.0.1;port=3306;dbname=buoi8_api;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(int $status, string $message): void
{
    respond($status, ['success' => false, 'message' => $message]);
}

function connect(): PDO
{
    return new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

function toInteger($value): ?int
{
    // filter_var đổi true thành 1, nhưng true không phải số nên loại bỏ trước.
    if (is_bool($value)) {
        return null;
    }

    $number = filter_var($value, FILTER_VALIDATE_INT);

    return $number === false ? null : $number;
}

/**
 * Đọc JSON gửi lên và kiểm tra ba trường name, price, stock.
 *
 * @throws InvalidArgumentException khi dữ liệu không hợp lệ
 * @return array{name: string, price: int, stock: int}
 */
function readPayload(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw === false ? '' : $raw, true);

    if (!is_array($data)) {
        throw new InvalidArgumentException('Dữ liệu gửi lên phải là JSON gồm ba trường name, price và stock.');
    }

    $name = is_string($data['name'] ?? null) ? trim($data['name']) : '';

    // mb_strlen đếm theo ký tự nên tên có dấu không bị tính thành nhiều ký tự như strlen.
    if ($name === '' || mb_strlen($name) > 100) {
        throw new InvalidArgumentException('Tên sản phẩm phải có từ 1 đến 100 ký tự.');
    }

    $price = toInteger($data['price'] ?? null);

    if ($price === null || $price <= 0) {
        throw new InvalidArgumentException('Đơn giá phải là số nguyên lớn hơn 0.');
    }

    $stock = toInteger($data['stock'] ?? null);

    if ($stock === null || $stock < 0) {
        throw new InvalidArgumentException('Số lượng tồn phải là số nguyên không âm.');
    }

    return ['name' => $name, 'price' => $price, 'stock' => $stock];
}

/**
 * @throws InvalidArgumentException khi URL không có id hợp lệ
 */
function readId(): int
{
    $id = toInteger($_GET['id'] ?? null);

    if ($id === null || $id <= 0) {
        throw new InvalidArgumentException('URL phải có mã sản phẩm hợp lệ, ví dụ products.php?id=1.');
    }

    return $id;
}

function findProduct(PDO $db, int $id): ?array
{
    $statement = $db->prepare('SELECT id, name, price, stock FROM products WHERE id = ?');
    $statement->execute([$id]);
    $product = $statement->fetch();

    return $product === false ? null : $product;
}

try {
    $db = connect();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $rows = $db->query('SELECT id, name, price, stock FROM products ORDER BY id')->fetchAll();

        // Ép kiểu để JSON trả số, vì tùy cấu hình MySQL có thể trả cột số dưới dạng chuỗi.
        $products = array_map(static fn (array $row): array => [
            'id'    => (int) $row['id'],
            'name'  => (string) $row['name'],
            'price' => (int) $row['price'],
            'stock' => (int) $row['stock'],
        ], $rows);

        respond(200, ['success' => true, 'data' => $products]);
    }

    if ($method === 'POST') {
        $product = readPayload();

        $statement = $db->prepare('INSERT INTO products (name, price, stock) VALUES (?, ?, ?)');
        $statement->execute([$product['name'], $product['price'], $product['stock']]);

        respond(201, [
            'success' => true,
            'message' => 'Đã thêm sản phẩm "' . $product['name'] . '".',
            'data'    => ['id' => (int) $db->lastInsertId()] + $product,
        ]);
    }

    if ($method === 'PUT') {
        $id = readId();
        $product = readPayload();

        // Phải kiểm tra tồn tại trước, vì khi sửa mà dữ liệu không đổi thì rowCount() vẫn bằng 0.
        if (findProduct($db, $id) === null) {
            fail(404, 'Không tìm thấy sản phẩm có mã ' . $id . ' để sửa.');
        }

        $statement = $db->prepare('UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?');
        $statement->execute([$product['name'], $product['price'], $product['stock'], $id]);

        respond(200, [
            'success' => true,
            'message' => 'Đã cập nhật sản phẩm "' . $product['name'] . '".',
            'data'    => ['id' => $id] + $product,
        ]);
    }

    if ($method === 'DELETE') {
        $id = readId();

        $statement = $db->prepare('DELETE FROM products WHERE id = ?');
        $statement->execute([$id]);

        if ($statement->rowCount() === 0) {
            fail(404, 'Không tìm thấy sản phẩm có mã ' . $id . ' để xóa.');
        }

        respond(200, ['success' => true, 'message' => 'Đã xóa sản phẩm có mã ' . $id . '.']);
    }

    fail(405, 'API chỉ nhận các phương thức GET, POST, PUT và DELETE.');
} catch (InvalidArgumentException $e) {
    fail(400, $e->getMessage());
} catch (PDOException $e) {
    error_log('Loi API products: ' . $e->getMessage());
    fail(500, 'Không truy cập được database. Hãy kiểm tra MySQL đã chạy và cấu hình kết nối ở đầu file api/products.php.');
}
