<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<pre style="font-family: Consolas, monospace; font-size: 14px;">';
    register_shutdown_function(static function (): void {
        echo '</pre>';
    });
}

$name = 'Doãn Sơn Hoàng';
$score = 7.5;

if (!is_numeric($score)) {
    $rank = 'Không xếp loại được (điểm không phải là số)';
} elseif ($score < 0 || $score > 10) {
    $rank = 'Không xếp loại được (điểm phải trong khoảng 0 - 10)';
} elseif ($score >= 8) {
    $rank = 'Giỏi';
} elseif ($score >= 6.5) {
    $rank = 'Khá';
} elseif ($score >= 5) {
    $rank = 'Trung bình';
} else {
    $rank = 'Yếu';
}

echo 'KẾT QUẢ XẾP LOẠI HỌC SINH', PHP_EOL;
echo str_repeat('-', 32), PHP_EOL;
echo 'Tên học sinh : ', $name, PHP_EOL;
echo 'Điểm         : ', $score, PHP_EOL;
echo 'Xếp loại     : ', $rank, PHP_EOL;
