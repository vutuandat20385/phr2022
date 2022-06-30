<div class="col-12">
    <ul class="nav nav-pills mb-3" id="noti-tab" role="tablist">
        <li class="nav-item btn btn-outline-info btn-fw">
            <a class="nav-link" id="notification" data-toggle="pill" href="#notification-content" role="tab" aria-controls="notification" aria-selected="true">THÔNG BÁO</a>
        </li>
        <li class="nav-item btn btn-outline-danger btn-fw">
            <a class="nav-link" id="sms" data-toggle="pill" href="#sms-content" role="tab" aria-controls="sms" aria-selected="false">TIN NHẮN</a>
        </li>
        <li class="nav-item btn btn-outline-success btn-fw">
            <a class="nav-link" id="schedule" data-toggle="pill" href="#schedule-content" role="tab" aria-controls="schedule" aria-selected="false">LỊCH TÁI KHÁM</a>
        </li>
        <li class="nav-item btn btn-outline-info btn-fw">
            <a class="nav-link" id="remind-medicine" data-toggle="pill" href="#remind-medicine-content" role="tab" aria-controls="remind-medicine" aria-selected="false">NHẮC NHỞ ĐƠN THUỐC</a>
        </li>
        <li class="nav-item btn btn-outline-warning btn-fw">
            <a class="nav-link" id="remind-endExam" data-toggle="pill" href="#remind-endExam-content" role="tab" aria-controls="remind-endExam" aria-selected="false">NHẮC NHỞ KẾT THÚC TƯ VẤN</a>
        </li>
    </ul>
</div>
    
