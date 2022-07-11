
<div class="card p-3">
    <div class="row m-0">
        <div class="col-3 pt-2">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <div class="col-2" style="margin-top: 3px;"><input type="text" name="date_from" id="" class="form-control u_datepicker date_from miniTextBox" placeholder="Từ ngày" value="<?= $date_from; ?>"></div>
        <div class="col-2"style="margin-top: 3px;"><input type="text" name="date_to" class="form-control u_datepicker date_to miniTextBox" placeholder="Đến ngày" value="<?= $date_to; ?>"></div>
        <div class="col-3"style="margin-top: 3px;"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="Họ tên, SĐT" value="<?= $info; ?>"></div>
        <div class="col-1"><button class="btn btn-success" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
        <div class="col-1"><button class="btn btn-primary" style="width: 100%;" id="btn_capnhat">Cập nhật</button></div>
    </div>
    
    <div class="col-12">
        <?php if ($history) { ?>
            <?php $pager = \Config\Services::pager(); ?>
            <br>
        <table id="datatable" class="table table-bordered table-striped d4u-table">
            <thead>
                <tr class="bg-primary text-white">
                    <td class="w30"><input class="w30" type="checkbox" value="" id="checkAll"></td>
                    <td class="text-center w30">STT</td>
                    <td>Họ tên</td>
                    <td>SĐT</td>
                    <td class="text-center">G.Tính</td>
                    <td class="text-center" style="width: 120px;">Ngày sinh</td>
                    <td class="text-center">Kết quả chẩn đoán</td>
                    <td class="text-center" style="width: 120px;">Ngày khám</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $k => $val) { 
                    if($val['index'] > 0){
                ?>
                    <tr>
                        <td class="text-center w50">
                            <input class="w30" type="checkbox" value="<?= $val['id_treatment'] ?>" id="<?= $val['iddichvu_data'] ?>" name='treatment[]'>
                        </td>
                        <td class="text-center w30"><?= $val['index']; ?></td>
                        <td><?= $val['fullName'] ?></td>
                        <td ><?= $val['phoneNumber'] ?></td>
                        <td class="text-center"><?= $val['gender'] ?></td>
                        <td class="text-center" style="width: 100px;"><?= date('d-m-Y',strtotime($val['birthdate'])); ?></td>
                        <td class="text-center"><?= $val['conclusion'] ?></td>
                        <td class="text-center" style="width: 100px;"><?= date('d-m-Y',strtotime($val['exam_date'])); ?></td>
                        <td class="text-center"><a class="btn btn-warning text-white" style="line-height: 1; padding: 10px 30px;"  href="<?= base_url('trang-quan-tri/benh-an/d4u-khach-le/chi-tiet-benh-an');?>/<?= $val['annual_checkup_id'] ?>">Chi tiết</a></td>
                    </tr>
                    <!-- /.modal -->
                    <div class="modal fade" id="modal-lg<?= $val['annual_checkup_id'] ?>">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header"> <h4 class="modal-title">Thông tin bệnh án</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    
                                </div>
                            </div>
                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        
                    </div>
                    
                <!-- </div> -->
            
                <?php } 
                }
                ?>

            </tbody>

        </table>
        <?php $pager = \Config\Services::pager(); ?>
        <div class="row m-0">
            <div class="col-md-6">
                <?php echo 'Đang xem trang: '.$currentPage.'/'.$pager->getPageCount(); ?>
            </div>
            <div class="col-md-6 div-phantrang">
                <?php if ($pager):?>
                    <?php $pagi_path = '/trang-quan-tri/ehc/'.$link; ?>
                    <?php $pager->setPath($pagi_path); ?>
                    <?= $pager->links(); ?>                  
                <?php endif; ?>            
            </div>           
        </div>  
        <?php } ?>      
    </div>

</div>
<!-- Modal import PHR -->
<div class="modal fade" id="modal-import">
    <?= $form_importModal; ?>
</div>
<script>
    $(document).ready(function(){
        

        $('#btn_timkiem').click(function(){
            var info = $('input[name=search_info]').val();
            var link = '<?php echo base_url('/trang-quan-tri/ehc'); ?>' + '<?= $link; ?>?page=1';

            if(info != ''){ link += '&info='+info; }

            window.location.replace(link);

        });

        $('#btn_capnhat').click(function(){
           
            var treatmentList = '';
            // Lấy danh sách checkbox
            var checkboxes = document.getElementsByName('treatment[]');
            for (var i = 0; i < checkboxes.length; i++){
                if(checkboxes[i].checked == true){
                    var string = checkboxes[i].value;
                    treatmentList += string + ',';
                };
            }
            if(treatmentList == ''){
                // window.location.href = "<?php echo base_url(); ?>/ehc/cap-nhat-thong-tin";
                $.ajax({
                    url : "<?= base_url('trang-quan-tri/ehc/cap-nhat-thong-tin'); ?>",
                    type : "get",
                    data : {
                    },
                    success : function (result){
                        location.reload();
                    }
                });
                
            }else{
                // alert(treatmentList);
                $.ajax({
                    url : "<?php echo base_url(); ?>/trang-quan-tri/ehc/cap-nhat-lai",
                    type : "post",
                    data : {
                        treatmentList : treatmentList
                    },
                    success : function (result){
                        location.reload();
                    }
                });
            }
        });
        
    });

    // Chức năng chọn hết
    document.getElementById("checkAll").onclick = function (){
        // Lấy danh sách checkbox
        var checkboxes = document.getElementsByName('treatment[]');

        if ($('#checkAll').is(":checked")){
            for (var i = 0; i < checkboxes.length; i++){
            checkboxes[i].checked = true;
            }
        }else{
            for (var i = 0; i < checkboxes.length; i++){
            checkboxes[i].checked = false;
            }
        }      
    };

</script>