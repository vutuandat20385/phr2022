<div class="card p-3 mh-600">
    <div class="row m-0">
        <div class="col-3 pt-2">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
    <div class="col-12 mt-1">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center" style="width: 50px !important;">#</th>
                <th class="text-white text-center" style="width: 10%;">Tài khoản</th>
                <th class="text-white text-center" style="width: 30%;">Họ và tên</th>
                <th class="text-white text-center" style="">Email</th>
                <th class="text-white text-center" style="width: 25%;">Vai trò</th>
                <th class="text-white text-center" style="width: 5%;"></th>
            </tr>
            </thead>
            <tbody>
                
                <?php foreach($posts as $k => $u){ ?>
                    <tr>
                        <td> <?= $k+1; ?></td>
                        <td> <?= $u['username']; ?></td>
                        <td> <?= $u['fullname']; ?></td>
                        <td> <?= $u['email']; ?></td>
                        <td> <?= $u['role_name']; ?></td>
                        <td class="text-center"> 
                            <?php if($user['role'] == 1){ ?>
                                <a class="text-info p-0" data-toggle="modal" data-target="#viewAccountModal_<?= $u['id']; ?>"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                <a class="text-danger p-0" href="trang-quan-tri/tai-khoan/xoa-tai-khoan-quan-tri/<?= $u['id']; ?>"> <i class="fa fa-trash-o" aria-hidden="true"></i>
                            <?php } ?>
                            
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="viewAccountModal_<?= $u['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewAccountModal" aria-hidden="true">
                        <form method="post" action="trang-quan-tri/tai-khoan/sua-thong-tin-quan-tri">    
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-weight-bold" id="">CHỈNH SỬA THÔNG TIN TÀI KHOẢN</h5>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        
                                        <input type="text" class="form-control form-control-lg hidden" id="id" name="id" value="<?= $u['id']; ?>">
                                       
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-lg" id="fullname" name="fullname" placeholder="Tên đầy đủ" value="<?= $user['fullname']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" value="<?= $u['email']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control form-control-lg" id="role" name="role">
                                            <?php foreach($roles as $r){ ?>
                                                <?php if($r['role_id'] == $u['role']){
                                                    $selected = 'selected';
                                                }else{
                                                    $selected = '';
                                                } ?>
                                                <option value="<?= $r['role_id']; ?>" <?= $selected;?>><?= $r['role_name']; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Mật khẩu - không đổi thì để trống">
                                        </div>
                                    </div>
                                    <div class="model-footer text-center mb-3">
                                        <button type="submit" class="btn btn-success">Cập nhật thông tin</button>
                                    </div>
                                
                                </div>
                                
                            </div>
                        </form>        
                    </div>
                    
                <?php } ?>
            </tbody>
        </table>
  
    </div>   
</div>
