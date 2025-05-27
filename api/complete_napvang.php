<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID đơn nạp vàng!']);
    exit;
}
// Lấy đơn nạp vàng
$stmt = $pdo->prepare("SELECT * FROM napvang WHERE id = ? AND status = 'pending'");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Đơn nạp vàng không tồn tại hoặc đã xử lý!']);
    exit;
}
$user_id = $order['user_id'];
$amount = $order['amount'];
// Lấy số dư cũ
$stmt = $pdo->prepare("SELECT balance FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy user!']);
    exit;
}
$old_balance = $user['balance'];
$new_balance = $old_balance + $amount;
// Cộng tiền cho user
$stmt = $pdo->prepare("UPDATE user SET balance = balance + ? WHERE id = ?");
$stmt->execute([$amount, $user_id]);
// Cập nhật trạng thái đơn
$stmt = $pdo->prepare("UPDATE napvang SET status = 'success' WHERE id = ?");
$stmt->execute([$id]);
// Ghi lịch sử thay đổi số dư
try {
    $stmt = $pdo->prepare("INSERT INTO balance_history (user_id, old_balance, new_balance, amount, type, description, created_at) VALUES (?, ?, ?, ?, 'deposit', 'Nạp vàng', NOW())");
    $stmt->execute([$user_id, $old_balance, $new_balance, $amount]);
} catch (Exception $e) {
}
echo json_encode(['success' => true, 'message' => 'Đã cộng tiền cho user!', 'old_balance' => $old_balance, 'new_balance' => $new_balance]);