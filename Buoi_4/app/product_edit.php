<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$pageTitle = 'Sửa sản phẩm';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id = parseId($isPost ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null));

if ($id === null) {
    setFlash('ID sản phẩm không hợp lệ.', 'error');
    redirect('product_list.php');
}

$current = getProductById($id);

if ($current === null) {
    setFlash(sprintf('Không tìm thấy sản phẩm có ID %d.', $id), 'error');
    redirect('product_list.php');
}

$errors = [];
$form = [
    'name'     => (string) $current['name'],
    'price'    => (string) $current['price'],
    'quantity' => (string) $current['quantity'],
];

if ($isPost) {
    $form = [
        'name'     => postString('name'),
        'price'    => postString('price'),
        'quantity' => postString('quantity'),
    ];

    $product = validateProductInput($form['name'], $form['price'], $form['quantity']);
    $errors = $product['errors'];

    if ($errors === []) {
        $isChanged = updateProduct($id, $product['name'], $product['price'], $product['quantity']);

        setFlash($isChanged
            ? sprintf('Đã cập nhật sản phẩm "%s" (ID %d).', $product['name'], $id)
            : sprintf('Sản phẩm ID %d không có thông tin nào thay đổi.', $id));
        redirect('product_list.php');
    }
}

require __DIR__ . '/view/header.php';
?>
    <h2>Sửa sản phẩm</h2>

<?php if ($errors !== []): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <td><?= (int) $current['id'] ?></td>
        </tr>
        <tr>
            <th>Tên sản phẩm hiện tại</th>
            <td><?= e($current['name']) ?></td>
        </tr>
        <tr>
            <th>Giá hiện tại</th>
            <td><?= e(formatMoney($current['price'])) ?></td>
        </tr>
        <tr>
            <th>Số lượng hiện tại</th>
            <td><?= (int) $current['quantity'] ?></td>
        </tr>
    </table>

    <form method="post" action="product_edit.php">
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <p>
            <label for="name">Tên sản phẩm</label>
            <input type="text" id="name" name="name" maxlength="100" value="<?= e($form['name']) ?>">
        </p>
        <p>
            <label for="price">Giá</label>
            <input type="text" id="price" name="price" value="<?= e($form['price']) ?>">
        </p>
        <p>
            <label for="quantity">Số lượng</label>
            <input type="text" id="quantity" name="quantity" value="<?= e($form['quantity']) ?>">
        </p>
        <p>
            <button type="submit">Cập nhật</button>
            <a href="product_list.php">Hủy</a>
        </p>
    </form>
<?php require __DIR__ . '/view/footer.php'; ?>
