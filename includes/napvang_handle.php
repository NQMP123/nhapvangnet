<?php
$successMsg = $errorMsg = '';
// Lấy user_id từ session nếu chưa có
if (!isset($user_id)) {
    $user_id = $_SESSION['user_id'] ?? null;
}
// Tự động hủy các đơn nạp vàng pending quá 30 phút
$pdo->query("UPDATE napvang SET status='failed' WHERE status='pending' AND created_at < (NOW() - INTERVAL 30 MINUTE)");

// Xử lý hủy đơn nạp vàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_napvang') {
    $id = intval($_POST['id'] ?? 0);
    if ($id && $user_id) {
        // Chỉ cho phép hủy đơn của chính mình và trạng thái pending
        $stmt = $pdo->prepare("UPDATE napvang SET status='failed' WHERE id=? AND user_id=? AND status='pending'");
        $stmt->execute([$id, $user_id]);
        if ($stmt->rowCount()) {
            $_SESSION['successMsg'] = 'Đã hủy đơn nạp vàng.';
        } else {
            $_SESSION['errorMsg'] = 'Không thể hủy đơn này.';
        }
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'napvang') {
    if (!$user_id) {
        $errorMsg = 'Bạn cần đăng nhập để nạp vàng!';
    } else {
        $server_id = intval($_POST['server'] ?? 0);
        $type = $_POST['type'] ?? 'gold';
        $character_name = trim($_POST['character'] ?? '');
        $amount = 0;
        $typeInt = 0;
        if ($type === 'gold') {
            $amount = intval($_POST['gold-amount'] ?? 0);
            if ($amount == 0 && isset($_POST['custom-gold'])) {
                $amount = intval($_POST['custom-gold']);
            }
            $typeInt = 0;
        } else if ($type === 'bar') {
            $amount = intval($_POST['custom-gold'] ?? 0); // Số thỏi vàng
            $typeInt = 1;
        }
        if (!$server_id || !$character_name || !$amount) {
            $errorMsg = 'Vui lòng nhập đầy đủ thông tin!';
        } else {
            // Kiểm tra đã có đơn pending tại server này chưa
            $stmt = $pdo->prepare("SELECT id FROM napvang WHERE user_id=? AND status='pending' AND character_id IN (SELECT id FROM player WHERE user_id=? AND server_id=?)");
            $stmt->execute([$user_id, $user_id, $server_id]);
            if ($stmt->fetch()) {
                $errorMsg = 'Bạn chỉ được tạo 1 đơn nạp vàng đang chờ tại mỗi server!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO napvang (user_id, character_id, amount, status, type) VALUES (?, ?, ?, 'pending', ?)");
                $stmt->execute([$user_id, $character_id, $amount, $typeInt]);
                $successMsg = 'Tạo đơn nạp vàng thành công!';
            }
        }
    }
    // Lưu thông báo vào session và redirect
    if ($successMsg)
        $_SESSION['successMsg'] = $successMsg;
    if ($errorMsg)
        $_SESSION['errorMsg'] = $errorMsg;
    header('Location: index.php');
    exit;
}