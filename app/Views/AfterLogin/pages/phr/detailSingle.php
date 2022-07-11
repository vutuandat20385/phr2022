<div class="card p-3">
    <div class="col-12">
        <h4 class="contentHeader"><?= $panelTitle; ?></h4>
    </div>
    <?php if($phrInfo != false){ ?>
        <?php if(isset($examInfo['dia_chi'])){ $diachi = $examInfo['dia_chi'];}else{ $diachi = '';}?>
        <?php if(isset($examInfo['hospital'])){ $noikham = $examInfo['hospital'];}else{ $noikham = '';}?>
    <div class="card p-3 mb-3">
        <?php if($phrInfo['birthdate'] != '' && $phrInfo['birthdate'] != '0000-00-00'){
            $ngaysinh = date('d-m-Y', strtotime($phrInfo['birthdate']));
        }else{
            $ngaysinh = '';
        }?>
        <div class="row m-0">
            <div class="col-md-3"><?= 'Họ và tên: <span class="text-primary">'.$phrInfo['full_name'].'</span>'; ?></div>
            <div class="col-md-2"><?= 'Giới tính: <span class="text-primary">'.$phrInfo['gender'].'</span>'; ?></div>
            <div class="col-md-2"><?= 'NS: <span class="text-primary">'.$ngaysinh.'</span>'; ?></div>
            <div class="col-md-2"><?= 'SĐT: <span class="text-primary">'.$phrInfo['phone_number'].'</span>'; ?></div>
            <div class="col-md-3"><?= 'Ngày khám: <span class="text-primary">'.date("d-m-Y", strtotime($phrInfo['examination_date'])).'</span>'; ?></div> 
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
    <br>                
    <!-- Thể lực -->
    <?php if(!isset($examInfo['theLuc']['check']) || $examInfo['theLuc']['check'] == 1){ ?>
    <div class="card mb-3">
        <div class="card-header card-header-success card-header-icon">
            <h5 class="font-weight-bold mb-0 text-black">KHÁM THỂ LỰC</h5>
        </div>
        <div class="card-body ">
            <div class="row m-0">
                <?php if(isset($examInfo['theLuc']['chieu_cao']) && $examInfo['theLuc']['chieu_cao'] != ''){ ?>
                    <div class="col-md-3">Chiều cao: <span class="text-primary"><?= $examInfo['theLuc']['chieu_cao']; ?></span> cm</div>
                <?php } ?>
                <?php if(isset($examInfo['theLuc']['can_nang']) && $examInfo['theLuc']['can_nang'] != ''){ ?>
                    <div class="col-md-3">Cân nặng: <span class="text-primary"><?= $examInfo['theLuc']['can_nang']; ?></span> kg</div>
                <?php } ?>
                <?php if(isset($examInfo['theLuc']['huyet_ap']) && $examInfo['theLuc']['huyet_ap'] != ''){ ?>
                    <div class="col-md-3">Huyết áp: <span class="text-primary"><?= $examInfo['theLuc']['huyet_ap']; ?></span> mmHg</div>
                <?php } ?>
                <?php if(isset($examInfo['theLuc']['mach']) && $examInfo['theLuc']['mach'] != ''){ ?>
                    <div class="col-md-3">Mạch: <span class="text-primary"><?= $examInfo['theLuc']['mach']; ?></span> lần/phút</div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>   

    <!-- Khám lâm sàng -->
    <?php if(!isset($examInfo['khamLamSan']['check']) || $examInfo['khamLamSan']['check'] == 1){ ?>
        <?php if($kham_lam_sang){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">KHÁM LÂM SÀNG</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <?php foreach($kham_lam_sang as $k => $kq){
                            echo $kq;
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Chẩn đoán hình ảnh -->
    <?php if(!isset($examInfo['chuanDoanHinhAnh']['check']) || $examInfo['chuanDoanHinhAnh']['check'] == 1){ ?>
        <?php if($chan_doan_hinh_anh){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">CHẨN ĐOÁN HÌNH ẢNH</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                    <?php foreach($chan_doan_hinh_anh as $k => $kq){
                            echo $kq;
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
  
    <!-- Thăm dò chức năng -->
    <?php if(!isset($examInfo['thamDoChucNang']['check']) || $examInfo['thamDoChucNang']['check'] == 1){ ?>
        <?php if($tham_do_chuc_nang){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">THĂM DÒ CHỨC NĂNG</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <?php foreach($tham_do_chuc_nang as $k => $kq){
                            echo $kq;
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Hóa sinh miễn dịch -->
    <?php if(!isset($examInfo['hoaSinhMienDich']['check']) || $examInfo['hoaSinhMienDich']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($hoa_sinh_mien_dich){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">HÓA SINH - MIỄN DỊCH</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($hoa_sinh_mien_dich as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Nước tiểu -->
    <?php if(!isset($examInfo['nuocTieu']['check']) || $examInfo['nuocTieu']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($nuoc_tieu){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">NƯỚC TIỂU</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($nuoc_tieu as $k => $kq){
                                    echo $kq;
                                } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
  
    <!-- Công thức máu -->
    <?php if(!isset($examInfo['congThucMau']['check']) || $examInfo['congThucMau']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($cong_thuc_mau){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">CÔNG THỨC MÁU</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cong_thuc_mau as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
    
    <!-- Đông máu -->
    <?php if(!isset($examInfo['dong_mau']['check']) || $examInfo['dong_mau']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($dongmau){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">ĐÔNG MÁU</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dongmau as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Nhóm máu -->
    <?php if(!isset($examInfo['NhomMau']['check']) || $examInfo['NhomMau']['check'] == 1){ ?>
        <?php if($nhom_mau){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">NHÓM MÁU</h5>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <?php foreach($nhom_mau as $k => $kq){
                            echo $kq;
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Điện di Huyết sắc tố -->
    <?php if(!isset($examInfo['HST']['check']) || $examInfo['HST']['check'] == 1){ ?>
        <?php if($huyet_sac_to){?>
        <?php $i=0;?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">ĐIỆN DI HUYẾT SẮC TỐ</h5>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($huyet_sac_to as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
    
    <!-- Sinh học phân tử -->
    <?php if(!isset($examInfo['sinh_hoc_phan_tu']['check']) || $examInfo['sinh_hoc_phan_tu']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($shpt){?>
            <div class="card ">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia</h5>
                </div>
                <div class="card-body mb-3">
                    <div class="row">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($shpt as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Vi sinh -->
    <?php if(!isset($examInfo['viSinh']['check']) || $examInfo['viSinh']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($vi_sinh){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">VI SINH</h5>
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
                                <?php foreach($vi_sinh as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Soi Tươi Dịch Âm Đạo -->
    <?php if(!isset($examInfo['soiTuoiAmDao']['check']) || $examInfo['soiTuoiAmDao']['check'] == 1){ ?>
        <?php $i=0;?>
        <?php if($soi_tuoi_am_dao){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">SOI TƯƠI DỊCH ÂM ĐẠO</h5>
                </div>
                <div class="card-body ">
                    <div class="row m-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th scope="col" class="col-md-1 font-weight-bold">STT</th>
                                    <th scope="col" class="col-md-6 font-weight-bold">Tên xét nghiệm</th>
                                    <th scope="col" class="col-md-2 font-weight-bold text-center">Kết quả</th>
                                    <th scope="col" class="col-md-3 font-weight-bold text-center" colspan="2">Giá trị bình thường</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($soi_tuoi_am_dao as $k => $kq){
                                    echo $kq;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
   
    <!-- Chỉ số khác -->
    <?php if(!isset($examInfo['ChiSoKhac']['ChiSoKhac_check']) || $examInfo['ChiSoKhac']['ChiSoKhac_check'] == 1){ ?>
        <?php if($chi_so_khac){?>
            <div class="card mb-3">
                <div class="card-header card-header-success card-header-icon">
                    <h5 class="font-weight-bold mb-0 text-black">CHỈ SỐ KHÁC</h5>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <table class="table table-bordered table-hover" style="width: 97%; margin: 0 auto;">
                            <tr>
                                <td>
                                    <?php foreach($chi_so_khac as $k => $kq){
                                        echo $kq;
                                    } ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?> 
    <?php } ?> 
 
    <?php }else{ ?>
        <h4 class="text-center">Không tìm thấy bệnh án, xin vui lòng liên quản trị viên để biết thêm chi tiết.</h4>
    <?php } ?>                     
</div>