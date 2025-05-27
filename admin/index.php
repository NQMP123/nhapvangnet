<?php include '_auth.php'; ?>
<?php include '_admin_header.php'; ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card admin-card shadow-lg">
                <div class="card-header bg-info text-white text-center font-weight-bold" style="font-size:1.3em;">TRANG
                    QUẢN TRỊ ADMIN</div>
                <div class="card-body text-center">
                    <a href="/admin/servers.php" class="btn btn-lg btn-primary btn-admin m-3"><i
                            class="fa fa-server mr-2"></i>Quản lý
                        server nhập vàng</a>
                    <a href="/admin/withdraws.php" class="btn btn-lg btn-success btn-admin m-3"><i
                            class="fa fa-money-bill-wave mr-2"></i>Duyệt/Hủy đơn rút tiền</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '_admin_footer.php'; ?>