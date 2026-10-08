<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$pageTitle = 'Thêm sản phẩm';
$errors = [];
$form = ['name' => '', 'price' => '', 'quantity' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'name'     => postString('name'),
        'price'    => postString('price'),
        'quantity' => postString('quantity'),
    ];

    $product = validateProductInput($form['name'], $form['price'], $form['quantity']);
    $errors = $product['errors'];

    if ($errors === []) {
        $newId = addProduct($product['name'], $product['price'], $product['quantity']);

        setFlash(sprintf('Đã thêm sản phẩm "%s" với ID %d.', $product['name'], $newId));
        redirect('product_list.php');
    }
}

require __DIR__ . '/view/header.php';
?>
    <h2>Thêm sản phẩm</h2>

<?php if ($errors !== []): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <form method="post" action="product_add.php">
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
            <button type="submit">Thêm sản phẩm</button>
            <a href="product_list.php">Hủy</a>
        </p>
    </form>
<?php require __DIR__ . '/view/footer.php'; ?>
