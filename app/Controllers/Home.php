<?php

namespace App\Controllers;

use App\Services\HomeService;

class Home extends BaseController {

    public function __construct(){
        $this->home = new HomeService(); 

	}

    public function index() {

        $data['panelTitle'] = 'Bảng tổng hợp';
        $data['user'] 	= session()->get('user');
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/home/index');
		return view('AfterLogin/main', $data);
	}

    public function analytic() {

        $data['panelTitle'] = 'Trang thống kê biểu đồ số liệu';

        //Thống kê số tài khoản đăng ký mới trong vòng 20 ngày
            $num_day = 20; 
            $data['analyticRegister'] = $this->home->getNewRegister($num_day);

            $day_chart = array();
			$value_chart = array();

            foreach($data['analyticRegister'] as $k => $aR){
				array_push($day_chart, $aR['day']['day']);
				array_push($value_chart, $aR['countUsers']);
			}

            $data['register_day_chart'] = $day_chart;
			$data['register_value_chart'] = $value_chart;

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/analytic/index');
		return view('AfterLogin/main', $data);
	}
}
