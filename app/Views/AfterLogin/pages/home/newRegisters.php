<div class="card p-3">
    <div class="row m-0">
        <div class="col-6 pt-2">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
    </div>
    
    <div class="col-12 mt-1">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style="max-width: 100% !important; width:100% !important"> 
            <thead>
            <tr class="bg-primary">
                <th class="text-white text-center" style="width: 50px !important;">#</th>
                <th class="text-white text-center" style="width: 15%;">Số điện thoại</th>
                <th class="text-white text-center" style="width: 30%;">Họ và tên</th>
                <th class="text-white text-center" style="width: 10%;">Ngày sinh</th>
                <th class="text-white text-center" style="width: 10%;">Giới tính</th>
                <th class="text-white text-center" style="width: 15%;">Mã giới thiệu</th>
                <th class="text-white text-center" style="width: 15%;">Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
                <?php if($posts){ ?>
                    <?php foreach($posts as $user){ ?>
                        <tr>
                            <td> <?= $user['index']; ?></td>
                            <td> <?= $user['value']; ?></td>
                            <td> <?= $user['given_name']; ?></td>
                            <td> <?= date('d-m-Y', strtotime($user['birthdate'])); ?></td>
                            <td> <?php 
                                    if($user['gender'] === 'M' || $user['gender'] === 'MALE' || $user['gender'] === 'Nam'){
                                        echo 'Nam';
                                    }else if($user['gender'] === 'F' || $user['gender'] === 'FEMALE' || $user['gender'] === 'Nữ'){
                                        echo 'Nữ';
                                    }else{
                                        echo '';
                                    }
                            ; ?></td>
                            <td class="text-center"> <?= $user['code']; ?></td>
                            <td> <?= date('d-m-Y H:i:s', strtotime($user['date_created'])); ?></td>
                        
                        </tr>
                        
                    <?php } ?>
                <?php } ?>
                
            </tbody>
        </table>
    </div>
    <div class="col-md-6" style="padding: 8px;">
        <?php $pager = \Config\Services::pager(); ?>
        <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
    </div>
    <div class="col-md-6 div-phantrang">
        <?php if ($pager):?>
            <?php $pagi_path = 'trang-quan-tri/tai-khoan-moi-dang-ky'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>
