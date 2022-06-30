<?php
namespace App\Services;

use App\Models\User2Model;

class LoginService extends BaseService{
    public function __construct(){
        $this->user2 = new User2Model();
        
    }

    public function checkUser2($data){
        $check = $this->user2->where(['username' => $data['username']])->first();
        if($check){     
            if($check['password'] == hash('sha512',$data['password'].$check['salt'])){
                return array(
                    'status'    => 1,
                    'msg'       => '',
                    'user'      => $check
                );
            }else{
                return array(
                    'status'    => 0,
                    'msg'       => 'Mật khẩu không chính xác, xin vui lòng thử lại',
                    'user'      => null
                );
            }
            
        }else{
            return array(
                'status'    => 0,
                'msg'       => 'Tên đăng nhập chưa đăng ký trong hệ thống',
                'user'      => null
            );
        }
        
    }
}