<div class="card p-3">
    <div class="row">
        <div class="col-4 pt-1">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <div class="col-2"><input type="text" name="startDate" class="form-control timePublicNoti miniTextBox" placeholder="Ngày bắt đầu" value="<?= $start; ?>"></div>
        <div class="col-2"><input type="text" name="endDate" class="form-control timePublicNoti miniTextBox" placeholder="Ngày kết thúc" value="<?= $end; ?>"></div>
        <div class="col-3"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="Tìm theo Số điện thoại/Ngân hàng" value="<?= $info; ?>"></div>
        <div class="col-1" style="padding: 0 15px 0 0;"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
    </div>

    <div class="col-12 p-0 mt-2">
        <?php if ($transactions) { ?>
        <table id="tblNoti" class="table table-bordered table-striped d4u-table">
            <thead>
                <tr class="bg-primary text-white">
                    <th class="text-center" >STT</th>
                    <th class="text-center" >SĐT</th>
                    <th class="text-center" >Ngân hàng</th>
                    <th class="text-center" >Loại thẻ</th>
                    <th class="text-center" >Số tiền</th>
                    <th class="text-center" >Ngày thanh toán</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $k => $val) { 
                    if($val['index'] > 0){
                ?>
                    <tr class="" title="<?= $title; ?>">
                        <td class="text-center"><?= $val['index']; ?></td>
                        <td><?= $val['value'] ?></td>
                        <td><?= $val['vnp_bank_code'] ?></td>
                        <td><?= $val['vnp_card_type'] ?></td>
                        <td class="text-right"><?= number_format($val['vnp_amount']); ?></td>
                        <td><?= date('d/m/Y H:i',strtotime($val['vnp_pay_date'])); ?></td>
                        <td class="text-center" style="width: 80px;">
                            
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
                    <?php $pagi_path = 'transactions/vnpay'; ?>
                    <?php $pager->setPath($pagi_path); ?>
                    <?= $pager->links(); ?>                  
                <?php endif; ?>            
            </div>           
        </div>  
        <?php } else { ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> không có dữ liệu!</h5>
            </div>
        <?php } ?>

    </div>
    
</div>

<script>
    $(document).ready(function(){

        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var start = $('input[name=startDate]').val();
            var end = $('input[name=endDate]').val();

            var pagelink = '<?php echo base_url(); ?>' + '/trang-quan-tri/giao-dich/vnpay?page=1';
            if(info != ''){ 
                pagelink += '&info='+info; 
            }

            if(start != ''){ 
                pagelink += '&start='+start; 
            }

            if(end != ''){ 
                pagelink += '&end='+end; 
            }

            window.location.replace(pagelink);
        });  

    });


</script>