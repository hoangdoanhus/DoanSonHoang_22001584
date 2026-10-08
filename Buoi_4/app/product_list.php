<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$pageTitle = 'Danh sách sản phẩm';
$products = getAllProducts();
$flash = getFlash();

require __DIR__ . '/view/header.php';
?>
    <h2>Danh sách sản phẩm</h2>

<?php if ($flash !== null): ?>
    <p class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></p>
<?php endif; ?>

<?php if ($products === []): ?>
    <p>Chưa có sản phẩm nào trong database.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Chức năng</th>
        </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= (int) $product['id'] ?></td>
            <td><?= e($product['name']) ?></td>
            <td class="right"><?= e(formatMoney($product['price'])) ?></td>
            <td class="right"><?= (int) $product['quantity'] ?></td>
            <td>
                <a href="product_edit.php?id=<?= (int) $product['id'] ?>">Sửa</a>
                <a href="product_delete.php?id=<?= (int) $product['id'] ?>">Xóa</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>

    <p>
        <a href="product_add.php">Thêm sản phẩm</a>
    </p>
<?php require __DIR__ . '/view/footer.php'; ?>
