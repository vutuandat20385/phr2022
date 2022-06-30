<div class="col-12">
    <ul class="nav nav-pills mb-3" id="noti-tab" role="tablist">
        <?php if($allIndex){ ?>
            <?php foreach($allIndex as $k => $value){ ?>
                <li class="nav-item btn btn-outline-info btn-fw mb-3">
                    <a class="nav-link" id="dichvu-<?= $value['codeName'] ?>" data-toggle="pill" href="#<?= $value['codeName'] ?>-content" role="tab" aria-controls="cdha" aria-selected="true"><?= $value['name'] ?> <?php echo "(".count($value['child']).")" ?></a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>

<div class="tab-content" id="pills-tabContent">
    <?php if($allIndex){ ?>
        <?php foreach($allIndex as $k => $value){ ?>
            <div class="tab-pane fade" id="<?= $value['codeName'] ?>-content" role="tabpanel">
                <?php if ($value['child']){ ?>
                    <div class="card tab-content-item">
                        <div class="col-12">
                            <table class="table table-bordered table-hover" id="datatable">
                                <thead class="bg-primary">
                                    <tr>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">STT</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Tên</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Mã dịch vụ</td>
                                        <td colspan="3" class="text-center font-weight-bold text-white">Nam</td>
                                        <td colspan="3" class="text-center font-weight-bold text-white">Nữ</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Đơn vị</td>
                                        <td rowspan="2" ></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold text-white">Min</td>
                                        <td class="text-center font-weight-bold text-white">Max</td>
                                        <td class="text-center font-weight-bold text-white">Hiển thị</td>
                                        <td class="text-center font-weight-bold text-white">Min</td>
                                        <td class="text-center font-weight-bold text-white">Max</td>
                                        <td class="text-center font-weight-bold text-white">Hiển thị</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($value['child'] as $k => $child){ ?>
                                        <?php if ($child['status'] == 0){ ?>
                                            <tr class="deletedRecord">
                                                <td class="text-center"> <?= $k+1; ?></td>
                                                <td class=""> <?= $child['name']; ?></td>
                                                <td class="text-center font-weight-bold"> <?= $child['codeName']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td  class="text-center">
                                                    <a href="" data-toggle="modal" data-target="#editService<?= $child['id'] ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    <a href="" id="restore_service<?= $child['id'] ?>" onclick=restore_service(<?= $child['id'] ?>)><i class="fa fa-undo" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td class="text-center"> <?= $k+1; ?></td>
                                                <td class=""> <?= $child['name']; ?></td>
                                                <td class="text-center font-weight-bold"> <?= $child['codeName']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td  class="text-center">
                                                    <a href="" data-toggle="modal" data-target="#editService<?= $child['id'] ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    <a href="" id="del_service<?= $child['id'] ?>" onclick=delete_service(<?= $child['id'] ?>)><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <!-- EDIT SERVICE MODAL-->
                                        <div class="modal fade" id="editService<?= $child['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-uppercase font-weight-bold">Sửa dịch vụ khám</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label for="name">Tên dịch vụ</label>
                                                        <input type="text" value="<?= $child['name'] ?>" id="name<?= $child['id'] ?>" class="form-control bg-light">
                                                        <label for="codeName">Mã dịch vụ</label>
                                                        <input type="text" value="<?= $child['codeName'] ?>" id="codeName<?= $child['id'] ?>" class="form-control bg-light">
                                                        <label for="parent">Nhóm dịch vụ</label>
                                                        <select id="parent<?= $child['id'] ?>" class="form-control">
                                                            <?php foreach ($parent as $k => $p){ ?>
                                                                <?php if ($child['parentId'] == $p['id']){ ?>
                                                                    <option value="<?= $p['id'] ?>" selected><?= $p['name'] ?></option>
                                                                <?php } else { ?>
                                                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button id="edit_service<?= $child['id'] ?>" onclick=edit_service(<?= $child['id'] ?>) class="btn btn-primary">Sửa</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script>
    $('#add_service').click(function(){
        var name        = $("#name").val();
        var codeName    = $("#codeName").val();
        var parent      = $("#parent").val();

        $.ajax({
            url : "<?= site_url();?>trang-quan-tri/cau-hinh/dich-vu/them-moi",
            type : "post",
            dataType:"text",
            data : {
                name     : name,
                codeName : codeName,
                parent   : parent
            },
            success : function (result){
                location.reload();
            }
        });
    });

    function edit_service(id){
        var name        = $("#name" + id).val();
        var codeName    = $("#codeName" + id).val();
        var parent      = $("#parent" + id).val();

        $.ajax({
            url : "<?= site_url();?>trang-quan-tri/cau-hinh/dich-vu/chinh-sua/" + id,
            type : "post",
            dataType:"text",
            data : {
                name     : name,
                codeName : codeName,
                parent   : parent
            },
            success : function (result){
                location.reload();
            }
        });
    };

    function delete_service(id){
        $.ajax({
            url : "<?= site_url();?>trang-quan-tri/cau-hinh/dich-vu/xoa/" + id,
            type : "post",
            dataType:"text",
            data : {
            },
            success : function (result){
                location.reload();
            }
        });
    };

    function restore_service(id){
        $.ajax({
            url : "<?= site_url();?>trang-quan-tri/cau-hinh/dich-vu/khoi-phuc/" + id,
            type : "post",
            dataType:"text",
            data : {
            },
            success : function (result){
                location.reload();
            }
        });
    };
</script>