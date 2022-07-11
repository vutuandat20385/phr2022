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
<div class="row m-0">
    <div class="col-md-12">
        <h3 class="font-weight-bold"><? $panelTitle ?></h3>
        <div class="card">
            <div class="card-header card-header-text card-header-warning">
                <div class="card-text">
                    <h4 class="card-title">Thông tin cá nhân</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">Họ và tên: <span class="font-weight-bold"><?= $patientInfo['given_name']; ?></span></div>
                    <div class="col-md-3">Ngày sinh: <span class="font-weight-bold"><?= $patientInfo['birthdate1']; ?></span></div>
                    <div class="col-md-3">SĐT: <span class="font-weight-bold"><?= $patientInfo['value']; ?></span></div>
                    <div class="col-md-6">Địa chỉ: <span class="font-weight-bold"><?= $patientInfo['address1']; ?></span></div>
                    <div class="col-md-3">Email: <span class="font-weight-bold"><?= $patientInfo['email']; ?></span></div>
                    <div class="col-md-3">Giới tính: <span class="font-weight-bold"><?= $patientInfo['gender1']; ?></span></div>
                </div>
            </div>
        </div>
        <div class="card mh-600">
            <div class="card-header card-header-text card-header-warning">
                <div class="card-text">
                    <h4 class="card-title">Thông tin khám bệnh</h4>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills nav-pills-warning" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#lidokham" role="tablist"> Lí do khám </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#lichsukham" role="tablist"> Lịch sử khám </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#hoibenh" role="tablist"> Hỏi bệnh </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#tiensubenh" role="tablist"> Tiền sử bệnh </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#khambenh" role="tablist"> Khám bệnh </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#chandoan" role="tablist"> Chẩn đoán </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#chidinhcls" role="tablist"> Chỉ định CLS </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#donthuoc" role="tablist"> Đơn thuốc </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#loidan" role="tablist"> Lời dặn </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#theodoi" role="tablist"> Theo dõi </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pdf" role="tablist"> PDF File </a> </li>
                </ul>
                <div class="tab-content tab-space">
                    <div class="tab-pane active" id="lidokham">
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Lí do khám: </span>
                            <?= $appointmentInfo['reason']; ?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Mô tả chi tiết: </span>
                            <?= $appointmentInfo['reason_detail'];?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Câu hỏi cho bác sĩ: </span>
                            <?php foreach($cau_hoi as $k => $ch){ ?>
                                <div class="col-md-12">
                                    <span class="font-weight-bold">Câu hỏi <?= $k+1; ?>: </span>
                                    <span class=""><?= $ch;?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($ket_qua_cu){ ?>
                            <div class="div-subInfo">
                                <span class="font-weight-bold">Kết quả khám trước đây: </span>
                                <div class="row m-0">
                                    <?php foreach($ket_qua_cu as $k => $kqc){ ?>
                                        <div class="div-img-old-result col-md-2" id="img-old-result">
                                            <img class="imgStyle border" src="http://qa-admin.doctor4u.vn/openmrs/ws/rest/v1/obs/<?= $kqc; ?>/value?t=<?= round(microtime(true) * 1000) ?>">
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="lichsukham">
                        <?php if($lich_su_kham){ ?>
                            <?php foreach($lich_su_kham as $k => $lsk){ ?>
                                <?php if($lsk['chandoan_sobo'] && $lsk['chandoan_xacdinh']){ ?>
                                    <div class="div-subInfo">
                                        <p class="font-weight-bold d-flex">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp
                                            <?= date('d', strtotime($lsk['date_changed'])) ?> tháng <?= date('m', strtotime($lsk['date_changed'])) ?>, <?= date('Y', strtotime($lsk['date_changed'])) ?>
                                            <?php if($lsk['date']==''){?>
                                                (Hôm nay)
                                            <?php }else{ ?>
                                                (<?= $lsk['date']; ?> trước)
                                            <?php } ?>
                                        </p>
                                        <span class="font-weight-bold">Chẩn đoán sơ bộ:</span><br>
                                        <?php foreach ($lsk['chandoan_sobo'] as $k => $sb){ ?>
                                            <div class="col-md-12">
                                                <?= $sb ?>
                                            </div>
                                        <?php } ?>
                                        <span class="font-weight-bold">Chẩn đoán xác định:</span><br>
                                        <?php foreach ($lsk['chandoan_xacdinh'] as $k => $xd){ ?>
                                            <div class="col-md-12">
                                                <?= $xd ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else if($lsk['chandoan_sobo']) {?>
                                    <div class="div-subInfo">
                                        <p class="font-weight-bold d-flex">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp
                                            <?= date('d', strtotime($lsk['date_changed'])) ?> tháng <?= date('m', strtotime($lsk['date_changed'])) ?>, <?= date('Y', strtotime($lsk['date_changed'])) ?>
                                            <?php if($lsk['date']==''){?>
                                                (Hôm nay)
                                            <?php }else{ ?>
                                                (<?= $lsk['date']; ?> trước)
                                            <?php } ?>
                                        </p>
                                        <span class="font-weight-bold">Chẩn đoán sơ bộ:</span><br>
                                        <?php foreach ($lsk['chandoan_sobo'] as $k => $sb){ ?>
                                            <div class="col-md-12">
                                                <?= $sb ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else if($lsk['chandoan_xacdinh']) { ?>
                                    <div class="div-subInfo">
                                        <p class="font-weight-bold d-flex">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp
                                            <?= date('d', strtotime($lsk['date_changed'])) ?> tháng <?= date('m', strtotime($lsk['date_changed'])) ?>, <?= date('Y', strtotime($lsk['date_changed'])) ?>
                                            <?php if($lsk['date']==''){?>
                                                (Hôm nay)
                                            <?php }else{ ?>
                                                (<?= $lsk['date']; ?> trước)
                                            <?php } ?>
                                        </p>
                                        <span class="font-weight-bold">Chẩn đoán xác định:</span><br>
                                        <?php foreach ($lsk['chandoan_xacdinh'] as $k => $xd){ ?>
                                            <div class="col-md-12">
                                                <?= $xd ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } else {?>
                            <div class="div-subInfo">
                                <span>Chưa có thông tin</span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="hoibenh">
                        <?php if ($trieu_chung){ ?>
                            <?php foreach ($trieu_chung as $k => $tc){ ?>
                                <div class="div-subInfo">
                                    <span class="font-weight-bold">Triệu chứng <?= $k+1 ?>: </span>
                                    <?= $tc?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="div-subInfo">
                                <span>Chưa có thông tin</span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="tiensubenh">
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Dị ứng: </span>
                            <?php
                                if($di_ung){
                                    foreach($di_ung as $k => $du){ ?>
                                        <div class="col-md-12">
                                            <?php if(isset($du['non_coded_allergen'])){ echo $du['non_coded_allergen']; }; ?>
                                        </div>
                            <?php   }
                                } else {
                            ?>
                                <div class="div-subInfo">
                                    <span>Chưa có thông tin</span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Bệnh mãn tính: </span>
                            <?php
                                if($benh_man_tinh){
                                    foreach($benh_man_tinh as $k => $bmt){ ?>
                                        <div class="col-md-12">
                                            <?php if(isset($bmt['condition_non_coded'])){ echo $bmt['condition_non_coded']; }; ?>
                                        </div>
                            <?php   }
                                } else {
                            ?>
                            <span>Chưa có thông tin</span>
                            <?php } ?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Thói quen sinh hoạt (Rượu bia, thuốc lá, chất kích thích): </span>
                            <br><?= $ruou_bia; ?><br><?= $thuoc_la; ?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Tiền sử gia đình: </span>
                            <?= $tien_su_gia_dinh; ?>
                        </div>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Thuốc đang sử dụng: </span>
                            <?= $thuoc_dang_su_dung; ?>
                        </div>
                    </div>
                    <div class="tab-pane" id="khambenh">
                        <div class="div-khamtheluc">
                            <div class="div-subInfo">
                                <span class="font-weight-bold">Khám Thể Lực </span>
                                <div class="row m-0">
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                            <p class="font-weight-bold mb-0">Mạch: </p>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $mach; ?></h2>
                                            <p class="font-weight-bold text-right mb-0">lần/phút</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                            <p class="font-weight-bold mb-0">Nhiệt độ: </p>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $nhiet_do; ?></h2>
                                            <p class="font-weight-bold text-right mb-0">ºC</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                         <p class="font-weight-bold mb-0">Huyết áp: </p>
                                         <?php if($ha_max != '' || $ha_min != ''){ ?>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $ha_max.'/'.$ha_min; ?></h2>
                                         <?php }?>
                                         <p class="font-weight-bold text-right mb-0">mmHg</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                            <p class="font-weight-bold mb-0">Nhịp thở: </p>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $nhip_tho; ?></h2>
                                            <p class="font-weight-bold text-right mb-0">lần/phút</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                            <p class="font-weight-bold mb-0">Cân nặng: </p>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $can_nang; ?></h2>
                                            <p class="font-weight-bold text-right mb-0">kg</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-khamtheluc">
                                        <div class="cover-ktl">
                                            <p class="font-weight-bold mb-0">Chiều cao: </p>
                                            <h2 class="font-weight-bold text-center mb-0"><?= $chieu_cao; ?></h2>
                                            <p class="font-weight-bold text-right mb-0">cm</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-khamlamsang">
                            <div class="div-subInfo">
                                <?php if ($kham_lam_sang){ ?>
                                    <?php foreach($kham_lam_sang as $k => $kls){ ?>
                                        <div class="col-md-12">
                                            <span class="font-weight-bold">Khám Lâm Sàng <?= $k+1; ?>: </span>
                                            <span class=""><?= $kls;?></span>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                <span>Chưa có thông tin</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="chandoan">
                        <?php if(isset($chan_doan['chandoan_sobo'])){ ?>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Chẩn đoán sơ bộ: </span>
                            <?php foreach ($chan_doan['chandoan_sobo'] as $k => $sb){ ?>
                                <div class="col-md-12">
                                    <?= $sb ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if(isset($chan_doan['chandoan_xacdinh'])){ ?>
                        <div class="div-subInfo">
                            <span class="font-weight-bold">Chẩn đoán xác định: </span>
                            <?php foreach ($chan_doan['chandoan_xacdinh'] as $k => $xd){ ?>
                                <div class="col-md-12">
                                    <?= $xd ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if (empty($chan_doan['chandoan_sobo']) && empty($chan_doan['chandoan_xacdinh'])){ ?>
                            <div class="div-subInfo">
                                <span>Chưa có thông tin</span>
                            </div>
                        <?php } ?>
                    </div>
<!--                    <div class="tab-pane" id="chidinhcls">-->
<!--                        --><?php //if ($can_lam_sang){ ?>
<!--                            --><?php //foreach($can_lam_sang as $k => $cls){ ?>
<!--                                <div class="div-subInfo">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <p class="font-weight-bold">Chỉ định --><?//= $k+1 ?><!-- (--><?//= $cls['group'] ?><!--)</p>-->
<!--                                        --><?php //if($cls['member']){ ?>
<!--                                            --><?php //foreach($cls['member'] as $j => $m){?>
<!--                                                <p> - --><?php //echo $m['display']; ?><!--</p>-->
<!--                                            --><?php //} ?>
<!--                                        --><?php //} ?>
<!--                                    </div>-->
<!--                                </div>-->
<!--                            --><?php //} ?>
<!--                        --><?php //} else { ?>
<!--                            <div class="div-subInfo">-->
<!--                                <span>Chưa có thông tin</span>-->
<!--                            </div>-->
<!--                        --><?php //} ?>
<!--                    </div>-->
<!--                    <div class="tab-pane" id="donthuoc">-->
<!--                        --><?php //if ($don_thuoc){ ?>
<!--                            --><?php //foreach($don_thuoc as $k => $thuoc){ ?>
<!--                                <div class="div-subInfo">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <span class="font-weight-bold">Đơn thuốc --><?//= $k+1 ?><!--</span><br>-->
<!--                                        --><?php //if($thuoc['concept_name']){ ?>
<!--                                            <span>--><?//= $thuoc['concept_name'] ?><!--</span>-->
<!--                                        --><?php //} ?>
<!--                                        --><?php //if($thuoc['name']){ ?>
<!--                                            <span>--><?php //echo ' ('.$thuoc['name'].')' ?><!--</span>-->
<!--                                        --><?php //} ?>
<!--                                        <span class="float-right">-->
<!--                                            x--><?//= $thuoc['quantity'] ?><!-- --><?//= $thuoc['unit'] ?>
<!--                                        </span><br>-->
<!--                                        <span>-->
<!--                                            --><?//= $thuoc['instruction'] ?>
<!--                                            --><?php //if($thuoc['dosage']){ ?>
<!--                                                <span>-->
<!--                                                    --><?php
//                                                        echo ' (';
//                                                        foreach($thuoc['dosage'] as $dose){
//                                                    ?>
<!--                                                        --><?php
//                                                            switch ($dose):
//                                                                case 'MORNING':     echo 'sáng, ';    break;
//                                                                case 'NOON':        echo 'trưa, ';    break;
//                                                                case 'AFTERNOON':   echo 'chiều, ';   break;
//                                                                case 'EVENING':     echo 'tối, ';     break;
//                                                            endswitch;
//                                                        ?>
<!--                                                    --><?php //} ?>
<!--                                                </span>-->
<!--                                            --><?php //} ?>
<!--                                            --><?php //if($thuoc['status']){ ?>
<!--                                            <span>-->
<!--                                                --><?php
//                                                    switch ($thuoc['status']):
//                                                        case 'BEFORE':  echo 'trước ăn';    break;
//                                                        case 'AFTER':   echo 'sau ăn';      break;
//                                                    endswitch;
//                                                ?>
<!--                                            </span>-->
<!--                                            --><?php
//                                                } echo ')';
//                                            ?>
<!--                                        </span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            --><?php //} ?>
<!--                        --><?php //} else { ?>
<!--                            <div class="div-subInfo">-->
<!--                                <span>Chưa có thông tin</span>-->
<!--                            </div>-->
<!--                        --><?php //} ?>
<!--                    </div>-->
                    <div class="tab-pane" id="loidan">
                        <?php if ($loi_dan){ ?>
                            <?php foreach($loi_dan as $k => $ld){ ?>
                                <div class="div-subInfo">
                                    <span class="font-weight-bold">Hướng dẫn <?= $k+1; ?>: </span>
                                    <?= $ld ?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="div-subInfo">
                                <span>Chưa có thông tin</span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="theodoi">
                        <?php if ($theo_doi){ ?>
                            <?php foreach($theo_doi as $k => $td){ ?>
                                <div class="div-subInfo">
                                    <div class="row">
                                        <div class="col-md-8"><span class="font-weight-bold">Theo dõi <?= $k+1; ?>: </span></div>
                                        <div class="col-md-4 text-right font-weight-bold">
                                            <?php
                                                $td_date = date('w', strtotime($td['follow_date']));
                                                switch ($td_date):
                                                    case 0: echo 'Chủ Nhật, ';    break;
                                                    case 1: echo 'Thứ Hai, ';     break;
                                                    case 2: echo 'Thứ Ba, ';      break;
                                                    case 3: echo 'Thứ Tư, ';      break;
                                                    case 4: echo 'Thứ Năm, ';     break;
                                                    case 5: echo 'Thứ Sáu, ';     break;
                                                    case 6: echo 'Thứ Bảy, ';     break;
                                                endswitch;
                                                echo date('d/m/y', strtotime($td['follow_date']));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= $td['follow_description'] ?>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span class="font-weight-bold">Kết quả theo dõi <?= $k+1; ?>:</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($td['follow_result']){ ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?= $td['follow_description'] ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        Chưa có kết quả
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="div-subInfo">
                                <span>Chưa có thông tin</span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane" id="pdf">
                        <div class="div-subInfo">
                            <a href="<?php echo base_url().'/trang-quan-tri/pdf/don-thuoc/'.$patientInfo['appointment_code'];?>" class="btn btn-primary">Đơn thuốc</a>
<!--                            <button class="btn btn-primary" id="btnDownloadPhieuKhamBenh">Phiếu Khám bệnh</button>-->
<!--                            <button class="btn btn-primary" id="btnDownloadPhieuXetNghiem">Phiếu Xét nghiệm</button>-->
                        </div>

                    </div>
                </div>
            </div>
                
            <!-- </div> -->
        </div>
    </div>
</div>
<script>
    $('#btnDownloadDonThuoc').click(function(){
        var appointment_code = '<?= $patientInfo['appointment_code']; ?>';
        console.log(appointment_id);
        $.ajax({
            url: '<?= base_url();?>/trang-quan-tri/pdf/don-thuoc/'+appointment_code,
            type: 'get',
            data: {
            },
            success: function(result){
                // location.reload();
            }
        });
    });

    //load thêm lịch sử khám
    $('#lichsukham').simpleLoadMore({
        item: '.div-subInfo',
        easing: 'ease-in-out',
        easingDuration: '600',
        btnHTML: '<a href="#" class="load-more__btn font-weight-bold">Xem thêm</a>'
    });

    ezoom.onInit($('#img-old-result img'));
</script>

