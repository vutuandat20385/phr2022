<div class="modal-dialog modal-md">
    <div class="modal-content" style="background: none; box-shadow: none;">
        <div class="card " style="box-shadow: 0 27px 24px 0 rgb(0 0 0 / 20%), 0 40px 77px 0 rgb(0 0 0 / 22%);">
            <div class="card-header card-header-rose card-header-text">
                <div class="card-text">
                <h4 class="card-title font-weight-bold">Thêm chỉ số chuẩn</h4>
                </div>
            </div>
            <div class="card-body ">
                <form method="post" action="<?=base_url();?>/settings/them-chi-so-chuan">
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Tên chỉ số</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_DisplayName">
                            <span class="bmd-help">Tên hiển thị của chỉ số.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Tên viết tắt</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_ShortName">
                            <span class="bmd-help">Tên viết tắt lưu trong CSDL.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Mã chỉ số</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_CodeName">
                            <span class="bmd-help">Mã dịch vụ trong phần mềm HIS.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Đơn vị</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_DisplayUnit">
                            <span class="bmd-help">Đơn vị hiển thị của chỉ số.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Nhóm chỉ số</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <select class="form-control" id="groupIndex"  name="standard_Group">
                                <option value="" selected>Chọn nhóm</option>
                                <?php foreach($group as $g){?>
                                    <option value="<?= $g['id']; ?>"><?= $g['fullName']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        </div>
                    </div>
                    
                    <div class="row m-0">
                        <table class="table-borderless" style="width: 100%; margin-top: 15px;">
                            <tr class="bg-success text-white font-weight-bold">
                                <td class="col-sm-3 text-center"></td>
                                <td class="col-sm-5 text-center p-1">Nam</td>
                                <td class="col-sm-4 text-center p-1">Nữ</td>
                            </tr>
                            <tr>
                                <td class="col-sm-3 text-center"><label class="col-form-label">Max</label></td>
                                <td class="col-sm-5 text-center"><input type="text" class="form-control text-center" name="standard_MaxValue_Male"></td>
                                <td class="col-sm-4 text-center"><input type="text" class="form-control text-center" name="standard_MaxValue_Female"></td>
                            </tr>
                            <tr>
                                <td class="col-sm-3 text-center"><label class="col-form-label">Min</label></td>
                                <td class="col-sm-5 text-center"><input type="text" class="form-control text-center" name="standard_MinValue_Male"></td>
                                <td class="col-sm-4 text-center"><input type="text" class="form-control text-center" name="standard_MinValue_Female"></td>
                            </tr>
                            <tr>
                                <td class="col-sm-3 text-center"><label class="col-form-label">Hiển thị</label></td>
                                <td class="col-sm-5 text-center"><input type="text" class="form-control text-center" name="standard_DisplayValue_Male"></td>
                                <td class="col-sm-4 text-center"><input type="text" class="form-control text-center" name="standard_DisplayValue_Female"></td>
                            </tr>
                        </table>
                    </div>
                    <!-- <div class="row">
                        <label class="col-sm-3 col-form-label">Giá trị lớn nhất</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_MaxValue">
                            <span class="bmd-help">Giá trị lớn nhất của chỉ số.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Giá trị nhỏ nhất</label>
                        <div class="col-sm-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="standard_MinValue">
                            <span class="bmd-help">Giá trị nhỏ nhất của chỉ số.</span>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 col-form-label label-checkbox">Giới tính</label>
                        <div class="col-sm-9 form-check-inline pl-1 mr-0">
                            <div class="form-check" style="padding-top: 8px;">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="genderRadios" value="Nam" disabled> Nam
                                <span class="circle">
                                <span class="check"></span>
                                </span>
                            </label>
                            </div>
                            <div class="form-check" style="padding-top: 8px;">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="genderRadios" value="Nữ" disabled> Nữ
                                <span class="circle">
                                <span class="check"></span>
                                </span>
                            </label>
                            </div>
                        </div>
                    </div> -->
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-default " data-dismiss="modal" style="width: 100%;">Hủy</button>
                        </div>
                        <div class="col-6">
                            <input type="submit" class="btn btn-primary" name="submit" value="thêm chỉ số" style="width: 100%;"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- /.modal-content -->
</div>
<!-- <script>
    $('#groupIndex').change(function(){
        $('input[name="genderRadios"]').removeAttr('disabled');
    });
</script>; -->