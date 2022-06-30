<?php

namespace App\Controllers;

use App\Services\HomeService;
use App\Services\AccountService;

class AppointmentController extends BaseController {

    public function __construct(){

        $this->db = \Config\Database::connect();
	}

    public function getAppointmentReport() {

        $data['panelTitle'] = 'Báo cáo khám App Online';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/appointment/report');
		return view('AfterLogin/main', $data);
	}


  
}
