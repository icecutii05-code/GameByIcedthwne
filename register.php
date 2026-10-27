<?php
require_once __DIR__ . '/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!preg_match('/^@[A-Za-z0-9_]{3,24}$/', $username)) {
        $error = 'Tên tài khoản phải bắt đầu bằng @, sau đó là 3–24 chữ cái, số hoặc dấu gạch dưới.';
    } elseif (strlen($password) < 8) {
        $error = 'Mật khẩu cần có ít nhất 8 ký tự.';
    } else {
        try {
            $q = db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $q->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            header('Location: setup_profile.php'); exit;
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'Tên tài khoản này đã có người sử dụng. Hãy chọn tên khác.' : 'Không thể tạo tài khoản. Kiểm tra cấu hình database.';
        }
    }
}
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đăng ký • GameHub</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg">
<main class="auth-card"><a class="brand" href="login.php">🎮 GameHub</a><p class="eyebrow">BẮT ĐẦU CUỘC CHƠI</p><h1>Tạo tài khoản</h1><p class="muted">Không cần Gmail hay tài khoản Google.</p>
<?php if ($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Tên tài khoản</label><input name="username" placeholder="@ten_cua_ban" maxlength="25" required pattern="@[A-Za-z0-9_]{3,24}" autocomplete="username"><small>Bắt đầu bằng @. Tên tài khoản không được trùng.</small>
<label>Mật khẩu</label><input type="password" name="password" placeholder="Ít nhất 8 ký tự" minlength="8" required autocomplete="new-password">
<button class="btn primary full" type="submit">Đăng ký miễn phí →</button></form><p class="bottom-note">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p></main></body></html>
