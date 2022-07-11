<?php

namespace App\Controllers;

use App\Services\ApiService;
use App\Services\SettingService;

class Api extends BaseController {

    public function __construct(){
        $this->api = new ApiService(); 
        $this->setting = new SettingService();

	}

    public function index(){
        return 'AAA';
    }

    /**
     * Khách lẻ
     */
    public function get_hospital_user(){
        log_message('error', 1);
        $key 	= $this->request->getVar('key');
        $phone 	= $this->request->getVar('phone');
        if ($key == 'dtt@123') {
            if ($phone) {
                log_message('error', $key);
                log_message('error', $phone);
                $rs_data = $this->api->get_hospital_user($phone);
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
                $rs_phr = $this->api->get_phr_user($id_report);
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
                $rs_phr = $this->api->get_phr_user_2($id_report);
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
            $chiSoChuan = $this->setting->getChiSoChuan($gender);
         
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

    /**
     * Khách đoàn
     */

    public function get_hospital_user_group(){
        $key 	= $this->request->getVar('key');
        $phone 	= $this->request->getVar('phone');
        if ($key == 'dtt@123') {
            if ($phone) {
                $rs_data = $this->api->get_hospital_user_group($phone);
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

    public function get_phr_data_user_group() {
       
        $key 		    = $this->request->getVar('key');
        $id_report 	    = $this->request->getVar('id_examination_report');
        if ($key == 'dtt@123') {
            if ($id_report) {
               
                $rs_phr = $this->api->get_phr_user_group($id_report);
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

    public function get_phr_data_user_group_2() {
        $key 		    = $this->request->getVar('key');
        $id_report 	    = $this->request->getVar('id_examination_report');
        if ($key == 'dtt@123') {
            if ($id_report) {
                $rs_phr = $this->api->get_phr_user_group_2($id_report);
                if ($rs_phr) {
                    $data_json = json_decode($rs_phr['data'], true);
                    $data = array(
                        'code'          => 0,
                        'occ'           => $rs_phr['occ'],
                        'hospitalId'    => $rs_phr['hospitalId'],
                        'data'          => $data_json,
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

    public function get_phr_result_ranger_group() {
        $key = $this->request->getVar('key');
        if ($key == 'dtt@123') {
            $gender = trim($this->request->getVar('gender'));
            if ($gender == 'nam' || $gender == 'Nam' || $gender == 'male' || $gender == 'Male') {
                $genderValue = 'Nam';
            }else
                $genderValue = 'Nữ';
          
            $rs_phr = $this->setting->getStandardIndex($genderValue);
            
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
	
        return json_encode($data, true);

    }

  
}
