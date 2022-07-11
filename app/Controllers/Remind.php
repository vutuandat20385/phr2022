<?php

namespace App\Controllers;

require 'vendor/autoload.php';

use App\Services\RemindService;
use App\Services\SettingService;
use App\Services\HomeService;

class Remind extends BaseController {

	public function __construct(){
		$this->remind = new RemindService();
        $this->setting = new SettingService();
        $this->service = new HomeService();
	}

	public function index() {
		return true;
	}

    public function appointmentList(){
    
        $perPage    =  $this->request->getVar('limit');
        $page       =  $this->request->getVar('page');

        if($page < 1){
            $page = 1;
        }
		
        $start  = ($page-1) * $perPage;
        $allAppointment = $this->remind->getAppointmentList($start, $perPage);
 
        if(!empty($allAppointment)){
        
            $countAll = count($this->remind->getAllAppointmentList());
            $totalPage = ceil($countAll/ $perPage);
        }else{

            $totalPage = 0;
            $allAppointment = array();
        }
        

        return json_encode(array(
            'totalpage' => $totalPage,
            'result' => $allAppointment
        ));
    }

    public function appointmentManage(){
        $perPage    =  $this->request->getVar('limit');
        $page       =  $this->request->getVar('page');
        $userID     =  $this->request->getVar('user_id');

        if($page < 1){
            $page = 1;
        }
		$start  = ($page-1) * $perPage;
           
        $allAppointment = $this->remind->getAppointmentManage($userID, $start, $perPage);
        if(!empty($allAppointment)){
            $countAll = count($this->remind->getAllAppointmentManage($userID));
            $totalPage = ceil($countAll/ $perPage);
        }else{
            $totalPage = 0;
            $allAppointment = array();
        }
        

        return json_encode(array(
            'totalpage' => $totalPage,
            'result' => $allAppointment
        ));
    }

    public function register(){

        $username       = $this->request->getVar('username');
        $password       = $this->request->getVar('password');
        $fullname       = $this->request->getVar('fullname');
        $email          = $this->request->getVar('email');
        $salt           = $this->random_str(128);
        $data = array(
            'username'  => $username,
            'password'  => hash('sha512',$password.$salt),
            'fullname'  => $fullname,
            'email'     => $email,
            'salt'      => $salt,
        );

        $result = $this->remind->addUser2($data);
        session()->setFlashdata('msg', $result['status']);
        return true;
    }

    function random_str(
		int $length = 64,
		string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
	    ): string {
		
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}

    public function login(){
        $data = array(
            'username'  =>  trim($this->request->getVar('username')),
            'password'  =>  $this->request->getVar('password'),
            'device_token' => $this->request->getVar('device_token')
        );
        $result =  $this->remind->checkUser2($data);
        return json_encode($result);
    }

    public function medicineRemind(){
        // Thời gian check: (phút)
        $time = 10;
        $status = 'COMPLETED';
        $timeCheck = date('Y-m-d H:i:s', strtotime ( '-'.$time.' minute' , strtotime ( date('Y-m-d H:i:s') ) ));
        $result = $this->remind->getMedicineRemind($status, $timeCheck);

        if(!empty($result)){
            $apm = '';
            foreach($result as $r){
                $apm .='['.$r['appointment_code'].'- BS:'.$r['given_name'].'];';
            }
       
            // get Phone array
            $allPhone = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'medicine-remind-phonelist']);
            $phone_arr = explode(',',$allPhone['settingValue']);

