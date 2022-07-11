<div class="col-12">
    <ul class="nav nav-pills mb-3" id="noti-tab" role="tablist">
        <?php if($allIndex){ ?>
            <?php foreach($allIndex as $k => $value){ ?>
                <li class="nav-item btn btn-outline-info btn-fw mb-3">
                    <a class="nav-link" id="dichvu-<?= $value['shortName'] ?>" data-toggle="pill" href="#<?= $value['shortName'] ?>-content" role="tab" aria-controls="cdha" aria-selected="true"><?= $value['fullName'] ?> <?php echo "(".count($value['child']).")" ?></a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>

<div class="tab-content" id="pills-tabContent">
    <?php if($allIndex){ ?>
        <?php foreach($allIndex as $k => $value){ ?>
            <div class="tab-pane fade" id="<?= $value['shortName'] ?>-content" role="tabpanel">
                <?php if ($value['child']){ ?>
                    <div class="card tab-content-item">
                        <div class="col-12">
                            <table class="table table-bordered table-hover" id="datatable">
                                <thead class="bg-primary">
                                    <tr>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">STT</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Tên</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Tên rút gọn</td>
                                        <td colspan="3" class="text-center font-weight-bold text-white">Nam</td>
                                        <td colspan="3" class="text-center font-weight-bold text-white">Nữ</td>
                                        <td rowspan="2" class="text-center font-weight-bold text-white">Đơn vị</td>
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
                                        <tr>
                                            <td class="text-center"> <?= $k+1; ?></td>
                                            <td class=""> <?= $child['fullName']; ?></td>
                                            <td class="text-center font-weight-bold"> <?= $child['shortName']; ?></td>
                                            <td class="text-center"><?= $child['min_nam'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['max_nam'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['text_nam'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['min_nu'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['max_nu'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['text_nu'] ?? '' ?></td>
                                            <td class="text-center"><?= $child['unit_nu'] ?? '' ?></td>
                                        </tr>
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