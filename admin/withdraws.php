<?php include '_auth.php'; ?>
<?php include '_admin_header.php'; ?>
<div class="container my-5">
    <div class="card admin-card shadow mb-4">
        <div class="card-header bg-success text-white font-weight-bold">Duyệt/Hủy đơn rút tiền</div>
        <div class="card-body">
            <?php
            if (isset($_POST['approve_id'])) {
                $id = intval($_POST['approve_id']);
                $stmt = $pdo->prepare("UPDATE ruttien SET status='success' WHERE id=? AND status='pending'");
                $stmt->execute([$id]);
                echo '<div class="alert alert-success">Đã duyệt đơn!</div>';
            }
            if (isset($_POST['cancel_id'])) {
                $id = intval($_POST['cancel_id']);
                $stmt = $pdo->prepare("UPDATE ruttien SET status='failed' WHERE id=? AND status='pending'");
                $stmt->execute([$id]);
                echo '<div class="alert alert-warning">Đã hủy đơn!</div>';
            }
            $withdraws = $pdo->query("SELECT r.*, u.username FROM ruttien r JOIN user u ON r.user_id = u.id WHERE r.status='pending' ORDER BY r.created_at ASC")->fetchAll();
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white admin-table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Số tiền</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withdraws as $w): ?>
                            <tr>
                                <td><?php echo $w['id']; ?></td>
                                <td><?php echo htmlspecialchars($w['username']); ?></td>
                                <td><?php echo number_format($w['amount']); ?></td>
                                <td><?php echo $w['created_at']; ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="approve_id" value="<?php echo $w['id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm btn-admin">Duyệt</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="cancel_id" value="<?php echo $w['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm btn-admin">Hủy</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '_admin_footer.php'; ?>