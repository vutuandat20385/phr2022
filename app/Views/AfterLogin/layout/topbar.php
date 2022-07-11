
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <img class="logo" src="public/assets/afterlogin/images/logo.01.svg" alt="logo"/>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#phrMenu" aria-controls="phrMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="phrMenu">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url();?>">Bảng tổng hợp</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Tài khoản
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/tai-khoan/khach-hang">Khách hàng</a>
                <a class="dropdown-item" href="trang-quan-tri/tai-khoan/bac-si">Bác sĩ</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="trang-quan-tri/tai-khoan/quan-tri">Quản trị viên</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Bệnh án
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/benh-an/d4u-khach-le">Khách lẻ</a>
                <a class="dropdown-item" href="trang-quan-tri/benh-an/d4u-khach-doan">Khách đoàn</a>
                <a class="dropdown-item" href="trang-quan-tri/benh-an/d4u-test-covid">Test Covid</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                HIS-EHC
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/ehc/khach-le">Cập nhật khách lẻ</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Marketing
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/marketing/thong-bao">Thông báo Marketing APP</a>
                <a class="dropdown-item" href="trang-quan-tri/marketing/sinh-nhat">Sinh nhật khách hàng</a>
                <a class="dropdown-item" href="trang-quan-tri/marketing/tai-khoan-hoat-dong">Tài khoản hoạt động</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Tư vấn qua App
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/tu-van-qua-app/danh-sach-tu-van">Danh sách tư vấn</a>
                <a class="dropdown-item" href="trang-quan-tri/tu-van-qua-app/quan-ly-tu-van">Quản lý tư vấn</a>
                <a class="dropdown-item" href="trang-quan-tri/tu-van-qua-app/lich-tai-kham">Quản lý lịch tái khám</a>
                <a class="dropdown-item" href="trang-quan-tri/tu-van-qua-app/so-du-tai-khoan">Số dư tài khoản</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Lịch sử
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="trang-quan-tri/lich-su/import/benh-an/kham-le">Import bệnh án khách lẻ</a>
                <a class="dropdown-item" href="trang-quan-tri/lich-su/import/benh-an/kham-doan">Import bệnh án khách đoàn</a>
                <a class="dropdown-item" href="trang-quan-tri/lich-su/import/benh-an/test-covid">Import Test COVID</a>
                <?php if($user['role'] == 1 || $user['role'] == 2){ ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="trang-quan-tri/lich-su/khac">Khác</a>
                <?php } ?>
                
                </div>
            </li>
            <?php if($user['role'] == 1 || $user['role'] == 2){ ?>
            <li class="nav-item">
                <a class="nav-link" href="trang-quan-tri/cau-hinh">Cấu hình</a>
            </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="dang-xuat">Đăng xuất</a>
            </li>
        </ul>
       
    </div>
</nav>