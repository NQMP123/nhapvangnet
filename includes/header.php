<?php
// header.php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHAPVANG.VN - Giao dịch nhanh gọn, uy tín</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom-header.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-info bg-info custom-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/assets/images/logo.jpg" alt="Logo" style="height:50px; width:80px;">
            </a>
            <div class="mx-auto d-flex align-items-center custom-menu">
                <a class="nav-link font-weight-bold text-white <?php if (basename($_SERVER['PHP_SELF']) == 'index.php')
                    echo 'active-menu'; ?>" href="/">TRANG CHỦ</a>
                <a class="nav-link font-weight-bold text-white <?php if (basename($_SERVER['PHP_SELF']) == 'napvang.php')
                    echo 'active-menu'; ?>" href="/index.php">NẠP VÀNG</a>
                <a class="nav-link font-weight-bold <?php if (basename($_SERVER['PHP_SELF']) == 'ruttien.php')
                    echo 'active-menu-red'; ?>" href="/ruttien.php">RÚT TIỀN</a>
            </div>
            <div class="d-flex align-items-center">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <span class="text-white font-weight-bold mr-2">Xin chào,
                        <a href="/user.php" class="text-white font-weight-bold" style="text-decoration:underline;">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                    </span>
                    <a href="/logout.php" class="btn btn-danger ml-2">Đăng xuất</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-success font-weight-bold mr-2"><i class="fa fa-sign-in-alt"></i>
                        ĐĂNG NHẬP</a>
                    <a href="/register.php" class="btn btn-outline-light font-weight-bold"><i class="fa fa-user-plus"></i>
                        ĐĂNG KÍ</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>