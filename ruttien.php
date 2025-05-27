<?php
include 'includes/header.php';
require_once 'includes/db.php';
include 'includes/ruttien_handle.php';

// Lấy số dư
$user_id = $_SESSION['user_id'] ?? null;
$gold_balance = 0;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT balance FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $gold_balance = $stmt->fetchColumn();
}
// Danh sách ngân hàng
$banks = [
    "DONGABANK -NH TMCP DONG A (EAB)",
    "MB - NH TMCP QUAN DOI",
    "AGRIBANK - NH NN - PTNT VIET NAM",
    "BIDV - NH DAU TU VA PHAT TRIEN VIET NAM",
    "SACOMBANK - NH TMCP SAI GON THUONG TIN",
    "SEABANK - NH TMCP DONG NAM A",
    "HDBANK - NH TMCP PHAT TRIEN TP.HCM (HDB)",
    "VIB - NH TMCP QUOC TE VIET NAM",
    "TPBANK - NH TMCP TIEN PHONG",
    "VPBANK - NH TMCP VIET NAM THINH VUONG",
    "MSB - Ngân Hàng TMCP Hàng Hải Việt Nam",
    "Lien Viet Post Bank",
    "ACB - NH TMCP A CHAU",
    "SCB - NH TMCP SAI GON",
    "OCB-Ngân hàng TMCP Phương Đông",
    "VIETCOMBANK -NH TMCP NGOAI THUONG VIET NAM",
    "VIETINBANK - NH TMCP CONG THUONG VIET NAM",
    "TECHCOMBANK - NH TMCP KY THUONG VIET NAM ",
    "SHB - NH TMCP SAI GON HA NOI",
    "EXIMBANK - NH TMCP XUAT NHAP KHAU VIET NAM",
    "OCEANBANK - NH TMCP DAI DUONG (OJB)",
];
// Danh sách ví điện tử
$wallets = [
    "Momo"
];
$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg = $_SESSION['errorMsg'] ?? '';
unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

// Lấy lịch sử giao dịch rút tiền
$withdraws = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM ruttien WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt->execute([$user_id]);
    $withdraws = $stmt->fetchAll();
}
?>
<div class="container my-5">
    <h1 class="main-title">RÚT TIỀN</h1>
    <div class="main-title-underline"></div>
    <div class="alert alert-info font-weight-bold" style="background:#d2eef5;">
        <b>Thông Báo:</b> Cập nhật hệ thống rút tiền <b>tự động</b> về Ngân hàng và Momo, rút tối thiểu 50k, max
        50tr/lần<br>
        Mọi yêu cầu hỗ trợ, Thắc mắc, Khiếu nại vui lòng liên hệ fanpage <a href="#"
            class="font-weight-bold text-primary">Tại Đây</a> (Tuyệt đối không inbox ở bất kể nơi nào khác)<br>
        <b>Bước 1:</b> Đặt đơn rút tiền trên website<br>
        <b>Bước 2:</b> Admin sẽ duyệt và tiền sẽ về túi của bạn nhanh chóng.
    </div>
    <div class="card border-info mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <span><i class="fa fa-coins"></i> Số dư: <?php echo number_format($gold_balance); ?></span>
        </div>
        <div class="card-body">
            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success text-center"><?php echo $successMsg; ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger text-center"><?php echo $errorMsg; ?></div>
            <?php endif; ?>
            <form id="withdrawForm" method="post">
                <input type="hidden" name="action" value="ruttien">
                <input type="hidden" name="csrf_token"
                    value="<?php echo $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>">
                <div class="form-group row align-items-center">
                    <label class="col-md-2 col-form-label font-weight-bold">Hình thức</label>
                    <div class="col-md-10">
                        <select class="form-control" id="withdraw-type" name="withdraw-type" required>
                            <option value="">Chọn hình thức</option>
                            <option value="bank">Ngân hàng</option>
                            <option value="wallet">Ví điện tử</option>
                        </select>
                    </div>
                </div>
                <div id="bank-fields" class="d-none">
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Ngân hàng:</label>
                        <div class="col-md-10">
                            <select class="form-control" id="loainganhang" name="loainganhang">
                                <option value="">Chọn ngân hàng</option>
                                <?php foreach ($banks as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Số tài khoản:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="stk" placeholder="Nhập số tài khoản">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Tên tài khoản:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="ten_tk" placeholder="Nhập tên tài khoản">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Số tiền:</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" name="sotien" placeholder="Nhập số tiền muốn rút">
                        </div>
                    </div>
                </div>
                <div id="wallet-fields" class="d-none">
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Loại ví:</label>
                        <div class="col-md-10">
                            <select class="form-control" name="loaivi">
                                <option value="">Chọn ví</option>
                                <?php foreach ($wallets as $w): ?>
                                    <option value="<?php echo htmlspecialchars($w); ?>"><?php echo htmlspecialchars($w); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Tên hiển thị:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="tenhienthi"
                                placeholder="Nhập tên hiển thị ví">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Tài khoản ví:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="stkvi" placeholder="Nhập số điện thoại ví">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-md-2 col-form-label font-weight-bold">Số tiền:</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" name="sotienvi"
                                placeholder="Nhập số tiền muốn rút">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-info btn-block font-weight-bold">RÚT TIỀN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="alert alert-info font-weight-bold" style="background:#d2eef5;">
        LỊCH SỬ GIAO DỊCH
    </div>
    <div class="card mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Mô tả</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdraws)): ?>
                            <?php foreach ($withdraws as $w): ?>
                                <tr>
                                    <td><?php echo number_format($w['amount']); ?>đ</td>
                                    <td>
                                        <?php if ($w['status'] == 'success'): ?>
                                            <span class="badge badge-success">Thành công</span>
                                        <?php elseif ($w['status'] == 'pending'): ?>
                                            <span class="badge badge-warning">Chờ duyệt</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Đã hủy</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($w['created_at']))); ?></td>
                                    <td><?php echo htmlspecialchars($w['description'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-danger">Chưa có giao dịch nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        $('#withdraw-type').on('change', function () {
            if ($(this).val() === 'bank') {
                $('#bank-fields').removeClass('d-none');
                $('#wallet-fields').addClass('d-none');
            } else if ($(this).val() === 'wallet') {
                $('#wallet-fields').removeClass('d-none');
                $('#bank-fields').addClass('d-none');
            } else {
                $('#bank-fields').addClass('d-none');
                $('#wallet-fields').addClass('d-none');
            }
        });
        // Gọi khi load trang để hiển thị đúng trường
        $('#withdraw-type').trigger('change');
    });
</script>
<?php
include 'includes/footer.php';