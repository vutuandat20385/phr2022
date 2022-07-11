<?php

namespace App\Controllers;

require 'vendor/autoload.php';

use App\Services\HomeService;
use App\Services\SettingService;

class Home extends BaseController {

	private $service = '' ;

	public function __construct(){
		$this->service = new HomeService();
        $this->home = new HomeService();
        $this->setting = new SettingService();

	}

	public function index() {

        $data['panelTitle'] = 'Bảng tổng hợp';
        $data['user'] 	= session()->get('user');

        // Thống kê
        $data['analytics'] = array(
            'tong_so_tk' => $this->home->countPatientAccount(),
            'tk_hoat_dong_ngay' => $this->home->countActiveAccountInDay(),
            'tk_moi_dang_ky' => $this->home->countNewRegisterInDay(),
            'tk_sinh_nhat' => $this->home->countBirthdateInday(),
            'bc_kham_app_online' => $this->home->countAppointmentInMonth(),
            'bc_lich_su_thanh_toan' => $this->home->countPaymentInMonth(),
            'vnpay' => $this->home->countAllVNPAY(),
            'momo' => $this->home->countAllMOMO(),
            'ds_chuyen_khoan' => $this->home->countAllTRANSFER(),
        );

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
        
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/home/index');
		return view('AfterLogin/main', $data);
	}

    public function activeUsersInDay(){
        $data['panelTitle'] = 'DANH SÁCH TÀI KHOẢN HOẠT ĐỘNG TRONG NGÀY';
        $data['user'] 	= session()->get('user');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

        $allUser = $this->home->countActiveAccountInDay();
        $totalUsers = $allUser['list'];
        
        if(empty($totalUsers)){
            $countAll = 0;
            $data['currentPage'] = 1;
            $data['posts'] = false;
        }else{
            $countAll = count($totalUsers);
            
            service('pager')->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $data['posts'] = $this->home->getActiveAccountInDay($start, $perPage);
     
            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $countAll - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }
        

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/home/activeUsersInDay');
		return view('AfterLogin/main', $data);
    }

