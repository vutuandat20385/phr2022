<div class="row w-100 mx-0">
    <div class="col-lg-4 mx-auto">
        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
            <div class="brand-logo" style="margin: 0 auto;">
                <img src="public/assets/afterlogin/images/logo.01.svg" alt="logo">
            </div>
       
            <form class="pt-3" action="trang-quan-tri/tai-khoan/tao-moi" method="post">
                <div class="form-group">
                <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Tên đăng nhập">
                </div>
                <div class="form-group">
                <input type="text" class="form-control form-control-lg" id="fullname" name="fullname" placeholder="Tên đầy đủ">
                </div>
                <div class="form-group">
                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <select class="form-control form-control-lg" id="role" name="role">
                        <option value="">---Vai trò---</option>
                        <?php foreach($roles as $r){ ?>
                            <option value="<?= $r['role_id']; ?>"><?= $r['role_name']; ?></option>
                        <?php } ?>
                       
                       
                    </select>
                </div>
                <div class="form-group">
                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Mật khẩu">
                </div>
                <div class="mb-4">
                
                </div>
                <div class="mt-3">
                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Tạo tài khoản</button>
                </div>
                
            </form>

            <div class="col-12">
                <span class="text-warning"><?= $msg;?></span>
            </div>
        </div>
    </div>
</div>