            $msg1 = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'medicine-remind-content']);

            $msg = $msg1['settingValue'].' '.$apm;

            foreach($phone_arr as $phone){
                if($phone != ''){
                    //Send SMS
                    $this->sendSMS($phone,'Doctor4U',$msg);
                }
            }
         
        }
    }

    public function appointmentComplete(){
        
        $allAppointment = $this->remind->getAllAppointment('INCONSULTATION', date('Y-m-d'));

        $msg = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'appointment-complete-remind-content']);
        $msg = $msg['settingValue'];
        if(!empty($allAppointment)){
            foreach($allAppointment as $ap){
                $phone = $ap['value'];
                if($phone != ''){
                    //Send SMS
                    $this->sendSMS($phone,'Doctor4U',$msg);
                }
            }
        }
    }

    public function remindAppointment(){
        $time = 10;
        $user_id = 1;
        $user = $this->remind->getUser2Info($user_id);
      
        if($user){
            $role = $user['role'];
            // get All Token
            $allToken = $this->remind->getAllToken($user_id);
            if($role == '1'){
                
                $app = $this->remind->getApointmentByProvider();
                
                if(!empty($app)){
                    foreach($app as $a){
                        
                        // check remind already
                        $check = $this->remind->checkRemind($a['appointment_id'], $user_id);
                        if($check){
                        
                            foreach($allToken as $tkn){
                                $notificationArray = array();
                                $notificationArray["content_available"] = true;
                                $notificationArray["title"] = 'Có đăng ký khám tư vấn qua app mới! ';
                                $notificationArray["body"]  = 'Mã cuộc tư vấn: '.$a['appointment_code'].' lúc '.date('H:i d/m/Y',strtotime($a['start_date']));
                                $notificationArray["sound"] = "doctor4u.wav";
                                $notificationArray["android_channel_id"] = "sound";
                    
                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $token = $tkn['token'];
                    
                                $dataField = array(
                                    'type' => 'notification'
                                );
                    
                                $fields = array(
                                    'content_available' => true,
                                    'to'                => $token,
                                    'notification'      => $notificationArray,
                                    'data'              => $dataField
                                );
                    
                                // Your Firebase Server API Key
                                $headers = array(
                                    'Authorization:key=AAAAgcNqugA:APA91bE0b225pJnZFw6y80xd8-KEt5QSGY5Oac1ETCkbvip4In2MsEo05Jus1brSmdUqLY_gRIoYsQYT_S67A46Sk-zL3H-shnThnAcdQArMaapGBz4DWg5xEvsEXIYKkEEabLyzAfmr', 
                                    'Content-Type:application/json'
                                );
                                // Open curl connection
                                $ch = curl_init();
                                // Set the url, number of POST vars, POST data
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                $result = curl_exec($ch);
                    
                                if ($result === false) {
                                
                                    die('Curl failed: ' . curl_error($ch));
                                }
                                curl_close($ch);
                            }

                            $dataSave = array(
                                'appointment_id' => $a['appointment_id'],
                                'admin_id' => $user_id
                            );

                            $this->remind->saveAppointmentRemindStatus($dataSave);
                        }
                    }
                }

                $sapDienRa = $this->remind->getTuVanSapDienRa($time);
                if(!empty($sapDienRa)){
                    foreach($sapDienRa as $s){
                        foreach($allToken as $tkn){
                            $notificationArray = array();
                            $notificationArray["content_available"] = true;
                            $notificationArray["title"] = 'Có cuộc khám tư vấn qua app sắp diễn ra! ';
                            $notificationArray["body"]  = 'Mã cuộc tư vấn: '.$a['appointment_code'].' lúc '.date('H:i d/m/Y',strtotime($a['start_date']));
                            $notificationArray["sound"] = "doctor4u.wav";
                            $notificationArray["android_channel_id"] = "sound";
                
                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $token = $tkn['token'];
                
                            $dataField = array(
                                'type' => 'notification'
                            );
                
                            $fields = array(
                                'content_available' => true,
                                'to'                => $token,
                                'notification'      => $notificationArray,
                                'data'              => $dataField
                            );
                
                            // Your Firebase Server API Key
                            $headers = array(
                                'Authorization:key=AAAAgcNqugA:APA91bE0b225pJnZFw6y80xd8-KEt5QSGY5Oac1ETCkbvip4In2MsEo05Jus1brSmdUqLY_gRIoYsQYT_S67A46Sk-zL3H-shnThnAcdQArMaapGBz4DWg5xEvsEXIYKkEEabLyzAfmr', 
                                'Content-Type:application/json'
                            );
                            // Open curl connection
                            $ch = curl_init();
                            // Set the url, number of POST vars, POST data
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                            $result = curl_exec($ch);
                
                            if ($result === false) {
                            
                                die('Curl failed: ' . curl_error($ch));
                            }
                            curl_close($ch);
                        }
                    }
                }
                            
            }
        }
    }


}
