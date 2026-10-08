<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$pageTitle = 'Xóa sản phẩm';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id = parseId($isPost ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null));

if ($id === null) {
    setFlash('ID sản phẩm không hợp lệ.', 'error');
    redirect('product_list.php');
}

$product = getProductById($id);

if ($product === null) {
    setFlash(sprintf('Không tìm thấy sản phẩm có ID %d, có thể sản phẩm đã bị xóa.', $id), 'error');
    redirect('product_list.php');
}

if ($isPost) {
    $isDeleted = deleteProduct($id);

    setFlash($isDeleted
        ? sprintf('Đã xóa sản phẩm "%s" (ID %d).', (string) $product['name'], $id)
        : sprintf('Không xóa được sản phẩm ID %d, có thể sản phẩm đã bị xóa.', $id),
        $isDeleted ? 'success' : 'error');
    redirect('product_list.php');
}

require __DIR__ . '/view/header.php';
?>
    <h2>Xóa sản phẩm</h2>

    <p class="flash error">Bạn có chắc chắn muốn xóa sản phẩm dưới đây? Thao tác này không thể hoàn tác.</p>

    <table>
        <tr>
            <th>ID</th>
            <td><?= (int) $product['id'] ?></td>
        </tr>
        <tr>
            <th>Tên sản phẩm</th>
            <td><?= e($product['name']) ?></td>
        </tr>
        <tr>
            <th>Giá</th>
            <td><?= e(formatMoney($product['price'])) ?></td>
        </tr>
        <tr>
            <th>Số lượng</th>
            <td><?= (int) $product['quantity'] ?></td>
        </tr>
    </table>

    <form method="post" action="product_delete.php">
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <p>
            <button type="submit" class="danger">Xác nhận xóa</button>
            <a href="product_list.php">Hủy</a>
        </p>
    </form>
<?php require __DIR__ . '/view/footer.php'; ?>
