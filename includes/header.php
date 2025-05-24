<?php
// header.php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHAPVANG.NET - Giao dịch nhanh gọn, uy tín</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-info bg-info">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/assets/images/logo.png" alt="Logo" style="height:48px;">
                <span class="ml-2 font-weight-bold text-white">NHAPVANG.NET</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="/">TRANG CHỦ</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/napvang.php">NẠP VÀNG</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/ruttien.php">RÚT TIỀN</a></li>
                </ul>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <span class="text-white font-weight-bold ml-3">Xin chào,
                        <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="/logout.php" class="btn btn-danger ml-2">Đăng xuất</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-outline-light ml-2"><i class="fa fa-sign-in-alt"></i> ĐĂNG NHẬP</a>
                    <a href="/register.php" class="btn btn-light ml-2"><i class="fa fa-user-plus"></i> ĐĂNG KÍ</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>