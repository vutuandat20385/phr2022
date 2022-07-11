<div class="card p-3 mh-600">
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
                <th class="text-white text-center">Ngày khám</th>
                <th class="text-white text-center">Mã bệnh nhân</th>
                <th class="text-white text-center">Tên bệnh nhân</th>
                <th class="text-white text-center">Mã cuộc khám</th>
                <th class="text-white text-center">Bác sĩ</th>
                <th class="text-white text-center">Chuyên khoa</th>
                <th class="text-white text-center">Thanh toán</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($appointmentHistory)){
                        foreach($appointmentHistory as $k => $rp){ ?>
                            <tr>
                                <td><?= $k+1; ?></td>
                                <td><?= $rp['ngay_kham']; ?></td>
                                <td><?= $rp['ma_benh_nhan']; ?></td>
                                <td><?= $rp['ten_benh_nhan']; ?></td>
                                <td><?= $rp['ma_cuoc_kham']; ?></td>
                                <td><?= $rp['ten_bac_si']; ?></td>
                                <td><?= $rp['']; ?></td>
                                <td><?= $rp['']; ?></td>
                            </tr>
                <?php   }
                    }else{ ?>
                            <tr>
                                <td colspan="8">Chưa có lượt khám</td>
                            </tr>
                <?php    }
                ?>
            </tbody>
        </table>
    </div>
 
    
</div>

