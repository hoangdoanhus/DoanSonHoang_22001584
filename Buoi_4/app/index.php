<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$pageTitle = 'Trang chủ';
$products = getAllProducts();

$totalQuantity = 0;
$totalValue = 0.0;

foreach ($products as $product) {
    $totalQuantity += (int) $product['quantity'];
    $totalValue += (float) $product['price'] * (int) $product['quantity'];
}

require __DIR__ . '/view/header.php';
?>
    <h2>Trang chủ</h2>

    <p>Hệ thống quản lý sản phẩm của giỏ hàng.</p>

    <table>
        <tr>
            <th>Số sản phẩm đang có</th>
            <td class="right"><?= count($products) ?></td>
        </tr>
        <tr>
            <th>Tổng số lượng trong kho</th>
            <td class="right"><?= $totalQuantity ?></td>
        </tr>
        <tr>
            <th>Tổng giá trị kho</th>
            <td class="right"><?= e(formatMoney($totalValue)) ?></td>
        </tr>
    </table>

    <p>
        <a href="product_list.php">Xem danh sách sản phẩm</a>
    </p>
<?php require __DIR__ . '/view/footer.php'; ?>
