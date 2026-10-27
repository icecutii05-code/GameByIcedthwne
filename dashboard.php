<?php
require_once __DIR__ . '/config.php'; require_login();
$u = current_user(); if (!$u) { session_destroy(); header('Location: login.php'); exit; }
if (empty($u['display_name']) || empty($u['age'])) { header('Location: setup_profile.php'); exit; }
$s = db()->prepare('SELECT game_slug, best_score, updated_at FROM game_scores WHERE user_id=? ORDER BY best_score DESC'); $s->execute([$u['id']]); $scores=$s->fetchAll();
$games = [];
$gameDir = __DIR__.'/games';
if (is_dir($gameDir)) foreach (glob($gameDir.'/*.html') as $path) $games[] = basename($path);
$avatar = $u['avatar'] ?: '';
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>GameHub • Trang chủ</title><link rel="stylesheet" href="assets/style.css"></head><body class="app-bg">
<header class="topbar"><a class="brand" href="dashboard.php">🎮 GameHub</a><div class="user-chip"><?php if($avatar): ?><img src="<?=e($avatar)?>" alt="Avatar"><?php else: ?><span class="avatar-placeholder">🙂</span><?php endif; ?><span><?=e($u['display_name'])?></span></div></header>
<main class="container"><section class="hero"><div><p class="eyebrow">KHO GAME CỦA BẠN</p><h1>Chơi vui hết nấc. 🕹️</h1><p>Chào <?=e($u['display_name'])?>! Chọn một trò chơi và phá kỷ lục của chính mình.</p><a href="#games" class="btn primary">Khám phá game ↓</a></div><div class="hero-art">🎲<span>✨</span>👾</div></section>
<section id="games" class="section-head"><div><p class="eyebrow">THƯ VIỆN</p><h2>Tất cả trò chơi</h2></div><span class="pill"><?=count($games)?> game HTML</span></section>
<?php if (!$games): ?><div class="empty"><div class="empty-icon">🧩</div><h3>Chưa có game nào</h3><p>Tạo thư mục <code>games</code> rồi chép các file game .html của bạn vào đó.</p></div><?php else: ?><div class="game-grid"><?php foreach($games as $game): $title=ucwords(str_replace(['_','-'], ' ', pathinfo($game,PATHINFO_FILENAME))); ?><article class="game-card"><div class="game-icon"><?=['🎯','🧠','🃏','🎲','🧩','🚀'][abs(crc32($game))%6]?></div><div class="game-info"><h3><?=e($title)?></h3><p>Game HTML • Sẵn sàng chơi</p><a class="btn primary small" href="<?=e('games/'.$game)?>">Chơi ngay ↗</a></div></article><?php endforeach; ?></div><?php endif; ?>
<section class="section-head"><div><p class="eyebrow">THÀNH TÍCH</p><h2>Điểm số đã lưu</h2></div></section>
<div class="score-panel"><?php if(!$scores): ?><p class="muted">Chưa có điểm số. Khi game của bạn gửi điểm đến hệ thống, thành tích sẽ xuất hiện ở đây.</p><?php else: ?><div class="score-list"><?php foreach($scores as $row): ?><div class="score-row"><span>🎮 <?=e($row['game_slug'])?></span><strong><?=number_format((int)$row['best_score'])?> điểm</strong></div><?php endforeach; ?></div><?php endif; ?></div>
<section class="section-head"><div><p class="eyebrow">TÀI KHOẢN</p><h2>Cài đặt tài khoản</h2></div></section>
<div class="settings-grid"><div class="setting-card"><div class="setting-icon">👤</div><div><strong><?=e($u['username'])?></strong><p><?=e((string)$u['age'])?> tuổi · Tên tài khoản không thể trùng</p><a href="setup_profile.php">Chỉnh sửa hồ sơ / ảnh đại diện</a></div></div>
<form method="post" action="logout.php" class="setting-card"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><div class="setting-icon">🔄</div><div><strong>Đổi tài khoản</strong><p>Đăng xuất để đăng nhập tài khoản khác.</p><button class="text-button">Đăng xuất & đổi tài khoản →</button></div></form>
<form method="post" action="delete_account.php" class="setting-card danger-card" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản và toàn bộ điểm số? Hành động này không thể hoàn tác.');"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><div class="setting-icon">🗑️</div><div><strong>Xóa tài khoản</strong><p>Xóa hồ sơ và thành tích đã lưu.</p><button class="text-button danger-text">Xóa tài khoản vĩnh viễn</button></div></form></div>
<footer>GameHub · Điểm số được lưu trong database MySQL của máy chủ.</footer></main></body></html>
