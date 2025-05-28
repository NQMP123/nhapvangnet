<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/db.php';
    session_start();
    if (isset($_POST['approve_id'])) {
        $id = intval($_POST['approve_id']);
        $stmt = $pdo->prepare("UPDATE ruttien SET status='success' WHERE id=? AND status='pending'");
        $stmt->execute([$id]);
        $_SESSION['adminMsg'] = '<div class="alert alert-success">Đã duyệt đơn!</div>';
        header('Location: withdraws.php');
        exit;
    }
    if (isset($_POST['cancel_id'])) {
        $id = intval($_POST['cancel_id']);
        // Lấy thông tin đơn rút tiền
        $stmt = $pdo->prepare("SELECT * FROM ruttien WHERE id=? AND status='pending'");
        $stmt->execute([$id]);
        $withdraw = $stmt->fetch();
        if ($withdraw) {
            // Hoàn lại tiền
            $user_id = $withdraw['user_id'];
            $amount = $withdraw['amount'];
            // Lấy số dư cũ
            $stmt = $pdo->prepare("SELECT balance FROM user WHERE id=?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            $old_balance = $user ? $user['balance'] : 0;
            $new_balance = $old_balance + $amount;
            // Cộng lại tiền
            $stmt = $pdo->prepare("UPDATE user SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);
            // Ghi lịch sử thay đổi số dư
            try {
                $stmt = $pdo->prepare("INSERT INTO balance_history (user_id, old_balance, new_balance, amount, type, description, created_at) VALUES (?, ?, ?, ?, 'refund', 'Hoàn tiền hủy rút', NOW())");
                $stmt->execute([$user_id, $old_balance, $new_balance, $amount]);
            } catch (Exception $e) {
            }
            // Đánh dấu đơn đã hủy
            $stmt = $pdo->prepare("UPDATE ruttien SET status='failed' WHERE id=? AND status='pending'");
            $stmt->execute([$id]);
            $_SESSION['adminMsg'] = '<div class="alert alert-warning">Đã hủy đơn và hoàn lại tiền cho user!</div>';
        } else {
            $_SESSION['adminMsg'] = '<div class="alert alert-danger">Không tìm thấy đơn hoặc đơn không ở trạng thái chờ!</div>';
        }
        header('Location: withdraws.php');
        exit;
    }
}