<div class="card p-3 mb-3">
    <ul class="nav nav-pills border-0 pb-0" id="noti-tab" role="tablist">
        <li class="col-md-2 ">
            <a href="trang-quan-tri/tai-khoan/khach-hang" class="">
                <button type="button" class="btn btn-inverse-info btn-fw w100">Tổng số tài khoản <span class="count"><?= $analytics['tong_so_tk']; ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/tai-khoan-hoat-dong-trong-ngay" class="">
                <button type="button" class="btn btn-inverse-primary btn-fw w100">TK hoạt động trong ngày <span class="count"><?= count($analytics['tk_hoat_dong_ngay']['list']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/tai-khoan-moi-dang-ky" class="">
                <button type="button" class="btn btn-inverse-danger btn-fw w100">Tài khoản mới đăng ký <span class="count"><?= count($analytics['tk_moi_dang_ky']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/tai-khoan-sinh-nhat-trong-ngay" class="">
                <button type="button" class="btn btn-inverse-warning btn-fw w100">DS sinh nhật trong ngày <span class="count"><?= count($analytics['tk_sinh_nhat']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/bao-cao/kham-app-online" class="">
                <button type="button" class="btn btn-inverse-info btn-fw w100">BC Khám App Online Tháng<span class="count"><?= count($analytics['bc_kham_app_online']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/bao-cao/lich-su-thanh-toan" class="">
                <button type="button" class="btn btn-inverse-danger btn-fw w100">BC Lịch sử thanh toán <span class="count"><?= count($analytics['bc_lich_su_thanh_toan']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/giao-dich/vnpay" class="">
                <button type="button" class="btn btn-inverse-success btn-fw w100">Giao dịch VNPAY <span class="count"><?= count($analytics['vnpay']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 mb-1 ">
            <a href="trang-quan-tri/giao-dich/momo" class="">
                <button type="button" class="btn btn-inverse-warning btn-fw w100">Giao dịch MOMO <span class="count"><?= count($analytics['momo']); ?></span></button>
            </a>
        </li>
        <li class="col-md-2 ">
            <a href="trang-quan-tri/giao-dich/chuyen-khoan" class="">
                <button type="button" class="btn btn-inverse-info btn-fw w100">Chuyển khoản nạp tiền<span class="count"><?= count($analytics['ds_chuyen_khoan']); ?></span></button>
            </a>
        </li>
        <?php if($user['role'] == 1){ ?>
            <li class="col-md-2 ">
                <a href="trang-quan-tri/tai-khoan/tao-tai-khoan" class="">
                    <button type="button" class="btn btn-inverse-primary btn-fw w100">Tạo tài khoản mới</button>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="card p-3">
    <div class="row-fluid">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-tasks"></i></span>
                <h4 class="contentHeader">Số lượng tài khoản đăng ký</h4>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12" style="margin: 0;">
                        <canvas id="myChart" style="height:400px !important;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
   
        const labels = <?php $js_array_day = json_encode($register_day_chart); echo $js_array_day; ?>;

            const datapoints = <?php $js_array_value = json_encode($register_value_chart); echo $js_array_value; ?>;
            const data = {
            labels: labels,
            datasets: [
                {
                label: 'Tài khoản đăng ký mới',
                data: datapoints,
                fill: false,
                cubicInterpolationMode: 'monotone'
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                maintainAspectRatio: true,
            },
        };

        

        const ctx = document.getElementById('myChart');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: data,
        });

        
    });


   
</script>