<?php // footer.php ?>
<footer class="bg-info text-white text-center py-3 mt-5">
    <div class="container">
        &copy; 2024 NHAPVANG.VN - Giao dịch nhanh gọn, uy tín
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function () {
        function getGoldPrice() {
            var selected = $('#server option:selected');
            return parseInt(selected.data('gold-price')) || 0;
        }
        function getGoldAmount() {
            var type = $('#type').val();
            if (type === 'bar') {
                // Giao dịch thỏi vàng: lấy số thỏi vàng (không format)
                var raw = $('#custom-gold').val().replace(/[^\d]/g, '');
                return parseInt(raw) || 0;
            } else if (type === 'gold') {
                var val = $('#gold-amount').val();
                if (val === 'custom') {
                    var raw = $('#custom-gold').val().replace(/[^\d]/g, '');
                    return parseInt(raw) || 0;
                }
                return parseInt(val) || 0;
            }
            return 0;
        }
        function updateForm() {
            var type = $('#type').val();
            var goldPrice = getGoldPrice();
            var gold = getGoldAmount();
            var heso = goldPrice;
            var amount = 0;
            if (type === 'gold') {
                $('#gold-amount-group').removeClass('d-none');
                $('#custom-gold-group').toggleClass('d-none', $('#gold-amount').val() !== 'custom');
                $('#bar-note').addClass('d-none');
                if (gold && heso) amount = Math.round(gold / heso);
            } else if (type === 'bar') {
                $('#gold-amount-group').addClass('d-none');
                $('#custom-gold-group').removeClass('d-none');
                $('#bar-note').removeClass('d-none');
                // 1 thỏi vàng = 37tr vàng
                if (gold && heso) amount = Math.round((gold * 37000000) / heso);
            }
            $('#heso').val(heso ? heso : '');
            $('#amount').val(amount ? amount.toLocaleString('vi-VN') + 'đ' : '');
        }
        // Chỉ cho phép nhập số, không format khi đang nhập
        $('#custom-gold').on('input', function () {
            var type = $('#type').val();
            var val = $(this).val().replace(/[^\d]/g, '');
            if (type === 'gold') {
                // Giới hạn tối đa 9 chữ số (500 triệu)
                if (val.length > 9) val = val.slice(0, 9);
                if (val > 500000000) val = '500000000';
            } else if (type === 'bar') {
                // Giới hạn tối đa 99 thỏi vàng
                if (val > 99) val = '99';
            }
            $(this).val(val);
            updateForm();
        });
        // Khi blur thì không format lại, chỉ giữ số nguyên
        $('#custom-gold').on('blur', function () {
            var type = $('#type').val();
            var val = $(this).val().replace(/[^\d]/g, '');
            if (type === 'gold') {
                if (val > 500000000) val = '500000000';
                $(this).val(val);
            } else if (type === 'bar') {
                if (val > 99) val = '99';
                $(this).val(val);
            }
            updateForm();
        });
        // Đảm bảo luôn tính lại khi nhập số thỏi vàng
        $('#custom-gold').on('change keyup', function () {
            updateForm();
        });
        // Khi đổi hình thức nạp thì reset custom-gold và tính lại
        $('#type').on('change', function () {
            $('#custom-gold').val('');
            if ($(this).val() === 'bar') {
                $('#custom-gold').attr('placeholder', 'Nhập số thỏi vàng');
            } else {
                $('#custom-gold').attr('placeholder', 'Nhập số vàng');
            }
            updateForm();
        });
        $('#server, #type, #gold-amount').on('change keyup', updateForm);
        $('#gold-amount').on('change', function () {
            if ($(this).val() === 'custom') {
                $('#custom-gold-group').removeClass('d-none');
            } else {
                $('#custom-gold-group').addClass('d-none');
            }
            updateForm();
        });
        // Khởi tạo
        updateForm();
    });
</script>
</body>

</html>