<div class="tab-content" id="pills-tabContent">
    <!-- Thông báo -->
    <div class="tab-pane fade" id="notification-content" role="tabpanel" aria-labelledby="notification-content">
        <div class="card tab-content-item">
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <?php
                            if(isset($title_result_notification)){
                                    $title_result_notification_value = $title_result_notification['settingValue'];
                            }else
                                $title_result_notification_value = '';

                            if(isset($content_result_notification)){
                                $content_result_notification_value = $content_result_notification['settingValue'];
                            }else
                                $content_result_notification_value = '';

                            if(isset($title_uploadpdf_covid)){
                                $title_uploadpdf_covid_value = $title_uploadpdf_covid['settingValue'];
                            }else
                                $title_uploadpdf_covid_value = '';

                            if(isset($content_uploadpdf_covid)){
                                $content_uploadpdf_covid_value = $content_uploadpdf_covid['settingValue'];
                            }else
                                $content_uploadpdf_covid_value = '';
                        ?>
                        <tr>
                            <td class="col-2" rowspan="2">Gửi Thông báo khi <span class="text-danger font-weight-bold">Import dữ liệu</span> kết quả khám sức khỏe</td>
                            <td class="col-2">Tiêu đề</td>
                            <td class="col-8"><input type="text" class="form-control" value="<?= $title_result_notification_value?>" id="resultTitleNotificationSetting"></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung</td>
                            <td class="col-8"><input type="text" class="form-control" value="<?= $content_result_notification_value?>" id="resultContentNotificationSetting"></td>
                        </tr>

                        <tr>
                            <td class="col-2" rowspan="2">Gửi Thông báo khi <span class="text-danger font-weight-bold">Upload PDF</span> kết quả test COVID</td>
                            <td class="col-2">Tiêu đề</td>
                            <td class="col-8"><input type="text" class="form-control" value="<?= $title_uploadpdf_covid_value?>" id="titleUploadPDFCovidSetting"></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung</td>
                            <td class="col-8"><input type="text" class="form-control" value="<?= $content_uploadpdf_covid_value?>" id="contentUploadPDFCovidSetting"></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="row m-0 mt-3">
                <div class="col-10">
                </div>
                <div class="col-2">
                    <button class="btn btn-success" id="saveNotificationSetting" style="width: 100%;">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS -->
    <div class="tab-pane fade" id="sms-content" role="tabpanel" aria-labelledby="sms-content">
        <div class="card tab-content-item">
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <?php
                        //SMS khi import ket qua kham benh
                            if(isset($defaultSMSResult) && $defaultSMSResult['settingValue'] === 'yes'){
                                $defaultSMSResult_value = 'checked';
                            }else
                                $defaultSMSResult_value = '';

                            if(isset($contentSMSResult_old['settingValue']) && $contentSMSResult_old['settingValue'] != ''){
                                $contentSMSResult_old_value = $contentSMSResult_old['settingValue'];
                            }else
                                $contentSMSResult_old_value = '';

                            if(isset($contentSMSResult_new['settingValue']) && $contentSMSResult_new['settingValue'] != ''){
                                $contentSMSResult_new_value = $contentSMSResult_new['settingValue'];
                            }else
                                $contentSMSResult_new_value = '';

                        //SMS khi import ket qua test Covid
                            if(isset($defaultSMSTestCovid) && $defaultSMSTestCovid['settingValue'] === 'yes'){
                                $defaultSMSTestCovid_value = 'checked';
                            }else
                                $defaultSMSTestCovid_value = '';

                            if(isset($contentSMSTestCovid_old) && $contentSMSTestCovid_old['settingValue'] != ''){
                                $contentSMSTestCovid_old_value = $contentSMSTestCovid_old['settingValue'];
                            }else
                                $contentSMSTestCovid_old_value = '';

                            if(isset($contentSMSTestCovid_new) && $contentSMSTestCovid_new['settingValue'] != ''){
                                $contentSMSTestCovid_new_value = $contentSMSTestCovid_new['settingValue'];
                            }else
                                $contentSMSTestCovid_new_value = '';

                        //SMS khi upload ket qua test Covid
                            if(isset($defaultSMSUploadFileCovid) && $defaultSMSUploadFileCovid['settingValue'] === 'yes'){
                                $defaultSMSUploadFileCovid_value = 'checked';
                            }else
                                $defaultSMSUploadFileCovid_value = '';

                            if(isset($contentSMSUploadFileCovid_old) && $contentSMSUploadFileCovid_old['settingValue'] != ''){
                                $contentSMSUploadFileCovid_old_value = $contentSMSUploadFileCovid_old['settingValue'];
                            }else
                                $contentSMSUploadFileCovid_old_value = '';

                            if(isset($contentSMSUploadFileCovid_new) && $contentSMSUploadFileCovid_new['settingValue'] != ''){
                                $contentSMSUploadFileCovid_new_value = $contentSMSUploadFileCovid_new['settingValue'];
                            }else
                                $contentSMSUploadFileCovid_new_value = '';
                            ?>
                            
                        <tr>
                            <td class="col-2" rowspan="3">Gửi SMS khi <span class="text-danger font-weight-bold">Import dữ liệu</span> kết quả khám sức khỏe</td>
                            <td class="col-2">Cấu hình mặc định</td>
                            <td class="col-8">
                                <label class="toggle-switch toggle-switch-info">
                                    <input type="checkbox" id="cb_defaultSMSResult" <?= $defaultSMSResult_value; ?>>
                                    <span class="toggle-slider round"></span>
                                </label>
                                Gửi SMS thông báo đến số điện thoại cho bệnh nhân
                            </td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản cũ</td>
                            <td class="col-8"><input type="text"  id="inp_contentSMSResult_old" class="form-control" value="<?= $contentSMSResult_old_value; ?>"></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản mới</td>
                            <td class="col-8"><input type="text"  id="inp_contentSMSResult_new" class="form-control" value="<?= $contentSMSResult_new_value; ?>"></td>
                        </tr>

                        <tr>
                            <td class="col-2" rowspan="3">Gửi SMS khi <span class="text-danger font-weight-bold">Import dữ liệu</span> kết quả test COVID</td>
                            <td class="col-2">Cấu hình mặc định</td>
                            <td class="col-8">
                                <label class="toggle-switch toggle-switch-danger">
                                    <input type="checkbox" id="cb_defaultSMSTestCovid" <?= $defaultSMSTestCovid_value; ?>>
                                    <span class="toggle-slider round"></span>
                                </label>
                                Gửi SMS thông báo đến số điện thoại cho bệnh nhân
                            </td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản cũ</td>
                            <td class="col-8"><input type="text" id="inp_contentSMSTestCovid_old" class="form-control" value="<?= $contentSMSTestCovid_old_value; ?>" ></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản mới</td>
                            <td class="col-8"><input type="text" id="inp_contentSMSTestCovid_new" class="form-control" value="<?= $contentSMSTestCovid_new_value; ?>" ></td>
                        </tr>

                        <tr>
                            <td class="col-2" rowspan="3">Gửi SMS khi <span class="text-danger font-weight-bold">upload file</span> kết quả test COVID</td>
                            <td class="col-2">Cấu hình mặc định</td>
                            <td class="col-8">
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" id="cb_defaultSMSUploadFileCovid" <?= $defaultSMSUploadFileCovid_value; ?>>
                                    <span class="toggle-slider round"></span>
                                </label>
                                Gửi SMS thông báo đến số điện thoại cho bệnh nhân
                            </td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản cũ</td>
                            <td class="col-8"><input type="text" id="inp_contentSMSUploadFileCovid_old" class="form-control" value="<?= $contentSMSUploadFileCovid_old_value; ?>"></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung cho tài khoản mới</td>
                            <td class="col-8"><input type="text" id="inp_contentSMSUploadFileCovid_new" class="form-control" value="<?= $contentSMSUploadFileCovid_new_value; ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row m-0 mt-3">
                <div class="col-10">
                </div>
                <div class="col-2">
                    <button class="btn btn-success" id="saveSMSSetting" style="width: 100%;">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch tái khám -->
    <div class="tab-pane fade" id="schedule-content" role="tabpanel" aria-labelledby="schedule-content">
        <?php
                if(isset($defaultNotifyFollow) && $defaultNotifyFollow['settingValue'] == 'yes'){
                    $defaultNotifyFollow_value = 'checked';
                }else{
                    $defaultNotifyFollow_value = '';
                }

                if(isset($defaultSMSFollow) && $defaultSMSFollow['settingValue'] == 'yes'){
                    $defaultSMSFollow_value = 'checked';
                }else{
                    $defaultSMSFollow_value = '';
                }

                if(isset($followNotifyContent) && $followNotifyContent['settingValue'] != ''){
                    $followNotifyContent_value = $followNotifyContent['settingValue'];
                }else{
                    $followNotifyContent_value = '';
                }

                if(isset($followSMSContent) && $followSMSContent['settingValue'] != ''){
                    $followSMSContent_value = $followSMSContent['settingValue'];
                }else{
                    $followSMSContent_value = '';
                }

                if(isset($followDate1) && $followDate1 != ''){
                    $followDate1_value = $followDate1['settingValue'];
                }else{
                    $followDate1_value = '';
                }

                if(isset($followDate2) && $followDate2 != ''){
                    $followDate2_value = $followDate2['settingValue'];
                }else{
                    $followDate2_value = '';
                }
        ?>
        <div class="card tab-content-item">
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <!-- Gửi thông báo -->
                        <tr>
                            <td class="col-2" rowspan="5">Gửi thông báo & SMS khi <span class="text-danger font-weight-bold">có lịch tái khám</span> với bác sĩ</td>
                            <td class="col-2">Cấu hình Notification</td>
                            <td class="col-8" colspan="4">
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" id="cb_defaultNotifyFollow" <?= $defaultNotifyFollow_value; ?>>
                                    <span class="toggle-slider round"></span>
                                </label>
                                Gửi Thông báo đến tài khoản trên App Doctor4U cho bệnh nhân
                            </td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung thông báo</td>
                            <td class="col-8" colspan="4"><input type="text"  id="inp_followNotifyContent" class="form-control" value="<?= $followNotifyContent_value; ?>"></td>
                        </tr>

                        <!-- Gửi SMS -->
                        <tr>
                            <td class="col-2">Cấu hình SMS</td>
                            <td class="col-8" colspan="4">
                                <label class="toggle-switch toggle-switch-warning">
                                    <input type="checkbox" id="cb_defaultSMSFollow" <?= $defaultSMSFollow_value; ?>>
                                    <span class="toggle-slider round"></span>
                                </label>
                                Gửi SMS đến số điện thoại cho bệnh nhân
                            </td>
                        </tr>
                        <tr>
                            <td class="col-2">Nội dung SMS</td>
                            <td class="col-8" colspan="4"><input type="text"  id="inp_followSMSContent" class="form-control" value="<?= $followSMSContent_value; ?>"></td>
                        </tr>
                        <tr>
                            <td class="col-2">Nhắc lần 1, trước ngày hẹn : </td>
                            <td class="col-3"><input type="text"  id="inp_followDateDistance_1st" class="miniTextBox" value="<?= $followDate1_value; ?>"> ngày</td>
                            <td class="col-2">Nhắc lần 2, trước ngày hẹn : </td>
                            <td class="col-3"><input type="text"  id="inp_followDateDistance_2nd" class="miniTextBox" value="<?= $followDate2_value; ?>"> ngày</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row m-0 mt-3">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success" id="saveFollowSetting" style="width: 100%;">Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nhắc nhở đơn thuốc -->
    <div class="tab-pane fade" id="remind-medicine-content" role="tabpanel" aria-labelledby="remind-medicine-content">
        <?php
            if(isset($medicineRemindPhoneList)){
                $r_phoneList = $medicineRemindPhoneList['settingValue'];
            }else{
                $r_phoneList = '';
            }

            if(isset($medicineRemindContent)){
                $remindContent = $medicineRemindContent['settingValue'];
            }else{
                $remindContent = '';
            }
        ?>
        <div class="card tab-content-item">
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="row m-0">
                            <td class="col-2">Danh sách số điện thoại:</td><td class="col-10"><input type="text" class="form-control" name="phoneListRemind" id="phoneListRemind" value="<?= $r_phoneList;?>" placeholder="0977123456, 0978345678"></td>
                        </tr>
                        <tr class="row m-0">
                            <td class="col-2">Nội dung SMS:</td><td class="col-10"><input type="text" class="form-control" name="contentRemind" id="contentRemind" value="<?= $remindContent;?>" placeholder="Bác sĩ đã kết thúc khám, hãy vào xem & cập nhật đơn thuốc! "></td>
                        </tr>
                    </tbody>
                </table>
                <div class="row m-0 mt-3">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success" id="saveMedicineRemindSetting" style="width: 100%;">Lưu</button>
                    </div>
                </div>
            </div>
         </div>
    </div>

    <!-- Nhắc kết thúc khám tư vấn -->
    <div class="tab-pane fade" id="remind-endExam-content" role="tabpanel" aria-labelledby="remind-endExam-content">
        <?php
            if(isset($appCompleteRemindContent)){
                $appRemindContent = $appCompleteRemindContent['settingValue'];
            }else{
                $appRemindContent = '';
            }
        ?>

        <div class="card tab-content-item">
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="row m-0">
                            <td class="col-md-2">Nội dung SMS:</td><td class="col-md-10"><input type="text" class="form-control" name="appContentRemind" id="appContentRemind" value="<?= $appRemindContent;?>" placeholder="Bác sĩ vui lòng vào app để kết thúc các cuộc tư vấn đang diễn ra! "></td>
                        </tr>
                    </tbody>
                </table>
                <div class="row m-0 mt-3">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success" id="saveAppCompleteRemindSetting" style="width: 100%;">Lưu</button>
                    </div>
                </div>
        </div>
         </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#saveNotificationSetting').click(function(){

            var resultTitleNotificationValue    = $('#resultTitleNotificationSetting').val();
            var resultContentNotificationValue  = $('#resultContentNotificationSetting').val();

            var titleUploadpdfCovid     = $('#titleUploadPDFCovidSetting').val();
            var contentUploadpdfCovid   = $('#contentUploadPDFCovidSetting').val();

                $.ajax({
                    url : "<?= site_url();?>trang-quan-tri/cau-hinh/cap-nhat",
                    type : "post",
                    dataType:"text",
                    data : {
                        settingType : 'notification',
                        resultTitleNotificationValue    : resultTitleNotificationValue,
                        resultContentNotificationValue  : resultContentNotificationValue,
                        titleUploadpdfCovid             : titleUploadpdfCovid,
                        contentUploadpdfCovid           : contentUploadpdfCovid
                    },
                    success : function (result){
                        location.reload();
                    }
                });
        });

        $('#saveSMSSetting').click(function(){
            //SMS khi import du lieu
            var cb_defaultSMSResult = $('#cb_defaultSMSResult:checked').val();
            if(cb_defaultSMSResult == 'on'){
                defaultSMSResult = 'yes';
            }else
                defaultSMSResult = 'no';

            var contentSMSResult_old = $('#inp_contentSMSResult_old').val();
            var contentSMSResult_new = $('#inp_contentSMSResult_new').val();

            //SMS khi import ket qua test Covid
            var cb_defaultSMSTestCovid = $('#cb_defaultSMSTestCovid:checked').val();
            if(cb_defaultSMSTestCovid == 'on'){
                defaultSMSTestCovid = 'yes';
            }else
                defaultSMSTestCovid = 'no';

            var contentSMSTestCovid_old = $('#inp_contentSMSTestCovid_old').val();
            var contentSMSTestCovid_new = $('#inp_contentSMSTestCovid_new').val();

            //SMS khi upload file ket qua test Covid
            var cb_defaultSMSUploadFileCovid = $('#cb_defaultSMSUploadFileCovid:checked').val();
            if(cb_defaultSMSUploadFileCovid == 'on'){
                defaultSMSUploadFileCovid = 'yes';
            }else
                defaultSMSUploadFileCovid = 'no';

            var contentSMSUploadFileCovid_old = $('#inp_contentSMSUploadFileCovid_old').val();
            var contentSMSUploadFileCovid_new = $('#inp_contentSMSUploadFileCovid_new').val();

            $.ajax({
                url : "<?= site_url();?>trang-quan-tri/cau-hinh/cap-nhat",
                type : "post",
                dataType:"text",
                data : {
                    settingType : 'sms',

                    defaultSMSResult : defaultSMSResult,
                    contentSMSResult_old : contentSMSResult_old,
                    contentSMSResult_new: contentSMSResult_new,

                    defaultSMSTestCovid: defaultSMSTestCovid,
                    contentSMSTestCovid_old: contentSMSTestCovid_old,
                    contentSMSTestCovid_new: contentSMSTestCovid_new,

                    defaultSMSUploadFileCovid: defaultSMSUploadFileCovid,
                    contentSMSUploadFileCovid_old: contentSMSUploadFileCovid_old,
                    contentSMSUploadFileCovid_new: contentSMSUploadFileCovid_new
                },
                success : function (result){
                    location.reload();
                }
            });
        });  

        $('#saveFollowSetting').click(function(){
            var defaultFollowNotify = $('#cb_defaultNotifyFollow:checked').val();
            var contentFollowNotify = $('#inp_followNotifyContent').val();

            var defaultFollowSMS = $('#cb_defaultSMSFollow:checked').val();
            var contentFollowSMS = $('#inp_followSMSContent').val();

            var followDate1 = $('#inp_followDateDistance_1st').val();
            var followDate2 = $('#inp_followDateDistance_2nd').val();

            if(defaultFollowNotify == 'on'){
                defaultFollowNotify = 'yes';
            }else
                defaultFollowNotify = 'no';

            if(defaultFollowSMS == 'on'){
                defaultFollowSMS = 'yes';
            }else
                defaultFollowSMS = 'no';

            $.ajax({
                url : "<?= site_url();?>trang-quan-tri/cau-hinh/cap-nhat",
                type : "post",
                dataType:"text",
                data : {
                    settingType : 'follow',
                    defaultFollowNotify : defaultFollowNotify,
                    contentFollowNotify : contentFollowNotify,
                    defaultFollowSMS : defaultFollowSMS,
                    contentFollowSMS : contentFollowSMS,
                    followDate1 : followDate1,
                    followDate2 : followDate2
                },
                success : function (result){
                    location.reload();
                }
            });      
        });

        $('#saveMedicineRemindSetting').click(function(){
            var phoneList = $('#phoneListRemind').val();
            var contentRemind = $('#contentRemind').val();
            $.ajax({
                url: '<?= site_url();?>trang-quan-tri/cau-hinh/nhac-nho-thuoc',
                type: 'post',
                data: {
                    phoneList: phoneList,
                    contentRemind: contentRemind
                },
                success: function(result){
                    location.reload();
                }
            });
        });

        $('#saveAppCompleteRemindSetting').click(function(){
            var appContentRemind = $('#appContentRemind').val();
            $.ajax({
                url: '<?= site_url();?>trang-quan-tri/cau-hinh/nhac-ket-thuc-kham',
                type: 'post',
                data: {
                    appContentRemind: appContentRemind
                },
                success: function(result){
                    location.reload();
                }
            });
        });

    });
</script>