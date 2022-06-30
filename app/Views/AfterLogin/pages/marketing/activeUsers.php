<div class="card p-3">
    <div class="row">
        <div class="col-12">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
    </div>
    <div class="col-12 mt-1 p-0">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center" style="width: 50px !important;">#</th>
                <th class="text-white text-center" style="width: 10%;">Số điện thoại</th>
                <th class="text-white text-center" style="width: 15%;">Họ và tên</th>
                <th class="text-white text-center" style="width: 15%;">Hoạt động lần cuối</th>
                <th class="text-white text-center" style="">Ghi chú</th>
                <th class="text-white text-center" style="width: 50px;"></th>
            </tr>
            </thead>
            <tbody>
                <?php if($posts){?>
                    <?php foreach($posts as $user){ ?>
                        <tr>
                            <td> <?= $user['index']; ?></td>
                            <td class="text-center"> <?= $user['value']; ?></td>
                            <td> <?= $user['given_name']; ?></td>
                            <td class="text-center"> <?= date('d-m-Y H:i:s', strtotime($user['last_active'])); ?></td>
                            <td> <?= $user['note']; ?></td>
                            <td class="text-center"> 
                                <a class="text-info" data-toggle="modal" data-target="#viewAccountModal_<?= $user['value']; ?>"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="viewAccountModal_<?= $user['value']; ?>" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-weight-bold" id="">CẬP NHẬT THÔNG TIN GHI CHÚ </h5>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" id="phoneNumber_<?= $user['value']; ?>" value="<?= $user['value']; ?>" hidden>
                                        <textarea class="form-control" id="noteInfo_<?= $user['value']; ?>" name="noteInfo_<?= $user['value']; ?>" placeholder="Nhập nội dung ghi chú" rows="6"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" onClick="saveNoteInfo('<?= $user['value']; ?>')">Lưu thông tin</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    </div>
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
            <?php $pagi_path = 'trang-quan-tri/marketing/tai-khoan-hoat-dong'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>


<script>

    function saveNoteInfo(sdt){
        var phone   = $('#phoneNumber_'+sdt).val();
        var note    = $('#noteInfo_'+sdt).val();

        $.ajax({
            url: '<?= base_url();?>/trang-quan-tri/marketing/cap-nhat-ghi-chu',
            type: 'post',
            data: {
                phone: phone,
                note: note
            },
            success: function(result){
                location.reload();
            }
        });
    }
  

</script>