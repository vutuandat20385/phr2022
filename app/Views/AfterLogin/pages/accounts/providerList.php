<div class="card p-3">
    <div class="row m-0">
        <div class="col-4 pt-2">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <div class="col-12 mt-1">
            <table class="table table-bordered table-hover d4u-table" id="tblAllUsers"> 
                <thead>
                <tr class="bg-primary">
                    <th class="text-white text-center" style="width: 50px !important;">#</th>
                    <th class="text-white text-center" style="width: 20%;">Số điện thoại</th>
                    <th class="text-white text-center" style="width: 30%;">Họ và tên</th>
                    <th class="text-white text-center" style="width: 25%;">Chuyên khoa</th>
                    <th class="text-white text-center" style="width: 10%;">Ngày sinh</th>
                    <th class="text-white text-center" style="width: 10%;">Giới tính</th>
                    <th class="text-white text-center" style="width: 5%;"></th>
                </tr>
                </thead>
                <tbody>
                    
                    <?php foreach($posts as $user){ ?>
                        <tr>
                            <td> <?= $user['index']; ?></td>
                            <td> <?= $user['value']; ?></td>
                            <td> <?= $user['given_name']; ?></td>
                            <td> </td>
                            <td> <?= date('d-m-Y', strtotime($user['birthdate'])); ?></td>
                            <td> <?php 
                                    if($user['gender'] === 'M' || $user['gender'] === 'MALE' || $user['gender'] === 'Nam'){
                                        echo 'Nam';
                                    }else if($user['gender'] === 'F' || $user['gender'] === 'FEMALE' || $user['gender'] === 'Nữ'){
                                        echo 'Nữ';
                                    }else{
                                        echo '';
                                    }
                            ; ?></td>
                            <td> 
                                <!-- <a class="text-info p-0" data-toggle="modal" data-target="#viewAccountModal_<?= $user['value']; ?>"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                <a class="text-danger p-0" href="#"> <i class="fa fa-trash-o" aria-hidden="true"></i> -->
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="viewAccountModal_<?= $user['value']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form method="post" action="#">    
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold" id="">CHỈNH SỬA THÔNG TIN TÀI KHOẢN</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <?= $user['modal']; ?>
                                    
                                    </div>
                                    
                                </div>
                            </form>        
                        </div>
                        
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <?php $pager = \Config\Services::pager(); ?>
            <div class="row">
                <div class="col-md-6">
                    <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
                </div>
                <div class="col-md-6 div-phantrang">
                    <?php if ($pager):?>
                        <?php $pagi_path = 'trang-quan-tri/tai-khoan/bac-si'; ?>
                        <?php $pager->setPath($pagi_path); ?>
                        <?= $pager->links(); ?>                  
                    <?php endif; ?>            
                </div>           
            </div>   
        </div>   
    </div>
</div>

<script>
    function saveUserInfo(sdt){
        var name    = $('#uGivenName'+sdt).val();
        var phone   = $('#uPhoneNumber'+sdt).val();
        var gender  = $('input[name=uGender'+sdt+']:checked').val();
        // var email   = $('#uEmail'+sdt).val();
        var birthdate    = $('#uBirthdate'+sdt).val();

        $.ajax({
            url: '<?= base_url();?>/update-account',
            type: 'post',
            data: {
                name: name,
                phone: phone,
                gender: gender,
                // email: email,
                birthdate: birthdate
            },
            success: function(result){
                location.reload();
            }
        });
    }
    
</script>