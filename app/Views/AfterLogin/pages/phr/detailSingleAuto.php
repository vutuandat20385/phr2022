<div class="row p-1" style="margin-left: 0;">
    <div class="col-12">
        <h4 ><?= $panelTitle; ?></h4>
    </div>
    <?php if($phrInfo != false){ ?>
        <?php if(isset($examInfo['dia_chi'])){ $diachi = $examInfo['dia_chi'];}else{ $diachi = '';}?>
        <?php if(isset($examInfo['hospital'])){ $noikham = $examInfo['hospital'];}else{ $noikham = '';}?>
    <div class="card p-3 mb-3">
        <?php if($examInfo['birth'] != ''){
            $ngaysinh = date("d-m-Y", strtotime($examInfo['birth']));
        }else{
            $ngaysinh = '';
        }?>
        <div class="row m-0">
            <div class="col-md-3"><?= 'Họ và tên: <span class="text-primary">'.$examInfo['name'].'</span>'; ?></div>
            <div class="col-md-2"><?= 'Giới tính: <span class="text-primary">'.$examInfo['gender'].'</span>'; ?></div>
            <div class="col-md-2"><?= 'NS: <span class="text-primary">'.$ngaysinh.'</span>'; ?></div>
            <div class="col-md-2"><?= 'SĐT: <span class="text-primary">'.$examInfo['SDT'].'</span>'; ?></div>
            <div class="col-md-3"><?= 'Ngày khám: <span class="text-primary">'.date("d-m-Y", strtotime($examInfo['date'])).'</span>'; ?></div>
            <div class="col-md-7"><?= 'Địa chỉ: <span class="text-primary">'.$diachi.'</span>'; ?></div>
            <div class="col-md-5"><?= 'Nơi khám: <span class="text-primary">'.$noikham.'</span>'; ?></div>

            <?php if(isset($examInfo['chan_doan']) && $examInfo['chan_doan'] != ''){?>
                <div class="col-md-12"><?= 'Chẩn đoán: <span class="text-primary">'.$examInfo['chan_doan'].'</span>'; ?></div>
            <?php }
            if(isset($examInfo['ketLuan']) && $examInfo['ketLuan'] != ''){?>
                <div class="col-md-12"><?= 'Kết luận: <span class="text-primary">'.$examInfo['ketLuan'].'</span>'; ?></div>
            <?php }
            if(isset($examInfo['ketLuanXN']) && $examInfo['ketLuanXN'] != ''){?>
                <div class="col-md-12"><?= 'Kết luận xét nghiệm: <span class="text-primary">'.$examInfo['ketLuanXN'].'</span>'; ?></div>
            <?php }
            if(isset($examInfo['deNghi']) && $examInfo['deNghi'] != ''){ ?>
                <div class="col-md-12"><?= 'Tư vấn - Đề nghị: <span class="text-primary">'.$examInfo['deNghi'].'</span>'; ?></div>
            <?php }
            if(isset($examInfo['don_thuoc']) && $examInfo['don_thuoc'] != ''){ ?>
                <div class="col-md-12"><?= 'Đơn thuốc: <span class="text-primary">'.$examInfo['don_thuoc'].'</span>'; ?></div>
            <?php } ?>
        </div>

    </div>

    <!-- Thể lực -->
    <?php if($the_luc){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">KHÁM THỂ LỰC</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <div class="col-md-3">Chiều cao: <span class="text-primary"><?= $the_luc[0]['ketqua']; ?></span> cm</div>
                    <div class="col-md-3">Cân nặng: <span class="text-primary"><?= $the_luc[1]['ketqua']; ?></span> kg</div>
                    <div class="col-md-3">Huyết áp: <span class="text-primary"><?= $the_luc[2]['ketqua']; ?></span> mmHg</div>
                    <div class="col-md-3">Mạch: <span class="text-primary"><?= $the_luc[3]['ketqua']; ?></span> lần/phút</div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Khám lâm sàng -->
    <?php if($kham_lam_san){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">KHÁM LÂM SÀNG</h5>
            </div>
            <div class="card-body">
                <div class="row m-0">
                    <?php foreach($kham_lam_san as $k => $kq){
                        echo $kq;
                    } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Chẩn đoán hình ảnh -->
    <?php if($chan_doan_hinh_anh){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">CHẨN ĐOÁN HÌNH ẢNH</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-3 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-1 font-weight-bold" colspan="2">File</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($chan_doan_hinh_anh as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <td><?= $kq['ketqua'] ?></td>
                                <td class="text-center">
                                    <?php if ($kq['file']){ ?>
                                        <a href="https://docs.google.com/viewer?url=<?= $kq['file'] ?>" target="_blank" style="display: block;color: black;width: 100%" title="Xem kết quả">
                                            <i class="material-icons">description</i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Hóa sinh miễn dịch -->
    <?php if(!empty($hoa_sinh)){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">HÓA SINH - MIỄN DỊCH</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($hoa_sinh as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Nước tiểu -->
    <?php if(!empty($nuoc_tieu)){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">NƯỚC TIỂU</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($nuoc_tieu as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Công thức máu -->
    <?php if(!empty($cong_thuc_mau)){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">CÔNG THỨC MÁU</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($cong_thuc_mau as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Đông máu -->
    <?php if($dongmau){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">ĐÔNG MÁU</h5>
            </div>
            <div class="card-body ">
                <div class="row m-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($dongmau as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Nhóm máu -->
    <?php if($nhom_mau){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">NHÓM MÁU</h5>
            </div>
            <div class="card-body ">
                <div class="row">
                    <?php foreach($nhom_mau as $k => $kq){ ?>
                        <span class="col-md-4"><?= $kq['tenDV'] ?>:</span>
                        <span class="col-md-2 font-weight-bold"><?= $kq['ketqua'] ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Điện di Huyết sắc tố -->
    <?php if($hst){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">ĐIỆN DI HUYẾT SẮC TỐ</h5>
            </div>
            <div class="card-body ">
                <div class="row">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($hst as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Sinh học phân tử -->
    <?php if($sinh_hoc_phan_tu){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia</h5>
            </div>
            <div class="card-body ">
                <div class="row">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($sinh_hoc_phan_tu as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Vi sinh -->
    <?php if(!empty($vi_sinh)){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">VI SINH</h5>
            </div>
            <div class="card-body ">
                <div class="row">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($vi_sinh as $k => $kq){ ?>
                            <tr>
                                <td class="text-center"><?= $k+1 ?></td>
                                <td><?= $kq['tenDV'] ?></td>
                                <?php ?>
                                <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                    <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                <?php } else { ?>
                                    <td class="text-center"><?= $kq['ketqua'] ?></td>
                                <?php } ?>
                                <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                    <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                    <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                <?php }?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Chỉ số khác -->
    <?php if($khac){ ?>
        <div class="card mb-3">
            <div class="card-header card-header-success card-header-icon">
                <h5 class="font-weight-bold">CHỈ SỐ KHÁC</h5>
            </div>
            <div class="card-body ">
                <div class="row">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                            <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                            <th scope="col" class="col-md-2 font-weight-bold">Kết quả</th>
                            <th scope="col" class="col-md-3 font-weight-bold" colspan="2">Giá trị bình thường</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($khac as $k => $kq){ ?>
                            <tr>
                                    <td class="text-center"><?= $k+1 ?></td>
                                    <td><?= $kq['tenDV'] ?></td>
                                    <?php ?>
                                    <?php if ($kq['ketqua'] > $kq['chisochuan']['max'] || $kq['ketqua'] < $kq['chisochuan']['min']){ ?>
                                        <td class="text-center text-danger"><?= $kq['ketqua'] ?></td>
                                    <?php } else { ?>
                                        <td class="text-center"><?= $kq['ketqua'] ?></td>
                                    <?php } ?>
                                    <?php if ($kq['chisochuan'] && $kq['chisochuan']['text'] != null && $kq['chisochuan']['unit']){ ?>
                                        <td class="text-center"><?= $kq['chisochuan']['text'] ?></td>
                                        <td class="text-center"><?= $kq['chisochuan']['unit'] ?></td>
                                    <?php } else { ?>
                                        <td></td>
                                        <td></td>
                                    <?php }?>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
     
    <?php }else{ ?>
        <h4 class="text-center">Không tìm thấy bệnh án, xin vui lòng liên quản trị viên để biết thêm chi tiết.</h4>
    <?php } ?>
</div>