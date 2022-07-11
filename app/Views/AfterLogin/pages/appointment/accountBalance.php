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
                <div class="col-6"></div>
                <div class="col-4"><input type="text" name="search_info" class="form-control" placeholder="Mã BN, Họ và tên, Số điện thoại" value="<?= $info; ?>"></div>
                <div class="col-2"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
            </div>
            <div class="card-body" style="padding-top: 0;" id="balanceList">
                <div class="tab-content tab-space">
                    <?php if($accountBalance){?>
                        <table id="accountBalanceList" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th class="text-center">STT</th>
                                    <th>Mã</th>
                                    <th>Họ và tên</th>
                                    <th>Số điện thoại</th>
                                    <th><a class="text-white" id="account_sort">Số dư</a></th>
                                    <!-- <th></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($accountBalance as $val) {
                                    if($val['index'] > 0){
                                    ?>
                                    <tr>
                                        <td><?= $val['index']; ?></td>
                                        <td><?= $val['identifier']; ?></td>
                                        <td><?= $val['given_name']; ?></td>
                                        <td><?= $val['value']; ?></td>
                                        <td class="text-center"><?= number_format($val['balance']); ?></td>

                                        <!-- <td>
                                            <a class="text-success" href=""> <i class="material-icons">storage</i></a>
                                        </td> -->
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
                                    <?php $pagi_path = 'trang-quan-tri/tu-van-qua-app/so-du-tai-khoan'; ?>
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
 </div>

<script>
    $(document).ready(function(){

        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();

            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/tu-van-qua-app/so-du-tai-khoan?page=1';

            if(info != ''){ link += '&info='+info; }

            window.location.replace(link);

        });

        $('#account_sort').click(function(){
            var info = $('input[name=search_info]').val();

            var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/tu-van-qua-app/so-du-tai-khoan-sort?page=1';

            if(info != ''){ link += '&info='+info; }

            window.location.replace(link);
        });
        
    });

</script>