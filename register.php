<?php
include 'includes/register_handle.php';
include 'includes/header.php';
?>
<div class="container my-5" style="max-width: 400px;">
    <div class="card">
        <div class="card-header bg-info text-white text-center font-weight-bold">ĐĂNG KÝ</div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token"
                    value="<?php echo $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-info btn-block font-weight-bold">ĐĂNG KÝ</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Đã có tài khoản? Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
<?php
include 'includes/footer.php';
?>