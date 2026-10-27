<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Bạn cần đăng nhập']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Chỉ chấp nhận POST']); exit; }
$payload = json_decode(file_get_contents('php://input'), true);
$slug = preg_replace('/[^A-Za-z0-9_-]/', '', (string)($payload['game'] ?? ''));
$score = filter_var($payload['score'] ?? null, FILTER_VALIDATE_INT);
if ($slug === '' || strlen($slug)>100 || $score === false || $score < 0 || $score > 1000000000) {
  http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Game hoặc điểm số không hợp lệ']); exit;
}
$q = db()->prepare('INSERT INTO game_scores (user_id, game_slug, best_score) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE best_score = GREATEST(best_score, VALUES(best_score))');
$q->execute([$_SESSION['user_id'], $slug, $score]);
$q = db()->prepare('SELECT best_score FROM game_scores WHERE user_id=? AND game_slug=?');
$q->execute([$_SESSION['user_id'], $slug]);
echo json_encode(['ok'=>true,'best_score'=>(int)$q->fetchColumn()]);
