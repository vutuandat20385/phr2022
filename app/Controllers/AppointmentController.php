<?php

namespace App\Controllers;

use App\Services\AppointmentService;
use App\Services\SettingService;

require_once 'TechAPI/bootstrap.php';
require 'vendor/autoload.php';

class AppointmentController extends BaseController{
    public function __construct(){
        $this->appointment 	= new AppointmentService();
        $this->setting 	    = new SettingService();
        $this->db           = \Config\Database::connect();
    }

    /**
     * Tư vấn qua app
     */
    public function getAppointmentList(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'Danh sách Tư vấn';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
        $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';
        $data['rCode'] 		=($this->request->getVar('rCode')!==null)?$this->request->getVar('rCode'):'';

        $perPage =  20;

        $allPHR = $this->appointment->getAppointmentList($data['info'], $data['date_from'], $data['date_to'], $data['rCode']);

        $countAll = count($allPHR);

        if($allPHR){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $phr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allPHR[$i])) {
                    array_push($phr, $allPHR[$i]);
                }
            }
            foreach($phr as $k => $value){
                $phr[$k]['index'] = $countAll - $start - $k;
            }

            $data['appointmentList'] = $phr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

            // Get Referred Code
            $sql_referralCode = "SELECT referral_code FROM referral_campaign";
            $data['referralCode'] = $this->db->query($sql_referralCode)->getResultArray();

        }else{
            // Get Referred Code
            $sql_referralCode = "SELECT referral_code FROM referral_campaign";
            $data['referralCode'] = $this->db->query($sql_referralCode)->getResultArray();

            $data['appointmentList'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/appointmentList');
        return view('AfterLogin/main', $data);
    }

    public function appointmentDetail($appointment_code){
        $data['user'] 	            = session()->get('user');
        $data['panelTitle']         = 'CHI TIẾT THÔNG TIN BỆNH NHÂN';
        $appointmentDetail 			= $this->appointment->getAppointmentDetail($appointment_code);
        $appointmentDetail['patientInfo']['appointment_code'] = $appointment_code;
        $data['appointmentInfo'] 	= $appointmentDetail['appointmentInfo'];
        $data['patientInfo'] 		= $appointmentDetail['patientInfo'];

        $patientId = $appointmentDetail['appointmentInfo']['patient_id'];

        $visitId = $appointmentDetail['appointmentInfo']['visit_id'];

        $appointment_id = $appointmentDetail['appointmentInfo']['appointment_id'];

        //obs
        $obs = $this->appointment->getObs($visitId);

        $trieu_chung 	= array();
        $kham_lam_sang 	= array();
        $cau_hoi 		= array();
        $ket_qua_cu 	= array();
        $loi_dan 		= array();
        $nhiet_do 		= '';
        $can_nang		= '';
        $mach			= '';
        $ha_min			= '';
        $ha_max			= '';
        $nhip_tho		= '';
        $chieu_cao		= '';
        $ruou_bia		= '';
        $thuoc_la		= '';
        $tien_su_gia_dinh = '';
        $thuoc_dang_su_dung = '';

        //Theo doi: appointmentscheduling_appointment_follow
        $theo_doi = $this->appointment->getAppointmentFollow($appointment_id);

        foreach($obs as $k => $ob){
            if($ob['concept_id'] == '6102' && $ob['voided'] == '0'){ array_push($trieu_chung, $ob['value_text']); }

            if($ob['concept_id'] == '6105'){ array_push($kham_lam_sang, $ob['value_text']); }

            if($ob['concept_id'] == '6110'){ array_push($cau_hoi, $ob['value_text']); }

            if($ob['concept_id'] == '165869'){
                $arr_kq = explode('_',$ob['value_complex']);
                $pos = strpos(end($arr_kq),'.');
                array_push($ket_qua_cu, substr( end($arr_kq),  0, $pos));
            }

            if($ob['concept_id'] == '6106'){ array_push($loi_dan, $ob['value_text']); }

            if($ob['concept_id'] == '5088'){ $nhiet_do = $ob['value_numeric']; }

            if($ob['concept_id'] == '5089'){ $can_nang = $ob['value_numeric']; }

            if($ob['concept_id'] == '5087'){ $mach = $ob['value_numeric']; }

            if($ob['concept_id'] == '5086'){ $ha_min = $ob['value_numeric']; }

            if($ob['concept_id'] == '5085'){ $ha_max = $ob['value_numeric']; }

            if($ob['concept_id'] == '5242'){ $nhip_tho = $ob['value_numeric']; }

            if($ob['concept_id'] == '5090'){ $chieu_cao = $ob['value_numeric']; }

            if($ob['concept_id'] == '6103'){ $ruou_bia = $ob['value_text']; }

            if($ob['concept_id'] == '6104'){ $thuoc_la = $ob['value_text']; }

            if($ob['concept_id'] == '6108'){ $tien_su_gia_dinh = $ob['value_text']; }

            if($ob['concept_id'] == '6107'){ $thuoc_dang_su_dung = $ob['value_text']; }

        }

        //Lý do khám
        //cau hoi : 6110 (obs)
        $data['cau_hoi'] = $cau_hoi;

        //ket qua cu : 165869
        $data['ket_qua_cu'] = $ket_qua_cu;

        //Hỏi bệnh
        //trieu chung : 6102 (obs)
        $data['trieu_chung'] = $trieu_chung;

        //Khám thể lực (obs)
        //Nhiệt độ: 5088
        $data['nhiet_do'] = $nhiet_do;

        //Cân nặng: 5089
        $data['can_nang'] = $can_nang;

        //Mạch: 5087
        $data['mach'] = $mach;

        //HA min: 5068
        $data['ha_min'] = $ha_min;

        //HA max: 5085
        $data['ha_max'] = $ha_max;

        //Nhịp thở: 5242
        $data['nhip_tho'] = $nhip_tho;

        //Chiều cao: 5090
        $data['chieu_cao'] = $chieu_cao;

        //Khám lâm sàng
        //kham lam sang : 6105 (obs)
        $data['kham_lam_sang'] = $kham_lam_sang;

        //Tiền sử bệnh (obs)
        //di ung: Allergy
        $data['di_ung'] = $this->appointment->getAllergy($patientId);

        //benh man tinh: Conditions
        $data['benh_man_tinh'] = $this->appointment->getCHRONIC($patientId);

        //Ruou bia: 6103
        $data['ruou_bia'] = $ruou_bia;
        //Thuoc la: 6104
        $data['thuoc_la'] = $thuoc_la;
        //Tien su gia dinh: 6108
        $data['tien_su_gia_dinh'] = $tien_su_gia_dinh;
        //Thuoc dang su dung: 6107
        $data['thuoc_dang_su_dung'] = $thuoc_dang_su_dung;

        //Lời dặn
        //loi dan bac si: 6106 (obs)
        $data['loi_dan'] = $loi_dan;

        //Chẩn đoán
        //chan doan
        $data['chan_doan'] = $this->appointment->getEncounterDiagnosis($visitId);

//            //CLS
//            //can lam sang (order type = 4)
//            $cls_uuid = $this->appointment->getOrderUUID($visitId,4);
//            foreach ($cls_uuid as $k => $cls){
//                //gọi đến api thông qua uuid
//                $cls_order = $this->apiOrder($cls['uuid']);
//
//                $clsDetail[$k]['group'] = $cls_order['display'];
//                $clsDetail[$k]['member'] = $cls_order['orderSet']['orderSetMembers'];
//            }
//            $data['can_lam_sang'] = $clsDetail;
//
//            //Đơn thuốc
//            //don thuoc (order type = 5)
//            $drugs_uuid = $this->appointment->getOrderUUID($visitId,5);
//            foreach ($drugs_uuid as $k => $drugs){
//                //gọi đến api thông qua uuid
//                $drugs_order = $this->apiOrder($drugs['uuid']);
//
//                if ($drugs_order['drug']['name'] == 'Other') {
//                    $drugsDetail[$k]['concept_name'] = $drugs_order['display'];
//                } else {
//                    $drugsDetail[$k]['name'] = $drugs_order['drug']['name'];
//                    $drugsDetail[$k]['concept_name'] = $drugs_order['drug']['concept']['display'];
//                }
//                $drugsDetail[$k]['quantity'] = $drugs_order['quantity'];
//                $drugsDetail[$k]['unit'] = $drugs_order['drugUnits'];
//                $drugsDetail[$k]['instruction'] = $drugs_order['dosingInstructions'];
//                $drugsDetail[$k]['dosage'] = $drugs_order['timeUses'];
//                $drugsDetail[$k]['status'] = $drugs_order['eatingStatus'];
//
////                echo '<pre>';
////                print_r($drugs_order);
////                echo '</pre>';
//            }
//            $data['don_thuoc'] = $drugsDetail;

        //Theo dõi
        //theo doi: AppointmentFollow
        $data['theo_doi'] = $this->appointment->getAppointmentFollow($appointment_id);

        //Lịch sử khám
        $data['lich_su_kham'] = $this->appointment->history_appointment($patientId);
//            $data['lich_su_kham'] = array_slice($data['lich_su_kham'], 0, 5);
//            echo '<pre>';
//            print_r($data['lich_su_kham']);
//            echo '</pre>';

        $data 			= $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/appointmentDetail');
        return view('AfterLogin/main', $data);
    }

    /**
     * Xuất đơn thuốc
     */
    public function createPrescriptionPDF($appointment_code){
        $appointmentDetail = $this->appointment->getAppointmentDetail($appointment_code);

        //thông tin bệnh nhân
        $data['patientInfo'] = $appointmentDetail['patientInfo'];

        $visitId = $appointmentDetail['appointmentInfo']['visit_id'];

        //chẩn đoán
        $data['chan_doan'] = $this->appointment->getEncounterDiagnosis($visitId);

        //        thông tin đơn thuốc

        //        don thuoc (order type = 5)
        //        $drugs_uuid = $this->appointment->getOrderUUID($visitId,5);
        //        foreach ($drugs_uuid as $k => $drugs){
        //            //gọi đến api thông qua uuid
        //            $drugs_order = $this->apiOrder($drugs['uuid']);
        //
        //            if ($drugs_order['drug']['name'] == 'Other') {
        //                $drugsDetail[$k]['concept_name'] = $drugs_order['display'];
        //            } else {
        //                $drugsDetail[$k]['name'] = $drugs_order['drug']['name'];
        //                $drugsDetail[$k]['concept_name'] = $drugs_order['drug']['concept']['display'];
        //            }
        //            $drugsDetail[$k]['quantity'] = $drugs_order['quantity'];
        //            $drugsDetail[$k]['unit'] = $drugs_order['drugUnits'];
        //            $drugsDetail[$k]['instruction'] = $drugs_order['dosingInstructions'];
        ////            echo '<pre>';
        ////            print_r($drugs_order);
        ////            echo '</pre>';
        //        }
        //
        //        $data['don_thuoc'] = $drugsDetail;

        //lưu ý của bác sĩ
        $loi_dan = array();
        $obs = $this->appointment->getObs($visitId);

        foreach($obs as $k => $ob) {
            if ($ob['concept_id'] == '6106') {
                array_push($loi_dan, $ob['value_text']);
            }
        }

        $data['loi_dan'] = $loi_dan;

        $data = $this->getPDFLayout($data, 'Trang quản trị', 'AfterLogin/pages/pdf/pages/thuoc');

        $date = date('Ymd');
        $dompdf = new \Dompdf\Dompdf();
        $option = $dompdf->getOptions();
        $option->set(array('defaultFont' => 'Times New Roman', 'isRemoteEnabled' => true));
        $dompdf->setOptions($option);
        $dompdf->loadHtml(view('AfterLogin/pages/pdf/main', $data), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        /*
        Attachment => 0: Xem PDF online
        Attachment => 1: Tải PDF
         **/
        $dompdf->stream('DonThuoc_'.$date.'.pdf', array('Attachment' => 0));
        return view('AfterLogin/pages/pdf/main', $data);
    }

    /**
     * Quản lý tư vấn
     */
    public function appointmentFollowManage(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'Quản lý Tư vấn';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
        $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

        $perPage = 15;

        $allPHR = $this->appointment->getAppointmentManage($data['info'], $data['date_from'], $data['date_to']);

        $countAll = count($allPHR);

        if($allPHR){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $phr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allPHR[$i])) {
                    array_push($phr, $allPHR[$i]);
                }
            }
            foreach($phr as $k => $value){
                if(isset($value)){
                    $phr[$k]['index'] = $countAll - $start - $k;
                    // Triệu chứng, chẩn đoán, đề nghị của bs
                    $appointment_code = $value['appointment_code'];
                    log_message('error', $appointment_code);
                    $appointmentDetail = $this->appointment->getAppointmentDetail($appointment_code);
                    $visitId = $appointmentDetail['appointmentInfo']['visit_id'];

                    //obs
                    $obs = $this->appointment->getObs($visitId);

                    $trieu_chung 	= array();
                    $loi_dan        = array();

                    foreach($obs as $ob){
                        if($ob['concept_id'] == '6102'){ array_push($trieu_chung, $ob['value_text']); }
                        if($ob['concept_id'] == '6106'){ array_push($loi_dan, $ob['value_text']); }
                    }

                    $phr[$k]['trieu_chung'] = $trieu_chung;
                    $phr[$k]['feedback'] = $loi_dan;
                    $phr[$k]['chan_doan'] = $this->appointment->getEncounterDiagnosis($visitId);
                }

            }

            $data['appointmentList'] = $phr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

        }else{

            $data['appointmentList'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/appointmentFollowManage');
        return view('AfterLogin/main', $data);
    }

    public function editAppointmentFollowManage(){
        $re_exam = trim($this->request->getVar('re_exam'));
        if($re_exam != ''){
            $date = \DateTime::createFromFormat('d/m/Y', $re_exam);
            $re_exam_date = $date->format('Y-m-d 00:00:01');
        }else{
            $re_exam_date = '';
        }

        $data = [
            'appointment_id' 	=> trim($this->request->getVar('appointment_id')),
            'symptom' 			=> trim($this->request->getVar('symptom')),
            'diagnose' 			=> trim($this->request->getVar('diagnose')),
            'feedback' 			=> trim($this->request->getVar('feedback')),
            're_exam' 			=> $re_exam_date,
            'note' 				=> trim($this->request->getVar('note'))
        ];

        $this->appointment->editAppointmentFollowManage($data);

        return redirect()->to('/appointment-follow-manage');
    }

    /**
     * Lịch tái khám
     */
    public function appointmentFollowList(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'QUẢN LÝ LỊCH TÁI KHÁM';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
        $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

        $perPage =  15;

        $allPHR = $this->appointment->getAppointmentFollowList($data['info'], $data['date_from'], $data['date_to']);
        $countAll = count($allPHR);

        if($allPHR){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $phr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allPHR[$i])) {
                    array_push($phr, $allPHR[$i]);
                }
            }
            foreach($phr as $k => $value){
                $phr[$k]['index'] = $countAll - $start - $k;
                $today = date('d-m-Y');
                $date = date('d-m-Y', strtotime($value['ngay_tai_kham']));
                if($today == $date){

                    if($value['follow_result'] == null){
                        $phr[$k]['status'] = '<span class="text-success font-weight-bold">Đến hẹn</span>';
                    }else{
                        $phr[$k]['status'] = '<span class="text-primary font-weight-bold">Đã liên lạc</span>';
                    }
                }else{
                    if($value['follow_result'] == null){
                        $phr[$k]['status'] = '<span class="text-info font-weight-bold">Sắp đến hẹn</span>';
                    }else{
                        $phr[$k]['status'] = '<span class="text-primary font-weight-bold">Đã liên lạc</span>';
                    }
                }

            }

            $data['appointmentFollowList'] = $phr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

        }else{
            $data['appointmentList'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/appointmentFollowList');
        return view('AfterLogin/main', $data);
    }

    public function appointmentFollowListOver(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'LỊCH TÁI KHÁM QUÁ HẸN';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
        $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

        $perPage =  15;

        $allPHR = $this->appointment->getAppointmentFollowListOver($data['info'], $data['date_from'], $data['date_to']);
        $countAll = count($allPHR);

        if($allPHR){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $phr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allPHR[$i])) {
                    array_push($phr, $allPHR[$i]);
                }
            }
            foreach($phr as $k => $value){
                $phr[$k]['index'] = $countAll - $start - $k;
                $today = date('d-m-Y');
                $date = date('d-m-Y', strtotime($value['ngay_tai_kham']));
                if($value['follow_result'] == null){
                    $phr[$k]['status'] = '<span class="text-danger font-weight-bold">Đã quá hẹn</span>';
                }else{
                    $phr[$k]['status'] = '<span class="text-primary font-weight-bold">Đã liên lạc</span>';
                }

            }

            $data['appointmentFollowList'] = $phr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

        }else{
            $data['appointmentList'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/appointmentFollowListOver');
        return view('AfterLogin/main', $data);
    }

    public function updateAppointmentFollowStatus(){
        $id 		= $this->request->getVar('id');
        $content 	= $this->request->getVar('content');
        $result 	= $this->appointment->updateAppointmentFollowStatus($id, $content);
        return redirect()->to('/appointment-follow');
    }

    /**
     * Gửi thông báo nhắc nhở bác sĩ
     */
    public function notificationAppointmentFollow(){
        $time1 = 3;
        $time2 = 1;
        $listAF1 = $this->appointment->getAppointmentNotifyList($time1);
        $listAF2 = $this->appointment->getAppointmentNotifyList($time2);

        $contentNotification1 = $this->setting->getSettingValue(['settingType'=> 'follow', 'settingName' => 'follow-notify-1']);
        $contentNotification2 = $this->setting->getSettingValue(['settingType'=> 'follow', 'settingName' => 'follow-notify-2']);
        $contentSMS1 = $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => 'follow-sms-1']);
        $contentSMS2 = $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => 'follow-sms-2']);

        $checkNotify = $this->setting->getSettingValue(['settingType'=> 'follow', 'settingName' => 'follow-notify-default']);
        $checkSMS = $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => 'follow-sms-default']);
        if($checkNotify['settingValue'] == 'yes'){
            foreach($listAF1 as $patient1){
                $phone = $patient1['value'];
                $this->send_notification($phone, $contentNotification1['settingValue']);
            }

            foreach($listAF2 as $patient2){
                $phone = $patient2['value'];
                $this->send_notification($phone, $contentNotification2['settingValue']);
            }
        }

        if($checkSMS['settingValue'] == 'yes'){
            foreach($listAF1 as $patient1){
                $phone = $patient1['value'];
                $brandName = 'Doctor4U';
                $msg = $contentSMS1['settingValue'];
                $this->sendSMS($phone,$brandName,$msg);
            }

            foreach($listAF2 as $patient2){
                $phone = $patient2['value'];
                $brandName = 'Doctor4U';
                $msg = $contentSMS2['settingValue'];
                $this->sendSMS($phone,$brandName,$msg);
            }
        }
    }

    /**
     * Quản lý số dư tài khoản
     */
    public function accountBalance(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'QUẢN LÝ SỐ DƯ TÀI KHOẢN';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';

        $perPage =  20;

        $allAccount = $this->appointment->getAccountBalance($data['info'],'wallet_id');
        $countAll = count($allAccount);

        if($allAccount){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $accountArr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allAccount[$i])) {
                    array_push($accountArr, $allAccount[$i]);
                }
            }
            foreach($accountArr as $k => $value){
                $accountArr[$k]['index'] = $countAll - $start - $k;


            }

            $data['accountBalance'] = $accountArr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

        }else{
            $data['accountBalance'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/accountBalance');


        return view('AfterLogin/main', $data);
    }

    public function accountBalanceSort(){
        $data['user'] 	= session()->get('user');
        $pager = \Config\Services::pager();

        $data['panelTitle'] = 'QUẢN LÝ SỐ DƯ TÀI KHOẢN';
        $pager=service('pager');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $data['info'] = ($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';

        $perPage =  20;

        $allAccount = $this->appointment->getAccountBalance($data['info'],'balance');
        $countAll = count($allAccount);

        if($allAccount){

            $pager->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $finish = ($page+1) * $perPage;
            $accountArr = array();
            for($i=$start; $i<$finish; $i++){
                if (isset($allAccount[$i])) {
                    array_push($accountArr, $allAccount[$i]);
                }
            }
            foreach($accountArr as $k => $value){
                $accountArr[$k]['index'] = $countAll - $start - $k;


            }

            $data['accountBalance'] = $accountArr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);

        }else{
            $data['accountBalance'] = false;
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/accountBalanceSort');


        return view('AfterLogin/main', $data);
    }
}