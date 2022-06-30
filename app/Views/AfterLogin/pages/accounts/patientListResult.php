<div class="row p-1" style="margin-left: 0;">
    <div class="col-12">
        <h4 ><?= $panelTitle; ?></h4>
    </div>
    <div class="col-md-4"  style="margin-top: 3px;">
        <input type="text" class="form-control miniTextBox" value="" placeholder="Tìm kiếm Tên/SĐT" id="inpInfo">
    </div>
    <div class="col-md-2 p-1">
        <select class="form-control" name="" id="sltCity">
            <option class="p-2" value="">Chọn Tỉnh/Thành phố</option>
             <?php foreach($city_village_list as $k => $c){ ?>
                <?php
                    if($c['city_village'] == $city){
                        $selected = 'selected';
                    }else{
                        $selected = '';
                    }
                ?>
                <option class="p-2" value="<?= $c['city_village']; ?>" <?= $selected; ?> ><?= $c['city_village']; ?></option>
            <?php } ?>
            
        </select>
    </div>
    <div class="col-md-2 p-1">
        <select class="form-control" name="" id="sltCode">
            <option class="p-2" value="">Chọn Mã giới thiệu</option>
            <?php foreach($referralCode as $k => $rc){ ?>
                <?php
                    if($rc['referral_code'] == $rCode){
                        $selected = 'selected';
                    }else{
                        $selected = '';
                    }
                ?>
                <option class="p-2" value="<?= $rc['referral_code']; ?>" <?= $selected; ?> ><?= $rc['referral_code']; ?></option>
            <?php } ?>
            
        </select>
    </div>
    <div class="col-md-1 text-right p-lr-1">
        <button class="btn btn-primary" style="color: #fff; width: 100%" id="btnSearch"> Tìm </button>
    </div>
    <div class="col-md-1 text-right p-lr-1">
        <button type="button" class="btn btn-primary" style="color: #fff;width: 100%" id="btnExport"> Xuất Excel </button>
    </div>
   
    <div class="col-md-1 text-right p-lr-1">
        <button class="btn btn-primary" style="color: #fff; width: 100%" id="btnUpdate" data-toggle="modal" data-target="#updateModal"> Cập nhật </button>
    </div>
    <div class="col-md-1 text-right p-lr-1"  style="padding-right: 15px;">
        <button class="btn btn-primary" style="color: #fff; width: 100%" id="btnUpdateDs" data-toggle="modal" data-target="#updateDsModal"> Thêm DS </button>
    </div>
    <div class="col-12">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center" style="width: 50px !important;">#</th>
                <th class="text-white text-center" style="width: 12%;">Số điện thoại</th>
                <th class="text-white text-center" style="width: 23%;">Họ và tên</th>
                <th class="text-white text-center" style="width: 8%;">Ngày sinh</th>
                <th class="text-white text-center" style="width: 6%;">Giới tính</th>
                <th class="text-white text-center" style="width: 15%;">Mã giới thiệu</th>
                <th class="text-white text-center" style="width: 10%;">Ngày tạo</th>
                <th class="text-white text-center" style="width: 17%;">Tỉnh/Thành phố</th>
                <th class="text-white text-center" style="width: 5%;"></th>
            </tr>
            </thead>
            <tbody>
                
                <?php foreach($posts as $user){ ?>
                    <tr>
                        <td> <?= $user['index']; ?></td>
                        <td> <?= $user['value']; ?></td>
                        <td> <?= $user['given_name']; ?></td>
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
                        <td class="text-center"> <?= $user['code']; ?></td>
                        <td> <?= date('d-m-Y', strtotime($user['date_created'])); ?></td>
                        <td> <?= $user['city_village']; ?></td>
                        <td> 
                            
                            <a class="text-info" data-toggle="modal" data-target="#viewAccountModal_<?= $user['value']; ?>"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                            <a class="text-danger" href="#"> <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="viewAccountModal_<?= $user['value']; ?>" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form method="post" action="#">    
                            <div class="modal-dialog modal-lg">
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
    </div> 
    <div class="col-md-6" style="padding: 8px;">
        <?php $pager = \Config\Services::pager(); ?>
        <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
    </div>
    <div class="col-md-6 div-phantrang">
        <?php if ($pager):?>
            <?php $pagi_path = 'trang-quan-tri/tai-khoan/khach-hang'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div>  
</div>                  


<!-- /.modal -->
<div class="modal fade" id="updateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title font-weight-bold">Cập nhật thông tin tài khoản</h4> </div>

            <div class="modal-body">
                <form action="trang-quan-tri/tai-khoan/sua-thong-tin" method="post" enctype="multipart/form-data">
                    Upload excel file : 
                    <input type="file" name="uploadFile" value="" class="form-control" /><br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="form-control btn btn-default " data-dismiss="modal">Đóng</button>
                        </div>
                        <div class="col-6">
                            <input type="submit" class="form-control btn btn-primary" name="submit" value="Cập nhật" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>

<div class="modal fade" id="updateDsModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title font-weight-bold">Tạo tài khoản theo danh sách</h4> </div>
            <div class="modal-body">
                <form action="<?=base_url();?>/danh-sach/d4u/tao-tai-khoan" method="post" enctype="multipart/form-data">
                    Upload excel file : 
                    <input type="file" name="uploadFile" value="" class="form-control" /><br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="form-control btn btn-default " data-dismiss="modal">Close</button>
                        </div>
                        <div class="col-6">
                            <input type="submit" class="form-control btn btn-primary" name="submit" value="Upload" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>

<script>

    function saveUserInfo(sdt){
        var name    = $('#uGivenName'+sdt).val();
        var phone   = $('#uPhoneNumber'+sdt).val();
        var gender  = $('input[name=uGender'+sdt+']:checked').val();
        var email   = $('#uEmail'+sdt).val();
        var city    = $('#uCity'+sdt).val();
        var referral_code    = $('#magioithieu'+sdt).val();
     
        $.ajax({
            url: '<?= base_url();?>/update-account',
            type: 'post',
            data: {
                name: name,
                phone: phone,
                gender: gender,
                email: email,
                city: city,
                referral_code: referral_code
            },
            success: function(result){
                location.reload();
            }
        });
    }
    $(document).ready(function(){
        $("#sltCode").select2();
        $("#sltCity").select2();

        $('#btnSearch').click(function(){
            var link = '<?= base_url(); ?>' + '/trang-quan-tri/tai-khoan/tim-kiem-khach-hang?';
            var info = $('#inpInfo').val();
            var city = $('#sltCity').val();
            var rCode = $('#sltCode').val();
            if(info !== ''){ link += '&info=' + info; }
            if(city !== ''){ link += '&city=' + city; }
            if(rCode !== ''){ link += '&rCode=' + rCode; }
            window.location.href = link;
        });

        $('#btnExport').click(function(){
            var link = '<?= base_url(); ?>' + '/export-account?';
            var info = $('#inpInfo').val();
            var city = $('#sltCity').val();
            var rCode = $('#sltCode').val();
            if(info !== ''){ link += '&info=' + info; }
            if(city !== ''){ link += '&city=' + city; }
            if(rCode !== ''){ link += '&rCode=' + rCode; }
     
            $.ajax({
                url: link,
                type: 'post',
                dataType:"text",
                data: {
                    info: info,
                    city: city,
                    rCode: rCode
                },
                success : function (result){
                    window.location.href = result;
                }
            });
        });
 
    });

</script>