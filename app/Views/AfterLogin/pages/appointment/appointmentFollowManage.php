 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        </button>
    </div>
</nav>
<!-- End Navbar -->
<div class="row">
    <div class="col-12">
        <div class="card" style="margin-top:0 !important;">
            <div class="row card-header" style="padding-bottom: 0;">
                <div class="col-12 text-center"></div>
                <div class="col-2"><input type="text" id="date_from" name="date_from" class="form-control u_datepicker date_from" placeholder="Từ ngày" value="<?= $date_from; ?>"></div>
                <div class="col-2"><input type="text" id="date_to" name="date_to" class="form-control u_datepicker date_to" placeholder="Đến ngày" value="<?= $date_to; ?>"></div>
                <div class="col-4"><input type="text" name="search_info" class="form-control" placeholder="Mã, Tên người khám" value="<?= $info; ?>"></div>
                <div class="col-2"></div>
                <div class="col-2"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
            </div>
            <div class="card-body" style="padding-top: 0;">
                    <?php if($appointmentList){?>
                        <table id="datatableAppointmentList" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th>Mã</th>
                                    <th>Tên BN</th>
                                    <th>Bác sĩ</th>
                                    <th style="width: 120px;">Giờ tư vấn</th>
                                    <th>Triệu chứng</th>
                                    <th>Chẩn đoán</th>
                                    <th>Đề nghị của bác sĩ</th>
                                    <th style="width: 90px;">Lịch hẹn</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($appointmentList as $val) {
                                    if($val['index'] > 0){ ?>
                                    <tr>
                                        <td><?= $val['index']; ?></td>
                                        <td><?= $val['appointment_code']; ?></td>
                                        <td><?= $val['patient_name']; ?></td>
                                        <td><?= $val['provider_name']; ?></td>
                                        <td class="text-center">
                                            <?php
                                                $date = date('d/m/Y', strtotime($val['start_date']));
                                                $time_start = date('H:i', strtotime($val['start_date']));
                                                $time_end = date('H:i', strtotime($val['end_date']));
                                                echo $date.'<br>('.$time_start.'-'.$time_end.')';
                                            ?>
                                        </td>
                                        <td><?php
                                            $trieu_chung = $val['trieu_chung'];
                                            $tt_str = implode(',',$trieu_chung);
                                        ?>
                                        <?php if (!empty($trieu_chung)){ ?>
                                            <?php foreach ($trieu_chung as $k => $tc){ ?>
                                                <div class="" title="<?= $tc ?>">
                                                    <?= text_limit($tc,10) ?>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                        </td>
                                        <td><?php
                                            $chan_doan = $val['chan_doan'] ?>
                                                <?php if(isset($chan_doan['chandoan_sobo']) && $chan_doan['chandoan_sobo'] != []){
                                                    $cd_str = implode(',',$chan_doan['chandoan_sobo']);
                                                ?>
                                                <div class="">
                                                    <span class="font-weight-bold">Chẩn đoán sơ bộ: </span>
                                                    <?php foreach ($chan_doan['chandoan_sobo'] as $k => $sb){ ?>
                                                        <div class="" title="<?= $sb ?>">
                                                            <?= text_limit($sb,10) ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>
                                                <?php
                                                    if(isset($chan_doan['chandoan_xacdinh']) && $chan_doan['chandoan_xacdinh'] != []){
                                                        $cd_str = implode(',',$chan_doan['chandoan_xacdinh']);
                                                ?>
                                                <div class="">
                                                    <span class="font-weight-bold">Chẩn đoán xác định: </span>
                                                    <?php foreach ($chan_doan['chandoan_xacdinh'] as $k => $xd){ ?>
                                                        <div class="" title="<?= $xd ?>">
                                                            <?= text_limit($xd,10) ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>
                                                <?php if (empty($chan_doan['chandoan_sobo']) && empty($chan_doan['chandoan_xacdinh'])){
                                                    $cd_str = '';
                                                    ?>
                                                    <div class="">
                                                        <span>Chưa có thông tin</span>
                                                    </div>
                                                <?php } ?>

                                        <?php

                                        ?></td>
                                        <td>
                                            <?php $feedback = $val['feedback'];
                                                $fb_str = implode(',',$feedback);
                                            ?>
                                            <?php if (!empty($feedback)){ ?>
                                                <?php foreach ($feedback as $k => $fb){ ?>
                                                    <div class="" title="<?= $fb ?>">
                                                        <?= text_limit($fb,10) ?>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td><?php
                                            if($val['follow_date'] != ''){
                                                echo date('d/m/Y',strtotime($val['follow_date']));
                                            }
                                         ?>
                                         </td>
                                        <td><?php
                                            switch ($val['status']) {
                                                case 'COMPLETED':
                                                    echo '<b class="text-success">Hoàn thành</b>';
                                                    break;
                                                case 'CANCELLED':
                                                    echo '<b class="text-danger">Đã hủy</b>';
                                                    break;
                                                case 'WAITING_EXAMINATION':
                                                    echo '<b class="text-primary">Chờ xét nghiệm</b>';
                                                    break;
                                                case 'WAITING_PAYMENT':
                                                    echo '<b class="text-warning">Chờ thanh toán</b>';
                                                    break;
                                                case 'INCONSULTATION':
                                                    echo '<b class="text-blue">Đang diễn ra</b>';
                                                    break;
                                                case 'SCHEDULED':
                                                    echo '<b class="text-info">Sắp diễn ra</b>';
                                                    break;
                                                case 'MISSED':
                                                    echo '<b class="text-secondary">Không liên lạc được</b>';
                                                    break;
                                                default:
                                                    # code...
                                                    break;
                                            }

                                            ?>
                                        </td>
                                        <td><?= $val['note']; ?></td>
                                        <td class="text-center"><a onClick="showModalEditAppointment(<?= $val['appointment_id']; ?>,'<?= $val['patient_name']; ?>', '<?= $val['sdt']; ?>', '<?= $val['provider_name']; ?>', '<?= $date; ?>', '<?= $tt_str; ?>', '<?= $cd_str; ?>', '<?= $fb_str; ?>', '<?= isset($val['re_exam_date'])? date('d/m/Y', strtotime($val['re_exam_date'])) : ''; ?>', '<?= $val['note']; ?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></td>
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
                                    <?php $pagi_path = 'trang-quan-tri/tu-van-qua-app/quan-ly-tu-van'; ?>
                                    <?php $pager->setPath($pagi_path); ?>
                                    <?= $pager->links(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php }else{
                        echo 'Không tìm thấy kết quả.';
                    } ?>
            </div>       
         </div>       
     </div> 
 </div>
<div id="modalEdit"></div>
<script>
    $(document).ready(function(){

        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var date_from = $('input[name=date_from]').val();
            var date_to = $('input[name=date_to]').val();

            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/tu-van-qua-app/quan-ly-tu-van?page=1';

            if(info != ''){ link += '&info='+info; }
            if(date_from != ''){ link += '&date_from='+date_from; }
            if(date_to != ''){ link += '&date_to='+date_to; }

            window.location.replace(link);

        });

        $('#date_from').datetimepicker({
            format:'d/m/Y',
            lang:'vi',
            onShow:function( ct ){
                this.setOptions({
                    formatDate:'d/m/Y',
                    maxDate:$('#date_to').val()?$('#date_to').val():false
                })
            },
            timepicker:false
        });
        $('#date_to').datetimepicker({
            format:'d/m/Y',
            lang:'vi',
            onShow:function( ct ){
                this.setOptions({
                    formatDate:'d/m/Y',
                    minDate:$('#date_from').val()?$('#date_from').val():false
                })
            },
            timepicker:false
        });
        
    });

    function showModalEditAppointment(id, benhnhan,sdt_benhnhan, bacsi, ngaykham, trieuchung, chandoan, phanhoi, lichtaikham, ghichu){
   
        $modal = "<div class='modal' tabindex='-1' role='dialog' id='editAppointment_"+id+"' style='font-size: 14px;'>"+
                    "<div class='modal-dialog' role='document' style='margin-top: 50px;'>"+
                        "<div class='modal-content'>"+
                            "<div class='modal-header'>"+
                                "<h5 class='modal-title font-weight-bold'>CẬP NHẬT THÔNG TIN CUỘC TƯ VẤN</h5>"+
                            "</div>"+
                            "<form method='post' action='<?= base_url();?>/trang-quan-tri/tu-van-qua-app/cap-nhat-quan-ly-tu-van'>"+
                                "<div class='modal-body' style='padding-top: 10px;'>"+
                                    "<span>Bệnh nhân: <a class='font-weight-bold'>"+benhnhan+"</a> - SĐT: <a class='font-weight-bold'>"+sdt_benhnhan+"</a></span><br>"+
                                    "<span>Bác sĩ: <a class='font-weight-bold'>"+bacsi+"</a> - Ngày khám: <a class='font-weight-bold'>"+ngaykham+"</a></span><hr>"+
                                    "<label>Ngày tái khám</label>"+
                                    "<input type='text' class='form-control lichtaikham bg-light' name='re_exam' id='re_exam_"+id+"' value='"+lichtaikham+"' readonly></input>"+
                                    "<label>Ghi chú</label>"+
                                    "<textarea class='form-control bg-light' id='ghichu_"+id+"' name='note' rows='2' >"+ghichu+"</textarea>"+
                                    "<input type='text' class='form-control hidden' name='appointment_id' id='appointment_id_"+id+"'value='"+id+"'></input>"+
                                "</div>"+
                                "<div class='modal-footer'>"+
                                    "<button type='submit' class='btn btn-primary'>LƯU THÔNG TIN</button>"+
                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>ĐÓNG</button>"+
                                "</div>"+
                            "</form>"+
                        "</div>"+
                    "</div>"+
                "</div>";
        $('#modalEdit').html($modal);
        $('#editAppointment_'+id).modal('show');
        $(".lichtaikham").datetimepicker({
    
            format:"DD/MM/YYYY",
            icons:{
                time:"fa fa-clock-o",
                date:"fa fa-calendar",
                up:"fa fa-chevron-up",
                down:"fa fa-chevron-down",
                previous:"fa fa-chevron-left",
                next:"fa fa-chevron-right",
                today:"fa fa-screenshot",
                clear:"fa fa-trash",
                close:"fa fa-remove"
            },
            locale: "vi"

        });
       
    }


</script>