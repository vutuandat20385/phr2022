
    <div class="card p-3">
        <div class="row">
            <div class="col-6 pt-1">
                <h4 class="contentHeader"><?= $panelTitle; ?></h4>
            </div>
            
        </div>
      
        <div class="col-12 p-0 mt-2">
            <?php if ($data) { ?>
               <table id="historyImportDetail" class="table table-bordered table-striped d4u-table">
                <thead>
                  <tr class="bg-primary text-white">
                    <th>ID cột Excel</th>
                    <th>Mã nhân viên</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Thông báo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach ($data as $val) {
                    
                    $text_color = '';
                    if(isset($val['type'])){
                      if($val['type'] == 'error'){
                        $text_color = 'text-danger';
                      }else if($val['type'] == 'success'){
                          $text_color = 'text-success';
                      }else if($val['type'] == 'warning'){
                          $text_color = 'text-warning';
                      }
                    }
                    
                  ?>
                    <tr>
                      <td><?php if(isset($val['id'])){ echo $val['id'];} ?></td>
                      <td><?php if(isset($val['employee_id'])){ echo $val['employee_id'];} ?></td>
                      <td><?php if(isset($val['full_name'])){ echo $val['full_name'];} ?></td>
                      <td><?php if(isset($val['phone_number'])){ echo $val['phone_number'];} ?></td>
                      <td class="<?= $text_color; ?>"><?php if(isset($val['status'])){ echo $val['status'];} ?></td>
                    </tr>
                  <?php
                  }
                  ?>

                </tbody>
                <tfoot>
                  <tr>
                    <th>ID cột Excel</th>
                    <th>Mã nhân viên</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Thông báo</th>
                  </tr>
                </tfoot>
              </table>
            <?php } else { ?>
              <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Không có dữ liệu!</h5>
                Thiếu file dữ liệu hoặc file dữ liệu trống!
              </div>
            <?php
            }
            ?>

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

<script>
    $(document).ready(function(){

        $('#historyImportDetail').DataTable({
            "pageLength": 25,
            "order": [[ 0, "desc" ]],
            "pagingType": "full_numbers",
            "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
            ],
            responsive: true,
            language: {
            search: "_INPUT_",
            searchPlaceholder: "Tìm kiếm hồ sơ",
            info: "Đang xem trang _PAGE_ / _PAGES_",
            infoEmpty: "Không có hồ sơ phù hợp",
            infoFiltered: "(trong tổng số _MAX_ hồ sơ)",
            lengthMenu: "Hiển thị _MENU_ hồ sơ mỗi trang",
            paginate: {
                "first": "Trang đầu",
                "last": "Trang cuối",
                "next": "Trang tiếp",
                "previous": "Trang trước"
                },
            zeroRecords: "Không tìm thấy hồ sơ nào phù hợp",
            
            }
        });
    });

</script>