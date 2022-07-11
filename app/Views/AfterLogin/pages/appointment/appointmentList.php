<div class="card p-3">
    <div class="row m-0">
        <div class="col-2 pt-2 mb-2">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <div class="col-2"><input type="text" id="date_from" name="date_from" class="form-control u_datepicker search_date" placeholder="Từ ngày" value="<?= $date_from; ?>"></div>
        <div class="col-2"><input type="text" id="date_to" name="date_to" class="form-control u_datepicker search_date" placeholder="Đến ngày" value="<?= $date_to; ?>"></div>
        <div class="col-3"><input type="text" name="search_info" class="form-control" placeholder="Mã, Tên người khám" value="<?= $info; ?>"></div>
        <div class="col-2">
            <select class="form-control" name="" id="sltCode">
                <option class="p-2" value="">Chọn Mã giới thiệu</option>
                <?php foreach($referralCode as $k => $rc){ ?>
                    <?php
                        if($rc['referral_code'] == $rCode){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                    ?>
                    <option class="p-2" value="<?= $rc['referral_code']; ?>" <?= $selected; ?> ><?= $rc['referral_code']; ?></option>
                <?php }?>
                
            </select>
        </div>
        <div class="col-1"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
            <?php if($appointmentList){?>
                <table id="datatableAppointmentList" class="table table-hover table-bordered table-striped d4u-table">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="text-center">STT</th>
                            <th>Mã</th>
                            <th>Tên người khám</th>
                            <th>Mã giới thiệu</th>
                            <th>Bác sĩ phụ trách</th>
                            <th>Giờ tư vấn</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($appointmentList as $val) {
                            if($val['index'] > 0){
                            ?>
                            <tr>
                                <td><?= $val['index']; ?></td>
                                <td><?= $val['appointment_code']; ?></td>
                                <td><?= $val['patient_name']; ?></td>
                                <td class="text-center"><?= $val['code']; ?></td>
                                <td><?= $val['provider_name']; ?></td>
                                <td>
                                    <?php
                                        $date = date('d/m/Y', strtotime($val['start_date']));
                                        $time_start = date('H:i', strtotime($val['start_date']));
                                        $time_end = date('H:i', strtotime($val['end_date']));
                                        echo $date.' ( '.$time_start.' - '.$time_end.' )';
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
                                <td class="text-center">
                                    <a class="text-success" href="<?= base_url('/trang-quan-tri/tu-van-qua-app/chi-tiet-benh-nhan'); ?>/<?= $val['appointment_code']; ?>"><i class="fa fa-bars" aria-hidden="true"></i></a>
                                </td>
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
                            <?php $pagi_path = 'trang-quan-tri/tu-van-qua-app/danh-sach-tu-van'; ?>
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

<script>
    $(document).ready(function(){
        $("#sltCode").select2();

        
        $('.search_date').datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var date_from = $('input[name=date_from]').val();
            var date_to = $('input[name=date_to]').val();
            var rCode = $('#sltCode').val();

            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/tu-van-qua-app/danh-sach-tu-van?page=1';

            if(info != ''){ link += '&info='+info; }
            if(date_from != ''){ link += '&date_from='+date_from; }
            if(date_to != ''){ link += '&date_to='+date_to; }
            if(rCode !== ''){ link += '&rCode=' + rCode; }

            window.location.replace(link);

        });
        
    });

</script>