<div class="card" style="padding: 0 20px; min-height: 625px;">
    <div class="col-12 mt-3">
        <h4 class="contentHeader"><?= $panelTitle; ?></h4>
    </div>
    <hr class="mt-0">
    <div class="row">
        <div class="col-1">
            <a href="trang-quan-tri/cau-hinh/thong-bao" class="text-decoration-none">
                <div class="setting-cover">
                    <img src="public/assets/afterlogin/images/noti-icon.jpg" class="setting-item">
                </div>
                <h4 class="setting-item-name">Thông báo</h4>
            </a>
        </div>
        <div class="col-1">
            <a href="trang-quan-tri/cau-hinh/dich-vu" class="text-decoration-none">
                <div class="setting-cover">
                    <img src="public/assets/afterlogin/images/service.png" class="setting-item">
                </div>
                <h4 class="setting-item-name">Dịch vụ</h4>
            </a>
        </div>
        <div class="col-1">
            <a href="trang-quan-tri/cau-hinh/chi-so-chuan" class="text-decoration-none">
                <div class="setting-cover">
                    <img src="public/assets/afterlogin/images/index.jpg" class="setting-item">
                </div>
                <h4 class="setting-item-name">Chỉ số chuẩn</h4>
            </a>
        </div>
        <div class="col-1">
            <a href="trang-quan-tri/cau-hinh/quan-ly-bac-si" class="text-decoration-none">
                <div class="setting-cover">
                    <img src="public/assets/afterlogin/images/doctor.png" class="setting-item">
                </div>
                <h4 class="setting-item-name">Quản lý bác sĩ</h4>
            </a>
        </div>
        <div class="col-1">
            <a href="trang-quan-tri/cau-hinh/template" class="text-decoration-none">
                <div class="setting-cover">
                    <img src="public/assets/afterlogin/images/template.png" class="setting-item">
                </div>
                <h4 class="setting-item-name">Template</h4>
            </a>
        </div>
    </div>
    
    <div class="col-12 mt-3">
        <h4 class="contentHeader">CRONJOB</h4>
    </div>
    <hr class="mt-0">
    <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
        <thead>
        <tr class="bg-primary">
            <th class="text-white text-center" style="width: 5%">#</th>
            <th class="text-white text-center" style="width: 30%;">Command</th>
            <th class="text-white text-center" style="width: 65%;">Mô tả</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Marketing commandMarketingNotification</td>
                <td>Tự động gửi thông báo marketing tới tất cả các tài khoản hàng ngày</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Remind remindAppointment</td>
                <td>Nhắc nhở khi có đăng ký cuộc khám tư vấn qua App</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Remind medicineRemind</td>
                <td>Nhắc nhở cập nhật thuốc sau khi bác sĩ kết thúc khám</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Ehc updateInfo</td>
                <td>Tự động trả kết quả từ HIS lên App cho khách hàng</td>
            </tr>
        </tbody>
    </table>
</div>