<?php

namespace App\Controllers;

use App\Services\ReportService;

class Reports extends BaseController {

    public function __construct(){
        $this->report = new ReportService(); 
	}

    public function appOnline() {

        $data['panelTitle'] = 'Báo cáo khám APP Online tháng';
       
        $info=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
    
        $perPage =  15;
        $allReports = $this->report->getAllReportsInMonth();
        $countAllReport = count($allReports);
   
        if($allReports){
          
            service('pager')->makeLinks($page+1, $perPage, $countAllReport);

            $start = $page * $perPage;
			$dataReport = array(
				'start' => $start,
				'limit' => $perPage,
				'info' => $info,
			);
            
            $reportArr = $this->report->getReportsInMonth($dataReport);

            foreach($reportArr as $k => $value){
                $reportArr[$k]['index'] = $countAllReport - $start - $k;
            }

            $data['allReports'] = $reportArr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAllReport/$perPage);
        
        }else{
            $data['allReports'] = false;
        }
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/report/appOnline');
		return view('AfterLogin/main', $data);
	}

    public function paymentHistory() {

        $data['panelTitle'] = 'Báo cáo Lịch sử thanh toán';
        $pager=service('pager');

        $info=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;

        $perPage =  15;
        $allReports = $this->report->getAllPaymentInMonth();
        $countAllReport = count($allReports);

        if($allReports){

            service('pager')->makeLinks($page+1, $perPage, $countAllReport);
            
            $start = $page * $perPage;
			$dataReport = array(
				'start' => $start,
				'limit' => $perPage,
				'info' => $info,
			);
            
            $reportArr = $this->report->getPaymentInMonth($dataReport);

            foreach($reportArr as $k => $value){
                $reportArr[$k]['index'] = $countAllReport - $start - $k;
            }

            $data['allReports'] = $reportArr;

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAllReport/$perPage);
        
        }else{
            $data['allReports'] = false;
        }
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/report/paymentHistory');
		return view('AfterLogin/main', $data);
	}

    public function appointmentHistory($person_id){

        $data['appointmentHistory'] = $this->report->getAppointmentHistory($person_id);

        $data['panelTitle'] = 'Lịch sử khám bệnh nhân';
        
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/report/appointmentHistory');
		return view('AfterLogin/main', $data);
    }
  
}
