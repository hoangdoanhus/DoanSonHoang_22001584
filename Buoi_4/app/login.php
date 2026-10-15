<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/common/auth.php';

requireGuest();

$pageTitle = 'Đăng nhập';
$errors = [];
$username = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isCsrfValid()) {
        $errors[] = 'Phiên làm việc đã hết hạn, vui lòng gửi lại form.';
    } else {
        $username = postString('username');
        $password = postRaw('password');

        if ($username === '' || $password === '') {
            $errors[] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
        } else {
            $user = findUserByUsername($username);

            if ($user !== null && password_verify($password, (string) $user['password'])) {
                loginUser($user);

                setFlash(sprintf('Xin chào %s, bạn đã đăng nhập thành công.', (string) $user['username']));
                redirect('product_list.php');
            }

            // Không nói rõ sai tên hay sai mật khẩu để tránh lộ tài khoản nào đang tồn tại.
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}

require __DIR__ . '/view/header.php';
?>
    <h2>Đăng nhập</h2>

<?php if ($flash !== null): ?>
    <p class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></p>
<?php endif; ?>

<?php if ($errors !== []): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <form method="post" action="login.php">
        <?= csrfField() ?>
        <p>
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" maxlength="50" value="<?= e($username) ?>">
        </p>
        <p>
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password">
        </p>
        <p>
            <button type="submit">Đăng nhập</button>
            <a href="register.php">Chưa có tài khoản? Đăng ký</a>
        </p>
    </form>
<?php require __DIR__ . '/view/footer.php'; ?>
