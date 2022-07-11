<?php
    if(isset($default_send_SMS) && $default_send_SMS['settingValue'] === 'yes'){
        $checkSMS = 'checked';
    }else
        $checkSMS = '';
?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header"> <h4 class="modal-title font-weight-bold"><?= $title; ?></h4> </div>
        <div class="modal-body">
            <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
                Upload excel file : 
                <input type="file" name="uploadFile" value="" class="form-control" /><br>

                <?php if($type == 'khach-doan'){ ?>
                    Tên công ty khách đoàn:
                    <input type="text" name="company" value="" class="form-control" /><br>
                <?php } ?>

                <input type="text" name="hospitalId" value="<?= $hospital_id; ?>" class="form-control" hidden />
                <input type="text" name="hospital" value="<?= $hospital; ?>" class="form-control" hidden />
                <div class="form-check form-check-success">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" value="sms" name="checkSMS" <?= $checkSMS; ?> >
                        Gửi tin nhắn SMS cho khách hàng
                    <i class="input-helper"></i></label>
                </div>
                <br>
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="form-control btn btn-default " data-dismiss="modal">Đóng</button>
                    </div>
                    <div class="col-6">
                        <input type="submit" class="form-control btn btn-primary" name="submit" value="Upload" />
                    </div>
                </div>
                
            </form>
        </div>
    </div>
<!-- /.modal-content -->
</div>