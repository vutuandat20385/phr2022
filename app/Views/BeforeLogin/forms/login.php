<form id="loginform" class="form-vertical" action="<?= base_url('/xu-ly-dang-nhap')?>" method='post'>
    <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập" required>
    </div>
    <div class="form-group">
            <input id="password-field" type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
    </div>
    <div class="form-group">
            <button type="submit" class="form-control btn btn-primary submit px-3">Đăng nhập</button>
    </div>
    <div class="form-group d-md-flex">
            <div class="w-50">
                <label class="checkbox-wrap checkbox-primary">Ghi nhớ tài khoản
                        <input type="checkbox" checked>
                            <span class="checkmark"></span>
                </label>
            </div>
            <div class="w-50 text-md-right">
                <a href="#" style="color: #fff">Quên mật khẩu</a>
            </div>
    </div>
</form>