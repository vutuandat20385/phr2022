<?php
    if(isset($default_send_SMS) && $default_send_SMS['settingValue'] === 'yes'){
        $checkSMS = 'checked';
    }else
        $checkSMS = '';
?>
<div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header"> <h4 class="modal-title font-weight-bold"><?= $title; ?></h4> </div>
        <div class="modal-body">
            <form action="<?=base_url();?>/import-test-covid" method="post" enctype="multipart/form-data">
                Upload excel file : 
                <input type="file" name="uploadFile" value="" class="form-control" /><br>

                <div class="form-check">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value="sms" name="checkSMS" <?= $checkSMS; ?> /> Gửi tin nhắn SMS cho bệnh nhân
                        <span class="form-check-sign">
                            <span class="check"></span>
                        </span>
                    </label>
                </div>
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
<!-- /.modal-content -->
</div>