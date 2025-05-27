<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: /login.php');
    exit;
}
$stmt = $pdo->prepare("SELECT isAdmin FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user || $user['isAdmin'] != 1) {
    echo "<div style='padding:2rem;color:red;font-weight:bold;'>Bạn không có quyền truy cập trang này!</div>";
    exit;
}