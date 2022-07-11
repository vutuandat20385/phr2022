<base href="<?= base_url(); ?>">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= $title; ?></title>
<!-- base:css -->
<link rel="stylesheet" href="public/assets/afterlogin/css/bootstrap.min.css">
<link rel="stylesheet" href="public/assets/afterlogin/vendors/mdi/css/materialdesignicons.min.css">
<link rel="stylesheet" href="public/assets/afterlogin/vendors/base/vendor.bundle.base.css">
<!-- endinject -->
<link rel="stylesheet" href="public/assets/afterlogin/css/jquery.datetimepicker.css">
<link rel="stylesheet" href="public/assets/afterlogin/css/select2.min.css">
<link rel="stylesheet" href="public/assets/afterlogin/css/font-awesome.min.css">
<!-- plugin css for this page -->
<!-- End plugin css for this page -->
<link rel="stylesheet" href="public/assets/afterlogin/css/menu.css">
<link rel="stylesheet" href="public/assets/afterlogin/css/jquery-ui.css">
<!-- inject:css -->
<link rel="stylesheet" href="public/assets/afterlogin/css/style.css">
<link rel="stylesheet" href="public/assets/afterlogin/css/d4u.css">
<!-- endinject -->
<link rel="shortcut icon" href="public/assets/afterlogin/images/favicon.png" />

<script src="public/assets/afterlogin/js/jquery.js" type="text/javascript"></script>
<script src="public/assets/afterlogin/js/popper.min.js" type="text/javascript"></script>
<script src="public/assets/afterlogin/js/bootstrap.min.js" type="text/javascript"></script>
<script src="public/assets/afterlogin/js/jquery.datetimepicker.js" type="text/javascript"></script>
<script src="public/assets/afterlogin/js/select2.min.js" type="text/javascript"></script>

<script src="public/assets/afterlogin/js/template.js"></script>
<script src="public/assets/afterlogin/js/jquery-ui.js"></script>
<script src="public/assets/afterlogin/vendors/chart.js/Chart.min.js"></script>
<script src="public/assets/afterlogin/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="public/assets/afterlogin/vendors/chartjs-plugin-datalabels/chartjs-plugin-datalabels.js"></script>
    <script src="public/assets/afterlogin/vendors/justgage/raphael-2.1.4.min.js"></script>
    <script src="public/assets/afterlogin/vendors/justgage/justgage.js"></script>
<script src="public/assets/afterlogin/js/jquery.cookie.js" type="text/javascript"></script>
<!-- Custom js for this page-->
<script src="public/assets/afterlogin/js/dashboard.js"></script>
<script src="public/assets/afterlogin/js/d4u.js"></script>
<!-- End custom js for this page-->

<?php
    function text_limit($str,$limit=10) {
        $str_s = '';
        if(stripos($str," ")){
            $ex_str = explode(" ",$str);
            if(count($ex_str)>$limit){
                for($i=0;$i<$limit;$i++){
                    $str_s.=$ex_str[$i]." ";
                }
                return $str_s.'...';
            }else{
                return $str;
            }
        }else{
            return $str;
        }
}
?>