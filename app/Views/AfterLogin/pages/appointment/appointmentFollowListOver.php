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
                <div class="col-10 text-center">
                    <ul class="nav nav-pills nav-pills-warning" role="tablist">
                        <li class="nav-item"> <a class="nav-link" href="<?= base_url(); ?>/trang-quan-tri/tu-van-qua-app/lich-tai-kham"> DS lịch tái khám </a></li>
                        <li class="nav-item"> <a class="nav-link active" href="<?= base_url(); ?>/trang-quan-tri/tu-van-qua-app/lich-tai-kham-qua-hen"> Lịch tái khám quá hẹn </a> </li>
                    </ul>
                </div>  
                <div class="col-2">
                    <button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button>
                    <button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Xuất Excel</button>
                </div>
                <div class="col-2"><input type="text" id="date_from" name="date_from" class="form-control u_datepicker date_from" placeholder="Từ ngày" value="<?= $date_from; ?>"></div>
                <div class="col-2"><input type="text" id="date_to" name="date_to" class="form-control u_datepicker date_to" placeholder="Đến ngày" value="<?= $date_to; ?>"></div>
                <div class="col-4"><input type="text" name="search_info" class="form-control" placeholder="Mã BN, Tên người khám, Tên bác sĩ, SĐT" value="<?= $info; ?>"></div>
                <div class="col-2">
                </div>
                
            </div>
            <div class="card-body" style="padding-top: 0;">
                <div class="tab-content tab-space">
                    <div class="tab-pane active" id="appList" >
                        <?php if($appointmentFollowList){?>

                            <table id="datatableAppointmentFollowList" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Mã bệnh nhân</th>
                                        <th class="text-center">Tên người khám</th>
                                        <th class="text-center">Số điện thoại</th>
                                        <th class="text-center">Bác sĩ phụ trách</th>
                                        <th class="text-center">Ngày tái khám</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Tác vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach ($appointmentFollowList as $val) {
                                        if($val['index'] > 0){
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $val['index']; ?></td>
                                            <td class="text-center"><?= $val['ma_benh_nhan']; ?></td>
                                            <td><?= $val['ten_benh_nhan']; ?></td>
                                            <td class="text-center"><?= $val['sdt']; ?></td>
                                            <td><?= $val['ten_bac_si']; ?></td>
                                            <td class="text-center"><?= date('d-m-Y',strtotime($val['ngay_tai_kham'])); ?></td>
                                            <td class="text-center"><?= $val['status']; ?></td>
                                            <td class="text-center"> 
                                                <a href="<?= base_url();?>/trang-quan-tri/tu-van-qua-app/chi-tiet-benh-nhan/<?= $val['appointment_code']; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <a href="" data-toggle="modal" data-target="#changeStatus_<?= $val['appointment_follow_id']; ?>"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></a>
                                            </td>

                                            <!-- Modal -->
                                            <div class="modal fade" id="changeStatus_<?= $val['appointment_follow_id']; ?>">
                                                <div class="modal-dialog modal-md">
                                                    <div class="modal-content">
                                                        <div class="modal-header"> <h4 class="modal-title font-weight-bold"><?= 'Thông báo' ?></h4> </div>
                                                        <div class="modal-body">
                                                            <?= 'Bạn chắc chắn muốn thay đổi trạng thái "<span class="font-weight-bold text-success">Đã liên lạc</span>" cho bệnh nhân ?' ?><br>
                                                            <form method="post" enctype="multipart/form-data">
                                                                <div class="form-group">
                                                                    <textarea class="form-control" id="ta_followResult_<?= $val['appointment_follow_id']; ?>" rows="5" placeholder="Kết quả theo dõi (Mặc định: Đã liên lạc)" style="background: #e5e5e5;padding: 10px;"></textarea>
                                                                </div>
                                                                <input type="text" class="form-control hidden" id="appointmentFollowId_<?= $val['appointment_follow_id']; ?>" value="<?= $val['appointment_follow_id']; ?>" >
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <button type="button" class="form-control btn btn-default " data-dismiss="modal">Hủy</button>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <a class="form-control btn btn-primary" onClick="updateStatus(<?= $val['appointment_follow_id']; ?>)">Cập nhật</a>
                                                                    </div>
                                                                </div>
                                                                
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal -->
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
                                        <?php $pagi_path = 'trang-quan-tri/tu-van-qua-app/lich-tai-kham-qua-hen'; ?>
                                        <?php $pager->setPath($pagi_path); ?>
                                        <?= $pager->links(); ?>                  
                                    <?php endif; ?>            
                                </div>           
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>       
         </div>       
     </div> 
 </div>



<script>
    function updateStatus(appointmentFollowId){
        var id      = $('#appointmentFollowId_' + appointmentFollowId).val();
        var content = $('#ta_followResult_' + appointmentFollowId).val();
        if(content == ''){
            content = 'Đã liên lạc';
        }
        
        $.ajax({
            url: '<?= base_url();?>/trang-quan-tri/tu-van-qua-app/cap-nhat-lich-tai-kham',
            type: 'post',
            data: {
                id: id,
                content: content
            },
            success: function(result){
                location.reload();
            }
            
        });
    }
    $(document).ready(function(){
        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var date_from = $('input[name=date_from]').val();
            var date_to = $('input[name=date_to]').val();

            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/tu-van-qua-app/lich-tai-kham-qua-hen?page=1';

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

</script>