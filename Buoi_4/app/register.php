<?php

declare(strict_types=1);

require_once __DIR__ . '/common/dbConnect.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/common/auth.php';

requireGuest();

$pageTitle = 'Đăng ký';
$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isCsrfValid()) {
        $errors[] = 'Phiên làm việc đã hết hạn, vui lòng gửi lại form.';
    } else {
        $username = postString('username');
        $password = postRaw('password');
        $passwordConfirm = postRaw('password_confirm');

        $errors = validateRegistration($username, $password, $passwordConfirm);

        if ($errors === [] && isUsernameTaken($username)) {
            $errors[] = sprintf('Tên đăng nhập "%s" đã tồn tại, vui lòng chọn tên khác.', $username);
        }

        if ($errors === []) {
            try {
                createUser($username, password_hash($password, PASSWORD_DEFAULT));

                setFlash(sprintf('Đã tạo tài khoản "%s", bạn có thể đăng nhập ngay.', $username));
                redirect('login.php');
            } catch (PDOException $e) {
                // 23000 là lỗi trùng khóa UNIQUE khi có hai lượt đăng ký cùng lúc.
                if ($e->getCode() === '23000') {
                    $errors[] = sprintf('Tên đăng nhập "%s" vừa được người khác sử dụng.', $username);
                } else {
                    error_log('Dang ky that bai: ' . $e->getMessage());
                    $errors[] = 'Không thể tạo tài khoản lúc này, vui lòng thử lại.';
                }
            }
        }
    }
}

require __DIR__ . '/view/header.php';
?>
    <h2>Đăng ký tài khoản</h2>

<?php if ($errors !== []): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <form method="post" action="register.php">
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
            <label for="password_confirm">Nhập lại mật khẩu</label>
            <input type="password" id="password_confirm" name="password_confirm">
        </p>
        <p>
            <button type="submit">Đăng ký</button>
            <a href="login.php">Đã có tài khoản? Đăng nhập</a>
        </p>
    </form>
<?php require __DIR__ . '/view/footer.php'; ?>
