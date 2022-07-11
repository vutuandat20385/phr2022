<div class="card p-3">
    <div class="row">
        <div class="col-6 pt-1">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
    </div>
    
    <div class="col-12 p-0 mt-2">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center">STT</th>
                <th class="text-white text-center">Mã bệnh nhân</th>
                <th class="text-white text-center">Họ và tên</th>
                <th class="text-white text-center">Số điện thoại</th>
                <th class="text-white text-center">Hình thức thanh toán</th>
                <th class="text-white text-center">Giá khám</th>
                <th class="text-white text-center">Mã khuyến mại</th>
                <th class="text-white text-center">Đã thanh toán</th>
                <th class="text-white text-center">Ngày thanh toán</th>
                <th class="text-white text-center">Chi tiết</th>
            </tr>
            </thead>
            <tbody>
               <?php if(!empty($allReports)){
                    foreach($allReports as $k => $rp){ ?>
                        <tr>
                            <td><?= $rp['index']; ?></td>
                            <td><?= $rp['ma_benh_nhan']; ?></td>
                            <td><?= $rp['ten_benh_nhan']; ?></td>
                            <td><?= $rp['so_dien_thoai']; ?></td>
                            <td><?= $rp['hinh_thuc_thanh_toan']; ?></td>
                            <td><?php if($rp['gia_kham'] != ''){ echo number_format($rp['gia_kham']); }; ?></td>
                            <td><?= $rp['ma_khuyen_mai']; ?></td>
                            <td><?php if($rp['da_thanh_toan'] != ''){ echo number_format($rp['da_thanh_toan']); }; ?></td>
                            <td><?= $rp['ngay_thanh_toan']; ?></td>
                            <td><a href="trang-quan-tri/bao-cao/lich-su-kham/<?= $rp['person_id']; ?>"><button type="button" class="btn btn-inverse-warning btn-fw">Chi tiết</button></a></td>
                        </tr>
                <?php   }
               }?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6" style="padding: 8px;">
        <?php $pager = \Config\Services::pager(); ?>
        <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
    </div>
    <div class="col-md-6 div-phantrang">
        <?php if ($pager):?>
            <?php $pagi_path = 'trang-quan-tri/bao-cao/lich-su-thanh-toan'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>

