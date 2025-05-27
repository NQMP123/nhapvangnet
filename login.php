<?php
include 'includes/login_handle.php';
include 'includes/header.php';
?>
<div class="container my-5" style="max-width: 400px;">
    <div class="card">
        <div class="card-header bg-info text-white text-center font-weight-bold">ĐĂNG NHẬP</div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
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
                <button type="submit" class="btn btn-info btn-block font-weight-bold">ĐĂNG NHẬP</button>
            </form>
            <div class="text-center mt-3">
                <a href="register.php">Chưa có tài khoản? Đăng ký</a>
            </div>
        </div>
    </div>
</div>
<?php
include 'includes/footer.php';
?>