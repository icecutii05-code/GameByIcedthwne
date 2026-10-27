<?php
require_once __DIR__ . '/config.php'; require_login();
$u = current_user(); if (!$u) { session_destroy(); header('Location: login.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['display_name'] ?? ''));
    $age = filter_var($_POST['age'] ?? null, FILTER_VALIDATE_INT);
    if ($name === '' || mb_strlen($name) > 60) $error = 'Vui lòng nhập tên hiển thị (tối đa 60 ký tự).';
    elseif ($age === false || $age < 13 || $age > 120) $error = 'Tuổi phải từ 13 đến 120.';
    else {
        $avatar = $u['avatar'];
        if (!empty($_FILES['avatar']['name'])) {
            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK || $_FILES['avatar']['size'] > 3*1024*1024) $error = 'Ảnh tải lên tối đa 3 MB.';
            else {
                $info = new finfo(FILEINFO_MIME_TYPE); $mime = $info->file($_FILES['avatar']['tmp_name']);
                $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                if (!isset($allowed[$mime])) $error = 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.';
                else {
                    $dir = __DIR__.'/uploads/avatars'; if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $filename = bin2hex(random_bytes(16)).'.'.$allowed[$mime];
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.'/'.$filename)) $avatar = 'uploads/avatars/'.$filename;
                    else $error = 'Không thể lưu ảnh. Kiểm tra quyền thư mục uploads.';
                }
            }
        }
        if (!$error) {
            $q = db()->prepare('UPDATE users SET display_name=?, age=?, avatar=?, profile_completed=1 WHERE id=?');
            $q->execute([$name,$age,$avatar,$u['id']]); header('Location: dashboard.php'); exit;
        }
    }
}
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hoàn tất hồ sơ • GameHub</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg"><main class="auth-card wide"><a class="brand" href="dashboard.php">🎮 GameHub</a><p class="eyebrow">THÊM CHÚT THÔNG TIN</p><h1>Hồ sơ người chơi</h1><p class="muted">Hoàn tất hồ sơ để vào kho game.</p>
<?php if ($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Tên hiển thị</label><input name="display_name" maxlength="60" required value="<?=e((string)($u['display_name']??''))?>" placeholder="Tên bạn muốn mọi người thấy">
<label>Tuổi</label><input type="number" name="age" min="13" max="120" required value="<?=e((string)($u['age']??''))?>" placeholder="Ví dụ: 18">
<label>Ảnh đại diện (không bắt buộc)</label><input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
<button class="btn primary full" type="submit">Lưu hồ sơ & vào GameHub →</button></form><form method="post" action="logout.php" class="logout-form"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><button class="btn ghost full">Đăng xuất</button></form></main></body></html>
