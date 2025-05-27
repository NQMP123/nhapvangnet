<?php
session_start();
require_once __DIR__ . '/db.php';
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'CSRF token không hợp lệ!';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (strlen($username) < 4)
            $errors[] = 'Tên đăng nhập phải từ 4 ký tự!';
        if (strlen($password) < 6)
            $errors[] = 'Mật khẩu phải từ 6 ký tự!';
        if ($password !== $confirm_password)
            $errors[] = 'Mật khẩu nhập lại không khớp!';
        // Kiểm tra trùng username
        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch())
            $errors[] = 'Tên đăng nhập đã tồn tại!';
        if (!$errors) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = 'Đăng ký thành công! Bạn có thể đăng nhập.';
        }
    }
}
?>