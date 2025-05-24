<?php
include 'includes/header.php';
require_once 'includes/db.php';

// Lấy danh sách server
$servers = $pdo->query("SELECT id, name FROM server ORDER BY id")->fetchAll();

// Nếu đã đăng nhập, lấy số dư và nhân vật
$user_id = $_SESSION['user_id'] ?? null;
$gold_balance = 0;
$characters = [];
$transactions = [];
if ($user_id) {
    // Tổng số dư tất cả nhân vật
    $gold_balance = $pdo->query("SELECT SUM(gold_balance) FROM character WHERE user_id = " . intval($user_id))->fetchColumn();
    // Danh sách nhân vật
    $characters = $pdo->prepare("SELECT c.*, s.name as server_name FROM character c JOIN server s ON c.server_id = s.id WHERE c.user_id = ?");
    $characters->execute([$user_id]);
    $characters = $characters->fetchAll();
    // Lịch sử giao dịch nạp vàng
    $transactions = $pdo->prepare("SELECT n.*, c.name as char_name, s.name as server_name FROM napvang n JOIN character c ON n.character_id = c.id JOIN server s ON c.server_id = s.id WHERE n.user_id = ? ORDER BY n.created_at DESC LIMIT 10");
    $transactions->execute([$user_id]);
    $transactions = $transactions->fetchAll();
}
?>
<div class="container my-5">
    <h1 class="text-center font-weight-bold mb-4" style="font-size:2.8rem;">
        NẠP VÀNG
        <div style="width:120px;height:4px;background:#b71c1c;margin:0.5rem auto 0;"></div>
    </h1>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="p-4 mb-4 rounded" style="background:#d4f1f7;">
                <p><b>Hệ thống nhập vàng tự động</b></p>
                <p><b>Bước 1: Đặt đơn nạp vàng</b> trên website</p>
                <p><b>Bước 2:</b> Vào <b>đúng địa điểm</b> gặp nhân vật nhập vàng để giao dịch<br>
                    (Chú ý kiểm tra kỹ tên nhân vật ví có fake)</p>
                <p>Sau khi <b>giao dịch thành công</b> bạn sẽ được <b>cộng tiền</b> trên website sau <b>3 giây</b><br>
                    Bạn có thể rút ra Thẻ cào, ATM, Ví MoMo và các loại ví điện tử khác ở mục <a href="/ruttien.php"
                        class="font-weight-bold text-primary">RÚT TIỀN</a></p>
                <p class="mb-0" style="color:#d32f2f;font-weight:bold;">
                    Hệ thống tự động hủy đơn sau <span style="color:#e53935;">15 phút</span> nếu <span
                        style="color:#e53935;">chưa giao dịch thành công</span><br>
                    Vui lòng sử dụng phiên bản NRO gốc v2.2.2 trở lên để giao dịch<br>
                    Hãy tìm khu không có Virus/BOSS để tránh bị hủy GD giữa chừng.
                </p>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-5 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white font-weight-bold">
                    <i class="fa fa-coins"></i> Số dư: <?php echo number_format($gold_balance); ?>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="server">Chọn máy chủ</label>
                            <select class="form-control" id="server" name="server" style="font-size: 16px;">
                                <option value="">Chọn máy chủ</option>
                                <?php foreach ($servers as $sv): ?>
                                    <option value="<?php echo $sv['id']; ?>"><?php echo htmlspecialchars($sv['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gold">Số vàng cần nạp</label>
                            <select class="form-control" id="gold" name="gold">
                                <option>Vui lòng chọn</option>
                                <!-- Option số vàng -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="heso">Hệ số</label>
                            <input type="text" class="form-control" id="heso" name="heso" disabled>
                        </div>
                        <div class="form-group">
                            <label for="amount">Số tiền nhận</label>
                            <input type="text" class="form-control" id="amount" name="amount" disabled>
                        </div>
                        <button type="button" class="btn btn-danger btn-block font-weight-bold" disabled>
                            <i class="fa fa-sign-in-alt"></i>
                            <?php echo $user_id ? 'NẠP VÀNG' : 'ĐĂNG NHẬP ĐỂ THỰC HIỆN'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white font-weight-bold text-center">
                    Ví trí nhân vật nhận vàng
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Server</th>
                                <th>Nhân vật</th>
                                <th>Địa điểm</th>
                                <th>KV</th>
                                <th>Số vàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($user_id && $characters): ?>
                                <?php foreach ($characters as $char): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($char['server_name']); ?></td>
                                        <td><?php echo htmlspecialchars($char['name']); ?></td>
                                        <td>---</td>
                                        <td>---</td>
                                        <td><?php echo number_format($char['gold_balance']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-danger text-center">Vui lòng đăng nhập để hiển thị nhân vật
                                        giao dịch</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white font-weight-bold">
                    LỊCH SỬ GIAO DỊCH
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Server</th>
                                <th>Nhân vật</th>
                                <th>Số vàng</th>
                                <th>Tình trạng</th>
                                <th>Thời gian</th>
                                <th>Điều khiển</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($user_id && $transactions): ?>
                                <?php foreach ($transactions as $tr): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($tr['server_name']); ?></td>
                                        <td><?php echo htmlspecialchars($tr['char_name']); ?></td>
                                        <td><?php echo number_format($tr['amount']); ?></td>
                                        <td><?php echo htmlspecialchars($tr['status']); ?></td>
                                        <td><?php echo htmlspecialchars($tr['created_at']); ?></td>
                                        <td>-</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Bạn chưa thực hiện giao dịch nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'includes/footer.php';
?>