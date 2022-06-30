<?php 
    if(isset($listTruePhone)){
        $listPhone = $listTruePhone;
    }else{
        $listPhone = '';
    }

    if(isset($listFailPhone)){
        $failPhone = $listFailPhone;
    }else{
        $failPhone = '';
    }

    if(isset($countTruePhone)){
        $countTrue = $countTruePhone;
    }else{
        $countTrue = 0;
    }

    if(isset($countFailPhone)){
        $countFail = $countFailPhone;
    }else{
        $countFail = 0;
    }
?>
<div class="div-header text-center">
    <h2 class=""><?= $panelTitle;?></h2>
</div>
<div class="card p-3 mb-3">
    <form >
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group border rounded p-2">
                    <span class="font-weight-bold">Danh sách số điện thoại </span> (  <span id="countPhone"><?= $countTrue; ?></span> ) 
                    <a data-toggle="modal" data-target="#modalImportPhone" class="btn btn-primary text-white" id="btnImport">Import</a><br>
                    <textarea class="form-control" id="listPhoneNumber" name="listPhoneNumber" rows="12" value=""><?= trim($listPhone,','); ?></textarea>
                </div>
                <div class="form-group border rounded p-2">
                    <span class="font-weight-bold">Nội dung tin nhắn</span> ( <span id="countChar">0</span> / 160 )<br>
                    <textarea class="form-control" id="contentSMS" name="contentSMS" rows="3"></textarea>
                </div>
                <div class="col-md-6 p-0 text-left">
                    <a class="btn btn-primary text-white" id="submitSMS" onclick="myFunction_sms()">Gửi tin nhắn</a>
                    <p class="small">Danh sách số điện thoại phân tách nhau bởi dấu ","</p>
                </div>
                
            </div>
            <div class="col-md-6">
                <div class="form-group border rounded p-2" style="min-height: 395px;background: #f0f3f6;">
                    <span class="font-weight-bold">Kết quả</span><br>
                    <div class="listResult" id="listResult">
                        <?php if($countFail > 0 ){?>
                        Có <?= $countFail; ?> số điện thoại sai: <br>
                        <?= trim($failPhone,','); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
 <!-- modal -->
 <div class="modal fade" id="modalImportPhone">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title font-weight-bold">IMPORT DANH SÁCH</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form action="<?=base_url();?>/upload-phone-list" method="post" enctype="multipart/form-data">
                File Excel : 
                <input type="file" name="uploadPhoneListFile" value="" class="form-control" />
                <br>
                <a class="mb-3 btn btn-info" style="width: 100%;" href="<?= base_url();?>/public/Download/DanhSachSDT.xlsx">Download Template</a>
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-light btn-fw" data-dismiss="modal" style="width: 100%;">Đóng</button>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-outline-primary btn-fw" id="btnUploadPhoneList" style="width: 100%;">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal -->

<script>
    function myFunction_sms() {
        var content         = document.getElementById("contentSMS").value;
        var strPhoneNumber  = document.getElementById("listPhoneNumber").value;

        arrPhoneNumber = strPhoneNumber.split(',');
        
        for(i=0; i<arrPhoneNumber.length; i++){
            if(arrPhoneNumber[i] != ''){
                $.ajax({
                    url : "<?= site_url();?>send-sms",
                    type : "post",
                    data : {
                            content: content,
                            phone: arrPhoneNumber[i]
                    },
                    success : function (result){
                        $('#listResult').prepend(result + '<br>');
                    },
                    error: function (phone, result){
                        $('#listResult').prepend('<span class="font-weight-bold">' + phone +'</span> - <span class="text-warning">Thất bại </span><br>');
          
                    }
                });
            }
        }
    }

    function check_sdt(sdt){
        var sdt = sdt.trim();
        var vnf_regex = /(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b/;
        if(sdt.length > 10){
            return false;
        }else{
            if(vnf_regex.test(sdt) == false){
                return false;
            }else{
                return true;
            }
        }
    }

    $('#contentSMS').keyup(function(){
        var content = document.getElementById("contentSMS").value;
        var leng = content.trim().length;
        $('#countChar').html(leng);
    });

    $('#listPhoneNumber').change(function(){
        var strPhoneNumber  = document.getElementById("listPhoneNumber").value;
        arrPhoneNumber = strPhoneNumber.split(',');
       
        arr_true = [];
        arr_fail = [];
        for(i=0; i<arrPhoneNumber.length; i++){
            sdt = arrPhoneNumber[i];
            if(check_sdt(sdt)){
                arr_true.push(sdt);
            }else{
                arr_fail.push(sdt);
            }
        }
        uniqueFail = [...new Set(arr_fail)];
        if(uniqueFail.length > 0){
            $('#listResult').append('Có '+uniqueFail.length+' số điện thoại sai: <br>');
            for(j=0; j<=uniqueFail.length; j++){
                $('#listResult').append(uniqueFail[j]);
                $('#listResult').append('<br>');
            }
        }

        uniqueTrue = [...new Set(arr_true)];
        if(uniqueTrue.length > 0){
            list = uniqueTrue.toString();
            $('#countPhone').html(uniqueTrue.length);
            $('#listPhoneNumber').val(list);
        }
    
    });

</script>