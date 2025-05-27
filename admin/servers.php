<?php include '_auth.php'; ?>
<?php include '_admin_header.php'; ?>
<div class="container my-5">
    <div class="card admin-card shadow mb-4">
        <div class="card-header bg-primary text-white font-weight-bold">Quản lý server nhập vàng</div>
        <div class="card-body">
            <form method="post" class="form-inline mb-3">
                <input type="text" name="name" class="form-control mr-2 mb-2" placeholder="Tên server" required>
                <input type="number" name="gold_price" class="form-control mr-2 mb-2" placeholder="Giá nhập vàng"
                    required>
                <button type="submit" class="btn btn-success btn-admin mb-2">Thêm server</button>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    echo '<div class="alert alert-danger">CSRF token không hợp lệ!</div>';
                    exit;
                }
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['gold_price'])) {
                $stmt = $pdo->prepare("INSERT INTO server (name, gold_price) VALUES (?, ?)");
                $stmt->execute([$_POST['name'], $_POST['gold_price']]);
                echo '<div class="alert alert-success">Đã thêm server!</div>';
            }
            if (isset($_POST['update_id'], $_POST['update_price'])) {
                $stmt = $pdo->prepare("UPDATE server SET gold_price = ? WHERE id = ?");
                $stmt->execute([$_POST['update_price'], $_POST['update_id']]);
                echo '<div class="alert alert-success">Đã cập nhật giá!</div>';
            }
            $servers = $pdo->query("SELECT * FROM server")->fetchAll();
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white admin-table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên server</th>
                            <th>Giá nhập vàng</th>
                            <th>Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servers as $sv): ?>
                            <tr>
                                <td><?php echo $sv['id']; ?></td>
                                <td><?php echo htmlspecialchars($sv['name']); ?></td>
                                <td>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="update_id" value="<?php echo $sv['id']; ?>">
                                        <input type="number" name="update_price" value="<?php echo $sv['gold_price']; ?>"
                                            class="form-control mr-2" style="width:120px;">
                                        <button type="submit" class="btn btn-primary btn-sm btn-admin">Lưu</button>
                                    </form>
                                </td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '_admin_footer.php'; ?>