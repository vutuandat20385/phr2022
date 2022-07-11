<div class="card p-3">
    <div class="row">
        <div class="col-6 pt-1">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
        <div class="col-2 offset-2"><input type="text" name="search_info" class="form-control miniTextBox" placeholder="Họ tên, SĐT" value="<?= $info; ?>"></div>
        <div class="col-1"><button class="btn btn-outline-success btn-fw" style="width: 100%;" id="btn_timkiem">Tìm kiếm</button></div>
        <div class="col-1"> <a href="" data-toggle="modal" data-target="#modal-import" class="btn btn-outline-warning btn-fw" style="width: 100%;">Import</a></div>
    </div>
    
    <div class="col-12 p-0 mt-2">
        <table class="table table-bordered table-hover d4u-table" id="tblAllUsers" style=""> 
            <thead>
                <tr class="bg-primary text-white">
                    <th class="text-center">Id</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Ngày test</th>
                    <th>Kết quả</th>
                    <th>Loại test</th>
                    <th>File</th>
                    <th style="width: 80px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listCovidTest as $k => $val) { 
                    if($val['status'] == 0){
                        $status = 'deletedRecord';
                        $titleRecord = 'Bản ghi đã xóa, không hiển thị trên App bệnh nhân';
                    }else{
                        $status = '';
                        $titleRecord = '';
                    }

                    if($val['index'] > 0){    
                ?>
                    <tr class="<?= $status; ?>" title="<?= $titleRecord; ?>">
                        <td class="text-center"><?= $val['index']; ?></td>
                        <td><?= $val['patient_name'] ?></td>
                        <td><?= $val['phone_number'] ?></td>
                        <td class=""><?= date('d-m-Y',strtotime($val['date'])); ?></td>
                        <td class=""><?= $val['result']; ?></td>
                        <td class=""><?= $val['type']; ?></td>
                        <td class="text-center">
                            <?php if($val['file_result'] == null || $val['file_result'] == ''){ ?>
                                <a href="" class="pl-0 pr-0 text-primary" data-toggle="modal" data-target="#modal-sm<?= $val['id'] ?>"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                            <?php }else{ ?>
                                <a href="" class="pl-0 pr-0 text-success" data-toggle="modal" data-target="#viewFile<?= $val['id'] ?>"><i class="fa fa-desktop" aria-hidden="true"></i></a>
                            <?php }
                        ?>
                        </td>
                        <td class="text-center">
                            <a class="pl-0 pr-0 text-info"  data-toggle="modal" data-target="#editRecord<?= $val['id'] ?>" id="btnEdit<?= $val['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>   
                            <div class="modal fade" id="editRecord<?= $val['id']; ?>">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                            <div class="modal-body">
                                                <h4 class="font-weight-bold">CHỈNH SỬA KẾT QUẢ</h4>
                                                <div class="row">
                                                    <div class="col-3" style="padding-top: 7px;"><span class="font-weight-bold">Ngày test:</span> </div>
                                                    <div class="col-9"><input type="text" value="<?= date('d/m/Y',strtotime($val['date'])); ?>" id="edit_testDate<?= $val['id']; ?>" class="form-control edit_testDate"></div>
                                                    <div class="col-3" style="padding-top: 7px;"><span class="font-weight-bold">Kết quả:</span> </div>
                                                    <div class="col-9"><input type="text" value="<?= $val['result']; ?>" id="edit_testResult<?= $val['id']; ?>" class="form-control edit_testResult"></div>
                                                    <div class="col-3" style="padding-top: 7px;"><span class="font-weight-bold">Loại test:</span> </div>
                                                    <div class="col-9"><input type="text" value="<?= $val['type']; ?>" id="edit_testType<?= $val['id']; ?>" class="form-control edit_testType"></div>
                                                </div>
                                                <input type="text" value="<?= $val['id']; ?>" id="edit_id" name="edit_id" class="form-control hidden">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="" class="btn btn-danger" id="updateRecord<?= $val['id']; ?>">Cập nhật</button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                if($val['status'] == 1){ ?>
                                    <a class="pl-0 pr-0 text-danger" id="changeStatus<?= $val['id']; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>   
                                <?php }else{ ?>
                                    <a class="pl-0 pr-0 text-warning" id="changeStatus<?= $val['id']; ?>"><i class="fa fa-repeat" aria-hidden="true"></i></a>   
                                <?php }
                            ?>
                                <script>
                                    $('#changeStatus<?= $val['id']; ?>').click(function(){
                                        $.ajax({
                                            url : "<?= base_url()?>/trang-quan-tri/benh-an/d4u-test-covid/chinh-sua-trang-thai",
                                            type : "post",
                                            data : {
                                                id: <?= $val['id']; ?>
                                            },
                                            success : function (result){
                                                location.reload();
                                            }
                                        });
                                    });

                                    $('#updateRecord<?= $val['id']; ?>').click(function(){
                                       
                                        $.ajax({
                                            url : "<?= base_url()?>/trang-quan-tri/benh-an/d4u-test-covid/chinh-sua-ket-qua",
                                            type : "post",
                                            data : {
                                                edit_id: <?= $val['id']; ?>,
                                                edit_testDate: $('#edit_testDate<?= $val['id']; ?>').val(),
                                                edit_testResult: $('#edit_testResult<?= $val['id']; ?>').val(),
                                                edit_testType: $('#edit_testType<?= $val['id']; ?>').val()
                                            },
                                            success : function (result){
                                                location.reload();
                                            }
                                        });
                                    });

                                </script>
                        </td>
                    </tr>
                    <?php } ?>
                    <!-- /.modal -->
                    

                    <?php
                        if($val['file_result'] != null || $val['file_result'] != ''){
                            $file_result = json_decode($val['file_result'], true);
                            $file_url = $file_result['fileResult'];
                            $ext = $file_result['ext'];
                            $link = $protocol.'://docs.google.com/viewer?&url='.base_url().'/public/TestCovidResult'.'/'.$file_url.'&embedded=true';
                            ?>
                                <div class="modal fade" id="viewFile<?= $val['id']; ?>">
                            
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="row modal-header m-0"> <h4 class="modal-title font-weight-bold"><a href="<?= $link; ?>" target="_blank">Bấm để xem kết quả</a></h4>
                                            <form method="post" action="<?= base_url(); ?>/upload-result/<?= $val['id']; ?>" enctype="multipart/form-data">
                                                <div class="row m-0">
                                                    <div class="col-4 pt-2">Cập nhật File kết quả</div>
                                                    <div class="col-4"><input type="file" name="uploadFile" class="form-control" accept=".pdf, .png, .jpg, .jpeg"></div>
                                                    <div class="col-2"><button type="submit" class="btn btn-danger">Cập nhật</button> </div>
                                                    <div class="col-2"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button> </div>
                                                </div>       
                                            </form>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <?php if($ext == 'pdf'){ ?>
                                                <iframe src="<?php echo $protocol; ?>://docs.google.com/viewer?url=<?= base_url().'/public/TestCovidResult'.'/'.$file_url;?>&embedded=true" width="100%" height="800px" style="border: none;"></iframe>
                                                    
                                            <?php }else{ ?>
                                                <img src="<?= base_url().'/public/TestCovidResult'.'/'.$file_url;?>" width="100%" height="100%">
                                            <?php } ?>
                                        </div>
                                    </div>
                            
                                </div>
                    <?php }else{ ?>
                        <div class="modal fade" id="modal-sm<?= $val['id']; ?>">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header"> <h4 class="modal-title font-weight-bold">Cập nhật file kết quả</h4>
                                </div>
                                <form method="post" action="trang-quan-tri/benh-an/d4u-test-covid/upload-ket-qua" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-4">Tên bệnh nhân:</div>
                                            <div class="col-8"><span class="font-weight-bold"><?= $val['patient_name'];?></span></div>
                                            <div class="col-4">Số điện thoại:</div>
                                            <div class="col-8"><span class="font-weight-bold"><?= $val['phone_number'];?></span></div>
                                            <div class="col-4">Upload File kết quả</div>
                                            <div class="col-8"><input type="file" name="uploadFile" class="form-control" accept=".pdf, .png, .jpg, .jpeg"></div>

                                        </div>
                                    </div>    
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="form-check form-check-success">
                                                    <input type="text" value="<?= $val['id']; ?>" name="id" class="hidden"/>
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" value="sms" name="checkSMS" <?= $checkSMSUploadFile; ?>>
                                                        Gửi tin nhắn SMS cho khách hàng
                                                    <i class="input-helper"></i><i class="input-helper"></i></label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-danger">Cập nhật</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                        
                        </div>
                    <?php } ?>

                    
                <!-- </div> -->
            
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
            <?php $pagi_path = 'trang-quan-tri/benh-an/d4u-test-covid'; ?>
            <?php $pager->setPath($pagi_path); ?>
            <?= $pager->links(); ?>                  
        <?php endif; ?>            
    </div> 
    
</div>
<!-- Modal import PHR -->
<div class="modal fade" id="modal-import">
    <?= $form_importModal; ?>
</div>

<script>

$(document).ready(function(){

    $('.edit_testDate').datepicker({
        dateFormat: 'dd/mm/yy'
    });

    $('#btn_timkiem').click(function(){
        var info = $('input[name=search_info]').val();
        var link = '<?php echo base_url(); ?>' + '/trang-quan-tri/benh-an/d4u-test-covid?page=1';

        if(info != ''){ link += '&info='+info; }

        window.location.replace(link);

    });
    
});
  

</script>