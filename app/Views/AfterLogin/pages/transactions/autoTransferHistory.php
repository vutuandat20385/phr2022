<div class="card p-3">
    <div class="row">
        <div class="col-6 pt-1">
            <h4 class="contentHeader"><?= $pageTitle; ?></h4>
        </div>
        <div class="col-2 offset-3"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="SĐT" value="<?= $info; ?>"></div>
        <div class="col-1"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
    </div>

    <div class="card-body" style="padding-top: 0;">
        <?php if ($history) { ?>
        <table id="datatable" class="table table-bordered d4u-table">
            <thead>
                <tr class="bg-primary text-white">
                    <th class="text-center">STT</th>
                    <th>SĐT</th>
                    <th class="text-center">Giờ chuyển khoản</th>
                    <th class="text-center">Số tiền</th>
                    <th class="text-center">Mã giao dịch</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Giờ nạp tiền</th>
                    <th></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $k => $val) { 
                    if($val['index'] > 0){
                        if($val['status'] == 1){
                            $status = 'Nạp tiền thành công';
                            $class_status = 'text-success font-weight-bold';
                        }else{
                            $status = 'Nạp tiền thất bại';
                            $class_status = 'text-danger';
                        }
                ?>
                <tr class="">
                    <td class="text-center"><?= $val['index']; ?></td>
                    <td><?= $val['phone_number']; ?></td>
                    <td class="text-center"><?= date('d/m/Y H:i',strtotime($val['time_transfer'])); ?></td>
                    <td class="text-center"><?= $val['amount']; ?></td>
                    <td class="text-center"><?= $val['transaction_code']; ?></td>
                    <td class="text-center <?= $class_status; ?>"><?= $status; ?></td>
                    <td class="text-center"><?= date('d/m/Y H:i:s',strtotime($val['date_modify'])); ?></td>
                    <td><i class="material-icons" data-toggle="modal" data-target="#modalViewContent_<?= $val['id']; ?>">remove_red_eye</i></td>
                    <!-- /.modal -->
                    <div class="modal fade" id="modalViewContent_<?= $val['id']; ?>">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header"> <h4 class="modal-title">Nội dung tin nhắn</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="row m-0" style="background: #7573731f;padding: 5px;">
                                <?= $val['sms_content']; ?>
                                </div>
                            </div>
                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                        
                    </div>    
                </tr>
            
                <?php }
                }
                ?>

            </tbody>

        </table>
        <?php $pager = \Config\Services::pager(); ?>
        <div class="row">
            <div class="col-md-6">
                <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
            </div>
            <div class="col-md-6 div-phantrang">
                <?php if ($pager):?>
                    <?php $pagi_path = 'trang-quan-tri/giao-dich/chuyen-khoan'; ?>
                    <?php $pager->setPath($pagi_path); ?>
                    <?= $pager->links(); ?>                  
                <?php endif; ?>            
            </div>           
        </div>  
        <?php }else{
                        echo 'Chưa có nội dung';
                    }  ?>

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
            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/giao-dich/chuyen-khoan?page=1';

            if(info != ''){ link += '&info='+info; }

            window.location.replace(link);

        });
        
    });

</script>