<?php
$successMsg = $errorMsg = '';
if (!isset($user_id)) {
    $user_id = $_SESSION['user_id'] ?? null;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errorMsg'] = 'CSRF token không hợp lệ!';
        header('Location: ruttien.php');
        exit;
    }
    if (!$user_id) {
        $errorMsg = 'Bạn cần đăng nhập để rút tiền!';
    } else {
        $type = $_POST['withdraw-type'] ?? '';
        $amount = 0;
        $desc = '';
        $character_id = null;
        if ($type === 'bank') {
            $bank = trim($_POST['loainganhang'] ?? '');
            $stk = trim($_POST['stk'] ?? '');
            $ten_tk = trim($_POST['ten_tk'] ?? '');
            $amount = intval($_POST['sotien'] ?? 0);
            $desc = 'Rút về ngân hàng: ' . $bank . ', STK: ' . $stk . ', Tên: ' . $ten_tk;
        } elseif ($type === 'wallet') {
            $wallet = trim($_POST['loaivi'] ?? '');
            $tenhienthi = trim($_POST['tenhienthi'] ?? '');
            $stkvi = trim($_POST['stkvi'] ?? '');
            $amount = intval($_POST['sotienvi'] ?? 0);
            $desc = 'Rút về ví: ' . $wallet . ', TK: ' . $stkvi . ', Tên: ' . $tenhienthi;
        }
        if ($amount < 50000) {
            $errorMsg = 'Số tiền rút tối thiểu là 50,000đ!';
        } elseif ($amount > 50000000) {
            $errorMsg = 'Số tiền rút tối đa là 50,000,000đ/lần!';
        } elseif (!$amount || !$desc) {
            $errorMsg = 'Vui lòng nhập đầy đủ thông tin!';
        } else {
            // Kiểm tra số dư user
            $stmt = $pdo->prepare("SELECT balance FROM user WHERE id=?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            if (!$user) {
                $errorMsg = 'Không tìm thấy tài khoản!';
            } elseif ($user['balance'] < $amount) {
                $errorMsg = 'Số dư không đủ để rút tiền!';
            } else {
                // Trừ số dư
                $stmt = $pdo->prepare("UPDATE user SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);
                // Lưu giao dịch rút tiền (character_id để 0)
                $stmt = $pdo->prepare("INSERT INTO ruttien (user_id, character_id, amount, status, created_at) VALUES (?, 0, ?, 'pending', NOW())");
                $stmt->execute([$user_id, $amount]);
                $successMsg = 'Tạo yêu cầu rút tiền thành công! Đơn của bạn sẽ được xử lý sớm.';
            }
        }
    }
    if ($successMsg)
        $_SESSION['successMsg'] = $successMsg;
    if ($errorMsg)
        $_SESSION['errorMsg'] = $errorMsg;
    header('Location: ruttien.php');
    exit;
}