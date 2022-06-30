
<?php

?>

<div class="card p-3">
    <?php

        if(isset($birthdayNotify)){
            $birthdayNotifyContent = $birthdayNotify['settingValue'];
        }else{
            $birthdayNotifyContent = '';
        }

        if(isset($birthdaySMS)){
            $birthdaySMSContent = $birthdaySMS['settingValue'];
        }else{
            $birthdaySMSContent = '';
        }
    ?>
    <div class="col-12">
        <h3 class="card-title font-weight-bold mt-3" >CẤU HÌNH THÔNG BÁO CHÚC MỪNG SINH NHẬT</h3>
    </div>

    <div class="col-12">
        <table class="table table-bordered">
            <tbody>
            <tr class="row m-0">
                <td class="col-md-2">
                    Nội dung thông báo:
                </td>
                <td class="col-md-9">
                    <textarea class="form-control" name="birthdayNotifyContent" id="birthdayNotifyContent" rows="6" placeholder="Nội dung thông báo sinh nhật"><?= $birthdayNotifyContent; ?></textarea>
                </td>
                <td class="col-md-1">
                    <button class="btn btn-success" id="saveBirthdayNotifySetting" style="width: 100%;">Lưu</button>
                </td>
            </tr>
            <tr class="row m-0">
                <td class="col-md-2">
                    Nội dung SMS:
                </td>
                <td class="col-md-9">
                    <textarea class="form-control" name="birthdaySMSContent" id="birthdaySMSContent" rows="6" placeholder="Nội dung SMS sinh nhật"><?= $birthdaySMSContent; ?></textarea>
                </td>
                <td class="col-md-1">
                    <button class="btn btn-success" id="saveBirthdaySMSSetting" style="width: 100%;">Lưu</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>


<script>
    $('#saveBirthdayNotifySetting').click(function(){
        var birthdayNotifyContent = $('#birthdayNotifyContent').val();
        $.ajax({
            url: '<?= base_url();?>/trang-quan-tri/marketing/luu-thong-bao-sinh-nhat',
            type: 'post',
            data: {
                birthdayNotifyContent: birthdayNotifyContent
            },
            success: function(result){
                location.reload();
            }
        });
    });

    $('#saveBirthdaySMSSetting').click(function(){
        var birthdaySMSContent = $('#birthdaySMSContent').val();
        $.ajax({
            url: '<?= base_url();?>/trang-quan-tri/marketing/luu-sms-sinh-nhat',
            type: 'post',
            data: {
                birthdaySMSContent: birthdaySMSContent
            },
            success: function(result){
                location.reload();
            }
        });
    });
</script>