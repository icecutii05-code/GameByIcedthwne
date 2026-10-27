<?php
declare(strict_types=1);
session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'gamehub';
const DB_USER = 'root';
const DB_PASS = ''; // XAMPP mặc định thường để trống; đổi nếu MySQL của bạn có mật khẩu.

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES => false]
        );
    }
    return $pdo;
}
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php'); exit;
    }
}
function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $q = db()->prepare('SELECT id, username, display_name, age, avatar FROM users WHERE id = ?');
    $q->execute([$_SESSION['user_id']]);
    return $q->fetch() ?: null;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', (string)$_POST['csrf'])) {
        http_response_code(403); exit('Yêu cầu không hợp lệ. Hãy tải lại trang và thử lại.');
    }
}
