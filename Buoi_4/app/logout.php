<?php

declare(strict_types=1);

require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/common/auth.php';

logoutUser();
setFlash('Bạn đã đăng xuất.');

redirect('login.php');
