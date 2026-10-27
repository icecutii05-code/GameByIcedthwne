<?php
require_once __DIR__ . '/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $q = db()->prepare('SELECT id, password_hash, profile_completed FROM users WHERE username = ?');
    $q->execute([$username]); $user = $q->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true); $_SESSION['user_id'] = (int)$user['id'];
        header('Location: ' . ($user['profile_completed'] ? 'dashboard.php' : 'setup_profile.php')); exit;
    }
    $error = 'Tên tài khoản hoặc mật khẩu không chính xác.';
}
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đăng nhập • GameHub</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg">
<main class="auth-card"><a class="brand" href="login.php">🎮 GameHub</a><p class="eyebrow">KHO GAME CỦA BẠN</p><h1>Chào mừng trở lại!</h1><p class="muted">Đăng nhập để tiếp tục chơi và lưu thành tích.</p>
<?php if ($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Tên tài khoản</label><input name="username" placeholder="@ten_cua_ban" required autocomplete="username">
<label>Mật khẩu</label><input type="password" name="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
<button class="btn primary full" type="submit">Đăng nhập →</button></form><p class="bottom-note">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p></main></body></html>
