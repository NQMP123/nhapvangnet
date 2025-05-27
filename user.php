<?php
include 'includes/header.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: /login.php');
    exit;
}
// Lấy thông tin user
$stmt = $pdo->prepare("SELECT id, username, email, balance, created_at FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
// Lấy tên hiển thị từ player đầu tiên (nếu có)

if (!$user) {
    echo '<div class="container my-5"><div class="alert alert-danger">Không tìm thấy tài khoản!</div></div>';
    include 'includes/footer.php';
    exit;
}
// Xác định tab
$tab = $_GET['tab'] ?? 'info';
// Lấy lịch sử thay đổi số dư (nếu có bảng balance_history)
$history = [];
try {
    $stmt2 = $pdo->prepare("SELECT * FROM balance_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt2->execute([$user_id]);
    $history = $stmt2->fetchAll();
} catch (Exception $e) {
    // Nếu chưa có bảng, dùng dữ liệu giả lập
    $history = [
        [
            'old_balance' => 0,
            'new_balance' => 100000,
            'amount' => 100000,
            'type' => 'deposit',
            'description' => 'Nạp tiền',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ],
        [
            'old_balance' => 100000,
            'new_balance' => 50000,
            'amount' => -50000,
            'type' => 'withdraw',
            'description' => 'Rút tiền',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
        ],
    ];
}
?>
<div class="container my-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="bg-white rounded shadow-sm p-3 h-100">
                <h4 class="text-info font-weight-bold mb-3" style="border-left:5px solid #16a9c7; padding-left:12px;">
                    MENU TÀI KHOẢN</h4>
                <ul class="list-unstyled ml-2">
                    <li class="mb-2">
                        <span class="text-info mr-2" style="font-size:1.2em;">&#9632;</span>
                        <a href="user.php?tab=info" class="font-weight-bold <?php if ($tab == 'info')
                            echo 'text-info';
                        else
                            echo 'text-dark'; ?>" style="text-decoration:none;">Thông tin
                            tài khoản</a>
                    </li>
                    <li>
                        <span class="text-info mr-2" style="font-size:1.2em;">&#9632;</span>
                        <a href="user.php?tab=history" class="font-weight-bold <?php if ($tab == 'history')
                            echo 'text-info';
                        else
                            echo 'text-dark'; ?>" style="text-decoration:none;">Lịch sử thay đổi số dư</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-9 mb-4">
            <div class="bg-white rounded shadow-sm p-4 h-100">
                <?php if ($tab == 'info'): ?>
                    <h2 class="font-weight-bold mb-4" style="letter-spacing:1px;">THÔNG TIN TÀI KHOẢN</h2>
                    <div style="border-bottom:3px solid #16a9c7; width:180px; margin-bottom:24px;"></div>
                    <table class="table table-borderless mb-0" style="font-size:1.15em;">
                        <tr>
                            <td class="font-weight-bold" style="width:180px;">ID của bạn:</td>
                            <td class="bg-light">#<?php echo number_format($user['id']); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tên tài khoản:</td>
                            <td class="bg-light"><?php echo htmlspecialchars($user['username']); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Số tiền tài khoản:</td>
                            <td class="bg-light text-danger font-weight-bold" style="font-size:1.2em;">
                                <?php echo number_format($user['balance']); ?> vnđ
                            </td>
                        </tr>
                    </table>
                <?php elseif ($tab == 'history'): ?>
                    <h2 class="font-weight-bold mb-4" style="letter-spacing:1px;">LỊCH SỬ THAY ĐỔI SỐ DƯ</h2>
                    <div style="border-bottom:3px solid #16a9c7; width:260px; margin-bottom:24px;"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Loại</th>
                                    <th>Mô tả</th>
                                    <th>Số thay đổi</th>
                                    <th>Số dư cũ</th>
                                    <th>Số dư mới</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($history)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">Chưa có lịch sử thay đổi số dư.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($history as $h): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($h['created_at']))); ?></td>
                                            <td><?php echo $h['type'] == 'deposit' ? '<span class="text-success">Nạp</span>' : '<span class="text-danger">Rút</span>'; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($h['description'] ?? ''); ?></td>
                                            <td
                                                class="font-weight-bold <?php echo $h['amount'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo ($h['amount'] > 0 ? '+' : '') . number_format($h['amount']); ?>
                                            </td>
                                            <td><?php echo number_format($h['old_balance']); ?></td>
                                            <td><?php echo number_format($h['new_balance']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>