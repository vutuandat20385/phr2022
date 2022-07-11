<div class="card p-3">
    <div class="row">
        <div class="col-6 pt-1">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <!-- <div class="col-2 offset-2"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="Họ tên, SĐT" value="<?= $info; ?>"></div>
        <div class="col-1"><button class="btn btn-outline-success btn-fw" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
        <div class="col-1"> <a href="" data-toggle="modal" data-target="#modal-import" class="btn btn-outline-warning btn-fw" style="width: 100%;">Import</a></div> -->
    </div>
    
    <div class="col-12 p-0 mt-2">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center">STT</th>
                <th class="text-white text-center">Ngày khám</th>
                <th class="text-white text-center">Mã bệnh nhân</th>
                <th class="text-white text-center">Tên bệnh nhân</th>
                <th class="text-white text-center">Số điện thoại</th>
                <th class="text-white text-center">Mã cuộc khám</th>
                <th class="text-white text-center">Bác sĩ</th>
                <th class="text-white text-center">Chuyên khoa</th>
                <th class="text-white text-center">Thanh toán</th>
                <th class="text-white text-center">Ghi chú</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($allReports)){
                        foreach($allReports as $k => $rp){ ?>
                            <tr>
                                <td><?= $rp['index']; ?></td>
                                <td><?= $rp['ngay_kham']; ?></td>
                                <td><?= $rp['ma_benh_nhan']; ?></td>
                                <td><?= $rp['ten_benh_nhan']; ?></td>
                                <td><?= $rp['value']; ?></td>
                                <td><?= $rp['ma_cuoc_kham']; ?></td>
                                <td><?= $rp['ten_bac_si']; ?></td>
                                <td><?= $rp['']; ?></td>
                                <td><?= $rp['']; ?></td>
                                <td><?= $rp['']; ?></td>
                            </tr>
                <?php   }
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6" style="padding: 8px;">
        <?php $pager = \Config\Services::pager(); ?>
        <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
    </div>
    <div class="col-md-6 div-phantrang">
        <?php if ($pager):?>
            <?php $pagi_path = 'trang-quan-tri/bao-cao/kham-app-online'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>

