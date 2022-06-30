<?php

namespace App\Controllers;

use App\Services\HomeService;
use App\Services\AccountService;

class Enforcement extends BaseController {

    public function __construct(){
        $this->home = new HomeService(); 
        $this->account = new AccountService();	

        $this->db = \Config\Database::connect();
	}

    public function index() {

        $data['panelTitle'] = 'Bảng tổng hợp';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/enforcement/index');
		return view('AfterLogin/main', $data);
	}

    public function sendSMSView() {

        $data['panelTitle'] = 'GỬI TIN NHẮN TỰ ĐỘNG THEO DANH SÁCH';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Gửi tin nhắn tự động theo danh sách | Doctor4U', 'AfterLogin/pages/enforcement/sendSMS');
		return view('AfterLogin/main', $data);
	}

    public function sendSMSProcess() {

        $content = trim($this->request->getVar('content'));
		$phone = trim($this->request->getVar('phone'));
		$result = $this->sendSMS($phone,'Doctor4U',$content);
		if(isset($result) && $result['status'] == 0){
			return '<span class="font-weight-bold">'.$phone.'</span> - <span class="text-danger">'.$result['error_desc'].'</span>';
		}else{
			return '<span class="font-weight-bold">'.$phone.'</span> - <span class="text-success">Thành công</span>';
		}
	}

    public function sendNotifyView() {

        $data['panelTitle'] = 'GỬI TIN THÔNG BÁO QUA APP THEO DANH SÁCH';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Gửi tin thông báo qua APP | Doctor4U', 'AfterLogin/pages/enforcement/sendNotify');
		return view('AfterLogin/main', $data);
	}

    public function commandView() {

        $data['panelTitle'] = 'TRUY VẤN CƠ SỞ DỮ LIỆU';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/enforcement/command');
		return view('AfterLogin/main', $data);
	}

  
}