    public function newRegisters(){
        $data['panelTitle'] = 'Danh sách tài khoản mới đăng ký';
        $data['user'] 	= session()->get('user');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

        $allUser = $this->home->countNewRegisterInDay();
        $totalUsers = $allUser;
        
        if(empty($totalUsers)){
            $countAll = 0;
            $data['currentPage'] = 1;
            $data['posts'] = false;
        }else{
            $countAll = count($totalUsers);
            
            service('pager')->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $data['posts'] = $this->home->getNewRegisterInDay($start, $perPage);
     
            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $countAll - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/home/newRegisters');
		return view('AfterLogin/main', $data);
    }

    public function birthdateUsersInDay(){
        $data['panelTitle'] = 'Danh sách tài khoản sinh nhật trong ngày';
        $data['user'] 	= session()->get('user');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

        $allUser = $this->home->countBirthdateInday();
        $totalUsers = $allUser;
        
        if(empty($totalUsers)){
            $countAll = 0;
            $data['currentPage'] = 1;
            $data['posts'] = false;
        }else{
            $countAll = count($totalUsers);
            
            service('pager')->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;
            $data['posts'] = $this->home->getBirthdateInday($start, $perPage);
     
            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $countAll - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/home/birthdateUsersInDay');
		return view('AfterLogin/main', $data);
    }

	public function get_hospital_user(){
        $key 	= $this->request->getVar('key');
        $phone 	= $this->request->getVar('phone');
        if ($key == 'dtt@123') {
            if ($phone) {
                $rs_data = $this->service->get_hospital_user($phone);
                if ($rs_data) {
                    $data = array(
                        'code' => 0,
                        'data' => $rs_data,
                    );
                } else {
                    $data = array(
                        'code' => 1,
                        'data' => 'không khớp!',
                    );
                }

            } else {
                $data = array(
                    'code' => 1,
                    'message' => 'chưa nhập số điện thoại',
                );

            }
        } else {
            $data = array(
                'code' => 1,
                'data' => 'key không khớp !',
            );
        }

        return json_encode($data, true);
    }

	public function get_phr_data_user() {
        $key 		    = $this->request->getVar('key');
        $id_report 	    = $this->request->getVar('id_examination_report');
        if ($key == 'dtt@123') {
            if ($id_report) {
                $rs_phr = $this->service->get_phr_user($id_report);
                if ($rs_phr) {
                    $data_json = json_decode($rs_phr['data'], true);
                    $data = array(
                        'code'          => 0,
                        'hospitalId'    => $rs_phr['hospitalId'],
                        'data'          => $data_json,
                    );
                } else {
                    $data = array(
                        'code'          => 1,
                        'hospitalId'    => null,
                        'data'          => 'không khớp!',
                    );
                }

            } else {
                $data = array(
                    'code'              => 1,
                    'hospitalId'        => null,
                    'message'           => 'id sai',
                );

            }
        } else {
            $data = array(
                'code'                  => 1,
                'hospitalId'            => null,
                'data'                  => 'key không khớp !',
            );
        }
        return json_encode($data, true);
    }

    public function get_phr_data_user_2() {
        $key 		    = $this->request->getVar('key');
        $id_report 	    = $this->request->getVar('id_examination_report');
        if ($key == 'dtt@123') {
            if ($id_report) {
                $rs_phr = $this->service->get_phr_user_2($id_report);
                if ($rs_phr) {
                    $data_json = json_decode($rs_phr['data'], true);
                    $data = array(
                        'code'          => 0,
                        'occ'           => $rs_phr['occ'],
                        'hospitalId'    => $rs_phr['hospitalId'],
                        'data'          => $data_json

                    );
                } else {
                    $data = array(
                        'code'          => 1,
                        'occ'           => null,
                        'hospitalId'    => null,
                        'data'          => 'không khớp!',
                    );
                }

            } else {
                $data = array(
                    'code'              => 1,
                    'occ'               => null,
                    'hospitalId'        => null,
                    'message'           => 'id sai',
                );

            }
        } else {
            $data = array(
                'code'                  => 1,
                'occ'                   => null,
                'hospitalId'            => null,
                'data'                  => 'key không khớp !',
            );
        }
        return json_encode($data, true);
    }

	public function get_phr_result_ranger() {
        $key = $this->request->getVar('key');
        if ($key == 'dtt@123') {
            $gender = trim($this->request->getVar('gender'));
            if ($gender == 'nam' || $gender == 'Nam' || $gender == 'male' || $gender == 'Male') {
                $genderValue = 'Nam';
            }else
                $genderValue = 'Nữ';
            // $rs_phr = $this->service->get_rs_php($id_get);
            $rs_phr = $this->setting->getStandardIndex($genderValue);
            // $data_json = json_decode($rs_phr, true);
            $data = array(
                'code' => 0,
                'data' => $rs_phr,
            );
        } else {
            $data = array(
                'code' => 1,
                'data' => 'key không khớp !',
            );
        }
	
        return json_encode($data);

    }

    public function get_phr_result_ranger_2() {
        $key = $this->request->getVar('key');
         if ($key == 'dtt@123') {
            $gender = trim($this->request->getVar('gender'));
            log_message('error', 'start');
            $chiSoChuan = $this->setting->getChiSoChuan($gender);
            log_message('error', 'finish');
            $data = array(
                'code' => 0,
                'data' => $chiSoChuan,
            );
        } else {
            $data = array(
                'code' => 1,
                'data' => 'key không khớp !',
            );
        }
	
        return json_encode($data);

    }

    public function chiSoChuan(){
        $key = $this->request->getVar('key');
        return json_encode($key);
        // if ($key == 'dtt@123') {
        //     $gender = trim($this->request->getVar('gender'));
        //     log_message('error', 'start');
        //     $chiSoChuan = $this->setting->getChiSoChuan($gender);
        //     log_message('error', 'finish');
        //     $data = array(
        //         'code' => 0,
        //         'data' => $chiSoChuan,
        //     );
        // } else {
        //     $data = array(
        //         'code' => 1,
        //         'data' => 'key không khớp !',
        //     );
        // }
	
        // return json_encode($data);
    }

}
