<div class="row p-1" style="margin-left: 0;">
    <div class="col-12">
        <h4 ><?= $panelTitle; ?></h4>
    </div>
    
    <div class="col-3"></div>
    <div class="col-3"></div>
    <div class="col-4"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="Họ tên, SĐT" value="<?= $info; ?>"></div>
    <div class="col-1"><button class="btn btn-outline-success btn-fw" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
    <div class="col-1"> <a href="" data-toggle="modal" data-target="#modal-import" class="btn btn-outline-warning btn-fw" style="width: 100%;">Import</a></div>
    <div class="col-12 mt-1">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center" style="width: 50px !important;">#</th>
                <th class="text-white text-center" style="width: 15%;">Họ và tên</th>
                <th class="text-white text-center" style="width: 10%;">Số điện thoại</th>
                <th class="text-white text-center" style="width: 10%;">Giới tính</th>
                <th class="text-white text-center" style="width: 15%;">Ngày sinh</th>
                <th class="text-white text-center" style="">Địa chỉ</th>
                <th class="text-white text-center" style="width: 150px;"></th>
            </tr>
            </thead>
            <tbody>
                <?php if($posts){?>
                    <?php foreach($posts as $user){ ?>
                        <tr>
                            <td><?= $user['index']; ?></td>
                            <td><?= $user['full_name']; ?></td>
                            <td><?= $user['phone_number']; ?></td>
                            <td>
                                <?php if($user['gender'] =='Nam' || $user['gender'] =='M' || $user['gender'] =='Male'){
                                    echo 'Nam';
                                }else if($user['gender'] == 'Nữ' || $user['gender'] =='F' || $user['gender'] =='Female'){
                                    echo 'Nữ';
                                }else{
                                    echo '';
                                }; ?>
                            </td>
                            <td><?php if($user['birthdate'] != ''){
                                            echo date('d-m-Y',strtotime($user['birthdate']));
                                        } ; ?>
                            </td>
                            <td><?= $user['address']; ?></td>
                            <td>
                                <a class="btn btn-outline-warning btn-fw" data-toggle="modal" data-target="#modal-lg<?= $val['annual_checkup_id'] ?>">Chi tiết</a>
                            </td>
                        </tr>

                        <!-- /.modal -->
                        <div class="modal fade" id="modal-lg<?= $val['annual_checkup_id'] ?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header"> <h4 class="modal-title">Thông tin PHR bệnh nhân</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <?php 
                                            if($user['history']){
                                                foreach($user['history'] as $h){ ?>
                                            <div class="col-3"><a class="btn btn-primary form-control" href="<?= base_url('trang-quan-tri/benh-an/d4u-khach-le/chi-tiet-benh-an').'/'.$h['annual_checkup_id'];?>"><?= $h['examination_date']; ?></a></div> 
                                        <?php }
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="modal-footer text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                            
                        </div>
                        
                    <?php } ?>
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
            <?php $pagi_path = 'trang-quan-tri/benh-an/d4u-khach-doan'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>
<!-- Modal import PHR -->
<div class="modal fade" id="modal-import">
    <?= $form_importModal; ?>
</div>

<script>

$(document).ready(function(){
        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/benh-an/d4u-khach-doan?page=1';

            if(info != ''){ link += '&info='+info; }

            window.location.replace(link);

        });
        
    });
  

</script>