
<!-- Main content -->

<div class="card m-0 ">
  <div class="card-header">
    <div class="col-12">
        <h4 ><?= $panelTitle; ?></h4>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <?php
    if ($phr) {
    ?>
        <table id="historyImport" class="table table-bordered table-hover d4u-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>File</th>
            <th>Người Import</th>
            <th>Thành công</th>
            <th>Thất bại</th>
            <th>Ngày</th>
            <th>Thông tin</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($phr as $val) {
          ?>
            <tr>
              <td><?php echo $val['id'] ?></td>
              <td><?php echo $val['file_name'] ?></td>
              <td><?php echo $val['fullname'] ?></td>
              <td><?php echo $val['count_success'] ?></td>
              <td><?php echo $val['count_false'] ?></td>
              <td><?php echo $val['date'] ?></td>
              <td><a href="<?=base_url('detail-import').'/'.$val['id'] ?>" id="view_report" type="button"  class="btn btn-block btn-warning">View</a></td>
            </tr>
          <?php
          }
          ?>

        </tbody>
        <tfoot>
          <tr>
              <th>Id</th>
              <th>File</th>
              <th>Người Import</th>
              <th>Thành công</th>
              <th>Thất bại</th>
              <th>Ngày</th>
              <th>Thông tin</th>
          </tr>
        </tfoot>
      </table>
    <?php
    } else {
    ?>
      <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-exclamation-triangle"></i> Không có dữ liệu!</h5>
      </div>
    <?php
    }
    ?>

  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    $(document).ready(function(){

        $('#historyImport').DataTable({
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