<div class="card p-3">
    <div class="row">
        <div class="col-12">
            <h4 class="contentHeader"><?= $panelTitle; ?></h4>
        </div>
    </div>
    
    <?php if ($mktList) { ?>
        <table id="tblNoti" class="table table-bordered d4u-table">
            <thead>
                <tr class="bg-primary text-white">
                    <th class="text-center">STT</th>
                    <th class="text-center" >Nội dung</th>
                    <th class="text-center" >Link</th>
                    <th class="text-center" style="width: 130px;">Ngày giờ đăng</th>
                    <th class="text-center" >Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mktList as $k => $val) { 
                    if($val['index'] > 0){
                        switch ($val['status']) {
                            case '2':
                                $text = 'font-weight-bold text-black';
                                $trang_thai = 'Chưa xuất bản';
                                $title = 'Chưa xuất bản';
                                break;
                            case '1':
                                $text = 'font-weight-bold text-success';
                                $trang_thai = 'Đã xuất bản';
                                $title = 'Đã xuất bản';
                                break;
                            default:
                                $text = 'font-weight-bold text-danger';
                                $trang_thai = 'Đã xóa';
                                $title = 'Đã xóa';
                                break;
                        }
                ?>
                    <tr class="" title="<?= $title; ?>">
                        <td class="text-center"><?= $val['index']; ?></td>
                        <td><?= $val['content'] ?></td>
                        <td><?= $val['link'] ?></td>
                        <td><?= date('d/m/Y H:i',strtotime($val['public_time'])); ?></td>
                        <td class="<?= $text;?> text-center" style="width: 120px;"><?= $trang_thai; ?></td>
                        <td class="text-center" style="width: 80px;">
                            <a class="text-info" data-toggle="modal" data-target="#editMktNoti<?= $val['id']; ?>"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                            <?php
                                if($val['status'] > 0){ ?>
                                    <a class="text-black p-0" id="deleteMktNoti<?= $val['id']; ?>" onClick="deleteNoti(<?= $val['id']; ?>,0)" title="Xóa"> <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            <?php }else{ ?>
                                    <a class="text-black p-0" id="deleteMktNoti<?= $val['id']; ?>" onClick="deleteNoti(<?= $val['id']; ?>,2)" title="Khôi phục về trạng thái Chưa xuất bản"><i class="fa fa-undo" aria-hidden="true"></i></a>
                            <?php } ?>
                            
                        </td>
                        
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="editMktNoti<?= $val['id']; ?>" role="dialog" aria-labelledby="editMktNoti" aria-hidden="true">
                        
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-weight-bold" id="">CHỈNH SỬA THÔNG BÁO</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-1" title="Nội dung thông báo"><i class="fa fa-text-width" aria-hidden="true"></i></div>
                                        <div class="col-11" title="Nội dung thông báo"><textarea class="form-control" id="contentNoti<?= $val['id']; ?>" rows="3" placeholder="Nội dung thông báo"><?= $val['content'] ?></textarea></div>
                                        <div class="col-1" title="Link bài viết"><i class="fa fa-link" aria-hidden="true"></i></div>
                                        <div class="col-11" title="Link bài viết"><input class="form-control" id="linkNoti<?= $val['id']; ?>" placeholder="Link bài viết" value="<?= $val['link'] ?>"></div>
                                        <div class="col-1" title="Thời gian đăng"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                        <div class="col-11" title="Thời gian đăng"><input class="form-control timePublicNoti" id="timePublicNoti<?= $val['id']; ?>" placeholder="Thời gian đăng" value="<?= date('d/m/Y H:i',strtotime($val['public_time'])); ?>"></div>
                                        <div class="col-12" ><input class="form-control" id="mktNoti<?= $val['id']; ?>" value="<?= $val['id'] ?>" hidden></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" onClick="editMktNoti(<?= $val['id']; ?>)">Lưu chỉnh sửa</button>
                                </div>
                            </div>
                        </div>
                            
                    </div>
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
                    <?php $pagi_path = 'trang-quan-tri/marketing/thong-bao'; ?>
                    <?php $pager->setPath($pagi_path); ?>
                    <?= $pager->links(); ?>                  
                <?php endif; ?>            
            </div>           
        </div>  
    <?php } ?>
                    
</div>


