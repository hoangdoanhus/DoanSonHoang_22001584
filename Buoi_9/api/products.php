<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

const DEFAULT_PAGE  = 1;
const DEFAULT_LIMIT = 5;
const MAX_LIMIT     = 50;

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
 * @throws InvalidArgumentException khi page hoặc limit trên URL không hợp lệ
 * @return array{page: int, limit: int}
 */
function readPagination(): array
{
    $rawPage = $_GET['page'] ?? '';
    $rawLimit = $_GET['limit'] ?? '';

    $page = $rawPage === '' ? DEFAULT_PAGE : toInteger($rawPage);
    $limit = $rawLimit === '' ? DEFAULT_LIMIT : toInteger($rawLimit);

    if ($page === null || $page < 1) {
        throw new InvalidArgumentException('Tham số page phải là số nguyên dương.');
    }

    if ($limit === null || $limit < 1 || $limit > MAX_LIMIT) {
        throw new InvalidArgumentException('Tham số limit phải là số nguyên từ 1 đến ' . MAX_LIMIT . '.');
    }

    return ['page' => $page, 'limit' => $limit];
}

try {
    $db = connect();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        ['page' => $page, 'limit' => $limit] = readPagination();

        $total = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $limit));

        // Xin trang vượt quá trang cuối thì trả về trang cuối cùng và báo lại số trang thực tế.
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $statement = $db->prepare(
            'SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset'
        );

        // LIMIT và OFFSET phải bind kiểu số, vì khi tắt emulate prepares tham số chuỗi sẽ thành '5' và lỗi cú pháp.
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        respond(200, [
            'success'    => true,
            'data'       => array_map('mapProduct', $statement->fetchAll()),
            'pagination' => [
                'page'        => $page,
                'limit'       => $limit,
                'total'       => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    if ($method === 'POST') {
        $product = readPayload();

        $statement = $db->prepare('INSERT INTO products (name, price, stock) VALUES (?, ?, ?)');
        $statement->execute([$product['name'], $product['price'], $product['stock']]);

        respond(201, [
            'success' => true,
            'message' => 'Đã thêm sản phẩm "' . $product['name'] . '".',
            'data'    => findProduct($db, (int) $db->lastInsertId()),
        ]);
    }

    if ($method === 'PUT') {
        $id = readId();
        $product = readPayload();

        // Phải kiểm tra tồn tại trước, vì khi sửa mà dữ liệu không đổi thì rowCount() vẫn bằng 0.
        if (findProduct($db, $id) === null) {
            fail(404, 'Không tìm thấy sản phẩm có mã ' . $id . ' để sửa.');
        }

        // Cột image không nằm trong câu lệnh nên sửa thông tin không làm mất ảnh hiện tại.
        $statement = $db->prepare('UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?');
        $statement->execute([$product['name'], $product['price'], $product['stock'], $id]);

        respond(200, [
            'success' => true,
            'message' => 'Đã cập nhật sản phẩm "' . $product['name'] . '".',
            'data'    => findProduct($db, $id),
        ]);
    }

    if ($method === 'DELETE') {
        $id = readId();
        $product = findProduct($db, $id);

        if ($product === null) {
            fail(404, 'Không tìm thấy sản phẩm có mã ' . $id . ' để xóa.');
        }

        $statement = $db->prepare('DELETE FROM products WHERE id = ?');
        $statement->execute([$id]);

        deleteImageFile($product['image']);

        respond(200, ['success' => true, 'message' => 'Đã xóa sản phẩm có mã ' . $id . '.']);
    }

    fail(405, 'API chỉ nhận các phương thức GET, POST, PUT và DELETE.');
} catch (InvalidArgumentException $e) {
    fail(400, $e->getMessage());
} catch (PDOException $e) {
    error_log('Loi API products: ' . $e->getMessage());
    fail(500, 'Không truy cập được database. Hãy kiểm tra MySQL đã chạy và cấu hình kết nối trong api/common.php.');
}
