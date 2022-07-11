<?php

namespace App\Controllers;

use App\Services\LoginService;
use App\Forms\Forms;

class Login extends BaseController {
    
    public function __construct(){
        $this->login = new LoginService(); 
        $this->form = new Forms(); 
        
	}

    public function index(){
        if(session()->has('user')){
            $data['user'] 	= session()->get('user');
			return redirect()->to('/trang-quan-tri');
        }else{

            $dataLoginForm = [];
            $data['loginForm'] = $this->form->loginForm($dataLoginForm);

            $dataForgotPasswordForm = [];
            $data['forgotPasswordForm'] = $this->form->forgotPasswordForm($dataForgotPasswordForm);

            $data = $this->getBeforeLoginLayout($data, 'Đăng nhập trang quản trị', 'BeforeLogin/pages/login');
            return view('BeforeLogin/main', $data);
        }
    }

    public function loginProcess(){
        $data = array(
            'username'  =>  trim($this->request->getVar('username')),
            'password'  =>  $this->request->getVar('password'),
        );
        $result =  $this->login->checkUser2($data);
        if($result['status'] == 1){
            session()->setTempdata('user', $result['user'], 7*24*60*60);
            return redirect()->to('/trang-quan-tri');
        }else{
            session()->setFlashdata('msg', $result['msg']);
            return redirect()->to('/dang-nhap');
        } 
    }

    public function register(){

        $username       = $this->request->getVar('username');
        $password       = $this->request->getVar('password');
        $fullname       = $this->request->getVar('fullname');
        $email          = $this->request->getVar('email');
        $role           = $this->request->getVar('role');
        $salt           = $this->random_str(128);
        $data = array(
            'username'  => $username,
            'password'  => hash('sha512',$password.$salt),
            'fullname'  => $fullname,
            'email'     => $email,
            'role'      => $role,
            'salt'      => $salt,
        );

        $result = $this->login->addUser2($data);
        session()->setFlashdata('msg', $result['status']);
        return redirect()->to('/trang-quan-tri/tai-khoan/tao-tai-khoan');
    }

    public function logOut(){
        session()->remove('user');
		return redirect()->to('/dang-nhap')->with('msg','Bạn đã đăng xuất khỏi hệ thống!');
    }

    
}