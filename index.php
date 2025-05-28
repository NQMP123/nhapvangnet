<?php
include 'includes/header.php';
require_once 'includes/db.php';
include 'includes/napvang_handle.php';

// Lấy danh sách server
$servers = $pdo->query("SELECT id, name, gold_price FROM server ORDER BY id")->fetchAll();

// Nếu đã đăng nhập, lấy số dư và nhân vật
$user_id = $_SESSION['user_id'] ?? null;
$gold_balance = 0;
$transactions = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT balance FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $gold_balance = $stmt->fetchColumn();
    // Danh sách nhân vật
    // Lịch sử giao dịch nạp vàng
    $transactions = $pdo->prepare("SELECT * FROM napvang WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $transactions->execute([$user_id]);
    $transactions = $transactions->fetchAll();
}

$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg = $_SESSION['errorMsg'] ?? '';
unset($_SESSION['successMsg'], $_SESSION['errorMsg']);
?>
<div class="container my-5">
    <h1 class="main-title">
        NẠP VÀNG
    </h1>
    <div class="main-title-underline"></div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="info-box">
                <p><b>Hệ thống nhập vàng tự động</b></p>
                <p><b>Bước 1: Đặt đơn nạp vàng</b> trên website</p>
                <p><b>Bước 2:</b> Vào <b>đúng địa điểm</b> gặp nhân vật nhập vàng để giao dịch<br>
                    (Chú ý kiểm tra kỹ tên nhân vật vì có fake)</p>
                <p>Sau khi <b>giao dịch thành công</b> bạn sẽ được <b>cộng tiền</b> trên website sau <b>3 giây</b><br>
                    Bạn có thể rút ra ATM, Ví MoMo và các loại ví điện tử khác ở mục <a href="/ruttien.php"
                        class="font-weight-bold text-primary">RÚT TIỀN</a></p>
                <p class="mb-0 text-danger">
                    Hệ thống tự động hủy đơn sau 15 phút nếu chưa giao dịch thành công<br>
                    Vui lòng sử dụng phiên bản NRO gốc v2.2.2 trở lên để giao dịch<br>
                    Hãy tìm khu không có Virus/BOSS để tránh bị hủy GD giữa chừng.
                </p>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-5 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fa fa-coins"></i> Số dư:
                    <?php echo number_format($gold_balance); ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success text-center">
                            <?php echo $successMsg; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>
                    <form id="goldForm" method="post">
                        <input type="hidden" name="action" value="napvang">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>">
                        <div class="form-group">
                            <label for="server">Chọn máy chủ</label>
                            <select class="form-control" id="server" name="server" required>
                                <option value="">Chọn máy chủ</option>
                                <?php foreach ($servers as $sv): ?>
                                    <option value="<?php echo $sv['id']; ?>"
                                        data-gold-price="<?php echo $sv['gold_price']; ?>">
                                        <?php echo htmlspecialchars($sv['name']) . ' sao x' . number_format($sv['gold_price']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Hình thức nạp</label>
                            <select class="form-control" id="type" name="type">
                                <option value="gold">Giao dịch vàng</option>
                                <option value="bar">Giao dịch thỏi vàng</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="character">Nhân vật</label>
                            <input type="text" class="form-control" id="character" name="character"
                                placeholder="Nhập tên nhân vật" required>
                        </div>
                        <div class="form-group" id="gold-amount-group">
                            <label for="gold-amount">Số vàng cần nạp</label>
                            <select class="form-control" id="gold-amount" name="gold-amount">
                                <option value="">Vui lòng chọn</option>
                                <option value="10000000">10 triệu vàng</option>
                                <option value="20000000">20 triệu vàng</option>
                                <option value="50000000">50 triệu vàng</option>
                                <option value="100000000">100 triệu vàng</option>
                                <option value="200000000">200 triệu vàng</option>
                                <option value="500000000">500 triệu vàng</option>
                                <option value="custom">Tự nhập số vàng</option>
                            </select>
                        </div>
                        <div class="form-group d-none" id="custom-gold-group">
                            <label for="custom-gold">Số vàng tự nhập</label>
                            <input type="number" class="form-control" id="custom-gold" name="custom-gold" min="1"
                                placeholder="Nhập số vàng">
                        </div>
                        <div class="form-group d-none" id="bar-note">
                            <small class="text-danger">thỏi vàng có giá trị 37tr vàng 1 thỏi</small>
                        </div>
                        <div class="form-group">
                            <label for="heso">Hệ số</label>
                            <input type="text" class="form-control font-weight-bold text-center" id="heso" name="heso"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="amount">Số tiền nhận</label>
                            <input type="text" class="form-control font-weight-bold text-center" id="amount"
                                name="amount" disabled>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block font-weight-bold" id="submitBtn" <?php echo !$user_id ? 'disabled' : ''; ?>>
                            <i class="fa fa-sign-in-alt"></i>
                            <?php echo $user_id ? 'NẠP VÀNG' : 'ĐĂNG NHẬP ĐỂ THỰC HIỆN'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white text-center">
                    Vị trí nhân vật nhận vàng
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="receiver-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Server</th>
                                    <th>Nhân vật</th>
                                    <th>Địa điểm</th>
                                    <th>KV</th>
                                    <th>Số vàng</th>
                                    <th>Số thỏi vàng</th>
                                </tr>
                            </thead>
                            <tbody id="receiver-tbody">
                                <tr>
                                    <td colspan="6" class="text-info text-center p-3">Vui lòng chọn máy chủ để xem danh
                                        sách nhân vật nhận vàng.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    LỊCH SỬ GIAO DỊCH
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <?php if ($user_id && !empty($transactions)): ?>
                            <?php foreach ($transactions as $tr): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="border rounded p-3 h-100 shadow-sm position-relative"
                                        style="background:#fafdff;">
                                        <div class="d-flex align-items-center mb-2">
                                            <?php if ($tr['status'] === 'success'): ?>
                                                <span class="badge badge-success mr-2" style="font-size:1.1em;"><i
                                                        class="fa fa-check-circle"></i></span>
                                            <?php elseif ($tr['status'] === 'pending'): ?>
                                                <span class="badge badge-warning mr-2" style="font-size:1.1em;"><i
                                                        class="fa fa-hourglass-half"></i></span>
                                            <?php else: ?>
                                                <span class="badge badge-danger mr-2" style="font-size:1.1em;"><i
                                                        class="fa fa-times-circle"></i></span>
                                            <?php endif; ?>
                                            <span class="font-weight-bold text-info">
                                                <?php echo htmlspecialchars("Server " . $tr['server_id'] . " sao"); ?>
                                            </span>
                                        </div>
                                        <div class="mb-1"><b>Nhân vật:</b>
                                            <?php echo htmlspecialchars($tr['character_name']); ?>
                                        </div>
                                        <div class="mb-1">
                                            <?php if (isset($tr['type']) && $tr['type'] == 1): ?>
                                                <b>Số thỏi vàng:</b> <span class="text-warning font-weight-bold">
                                                    <?php echo number_format($tr['amount']); ?>
                                                </span>
                                                <small class="text-muted">(1 thỏi = 37 triệu vàng)</small>
                                            <?php else: ?>
                                                <b>Số vàng:</b> <span class="text-warning font-weight-bold">
                                                    <?php echo number_format($tr['amount']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-1"><b>Thời gian:</b>
                                            <?php echo htmlspecialchars(date("d/m/Y H:i:s", strtotime($tr['created_at']))); ?>
                                        </div>
                                        <div class="mb-1"><b>Trạng thái:</b>
                                            <?php if ($tr['status'] === 'success'): ?>
                                                <span class="text-success font-weight-bold">Thành công</span>
                                            <?php elseif ($tr['status'] === 'pending'): ?>
                                                <span class="text-warning font-weight-bold">Chờ xử lý</span>
                                                <form method="post" class="d-inline-block ml-2"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?');">
                                                    <input type="hidden" name="action" value="cancel_napvang">
                                                    <input type="hidden" name="id" value="<?php echo $tr['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Hủy đơn</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-danger font-weight-bold">Đã hủy</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center mb-0">Bạn chưa thực hiện giao dịch nào.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        $('#server').on('change', function () {
            var serverId = $(this).val();
            if (!serverId) {
                $('#receiver-tbody').html('<tr><td colspan="5" class="text-info text-center p-3">Vui lòng chọn máy chủ để xem danh sách nhân vật nhận vàng.</td></tr>');
                return;
            }
            $.get('receiver_list.php?server_id=' + serverId, function (html) {
                $('#receiver-tbody').html(html);
            });
        });
    });
</script>
<?php
include 'includes/footer.php';
?>