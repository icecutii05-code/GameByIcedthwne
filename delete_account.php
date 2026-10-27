
<?php
require_once __DIR__ . '/config.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

verify_csrf();

$u = current_user();

// Xóa ảnh đại diện nếu ảnh nằm trong thư mục uploads/avatars/
if (
    $u &&
    !empty($u['avatar']) &&
    strpos($u['avatar'], 'uploads/avatars/') === 0
) {
    $path = __DIR__ . '/' . $u['avatar'];

    if (is_file($path)) {
        unlink($path);
    }
}

// Xóa tài khoản khỏi cơ sở dữ liệu
$q = db()->prepare('DELETE FROM users WHERE id = ?');
$q->execute([$_SESSION['user_id']]);

// Đăng xuất và hủy phiên đăng nhập
$_SESSION = [];
session_destroy();

// Chuyển về trang đăng ký
header('Location: register.php');
exit;