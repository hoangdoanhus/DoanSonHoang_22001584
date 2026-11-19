<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

const IMAGE_EXTENSIONS = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
];

function uploadErrorMessage(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_NO_FILE   => 'Chưa chọn ảnh để tải lên.',
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE => 'Ảnh vượt quá dung lượng cho phép (tối đa 2 MB).',
        UPLOAD_ERR_PARTIAL   => 'Ảnh mới tải lên được một phần, hãy thử lại.',
        default              => 'Không tải được ảnh lên (mã lỗi ' . $code . ').',
    };
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        fail(405, 'API tải ảnh chỉ nhận phương thức POST.');
    }

    // File lớn hơn post_max_size làm PHP bỏ trống $_POST và $_FILES, nếu không chặn sẽ báo nhầm là thiếu mã sản phẩm.
    if ($_POST === [] && $_FILES === [] && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        fail(400, 'Dữ liệu gửi lên vượt quá giới hạn post_max_size của PHP. Hãy chọn ảnh nhỏ hơn 2 MB.');
    }

    $db = connect();
    $id = toInteger($_POST['id'] ?? null);

    if ($id === null || $id <= 0) {
        throw new InvalidArgumentException('Thiếu mã sản phẩm hợp lệ trong dữ liệu gửi lên.');
    }

    $product = findProduct($db, $id);

    if ($product === null) {
        fail(404, 'Không tìm thấy sản phẩm có mã ' . $id . ' để gắn ảnh.');
    }

    $file = $_FILES['image'] ?? null;

    if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['size'])) {
        throw new InvalidArgumentException('Chưa chọn ảnh để tải lên.');
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException(uploadErrorMessage((int) $file['error']));
    }

    if ((int) $file['size'] > MAX_IMAGE_BYTES) {
        throw new InvalidArgumentException('Ảnh không được lớn hơn 2 MB.');
    }

    // getimagesize đọc nội dung file nên phát hiện được file chỉ đổi đuôi thành .jpg hoặc .png.
    $info = @getimagesize($file['tmp_name']);

    if ($info === false || !array_key_exists($info[2], IMAGE_EXTENSIONS)) {
        throw new InvalidArgumentException('Chỉ nhận ảnh JPG, JPEG hoặc PNG.');
    }

    if (!is_dir(UPLOADS_DIR) && !mkdir(UPLOADS_DIR, 0777, true) && !is_dir(UPLOADS_DIR)) {
        fail(500, 'Không tạo được thư mục uploads để lưu ảnh.');
    }

    // random_bytes để hai sản phẩm tải cùng tên file cũng không ghi đè lên nhau.
    $fileName = sprintf('sp%d_%s.%s', $id, bin2hex(random_bytes(8)), IMAGE_EXTENSIONS[$info[2]]);

    if (!move_uploaded_file($file['tmp_name'], UPLOADS_DIR . DIRECTORY_SEPARATOR . $fileName)) {
        fail(500, 'Không lưu được ảnh vào thư mục uploads.');
    }

    $statement = $db->prepare('UPDATE products SET image = ? WHERE id = ?');
    $statement->execute([UPLOADS_FOLDER . '/' . $fileName, $id]);

    // Chỉ xóa ảnh cũ sau khi database đã trỏ sang ảnh mới, tránh mất ảnh đang dùng khi bước trên lỗi.
    deleteImageFile($product['image']);

    respond(200, [
        'success' => true,
        'message' => 'Đã tải ảnh lên cho sản phẩm "' . $product['name'] . '".',
        'data'    => findProduct($db, $id),
    ]);
} catch (InvalidArgumentException $e) {
    fail(400, $e->getMessage());
} catch (PDOException $e) {
    error_log('Loi API upload: ' . $e->getMessage());
    fail(500, 'Không truy cập được database. Hãy kiểm tra MySQL đã chạy và cấu hình kết nối trong api/common.php.');
}
