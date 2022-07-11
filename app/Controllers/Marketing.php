<?php

namespace App\Controllers;

use App\Services\MarketingService;
use App\Services\AccountService;
use App\Services\SettingService;

class Marketing extends BaseController {

    public function __construct(){
        $this->marketing = new MarketingService(); 
        $this->account = new AccountService();	
        $this->setting = new SettingService();

        $this->db = \Config\Database::connect();
	}

    public function marketingNotification(){
	
        $data['pageTitle']	= 'TRANG QUẢN LÝ THÔNG BÁO MARKETING';
        $data['panelTitle']	= 'TRANG QUẢN LÝ THÔNG BÁO MARKETING';
        $data['user'] 		= session()->get('user');
        $page				= (int)(($this->request->getVar('page')!==null) ? $this->request->getVar('page') : 1)-1;
        $data['info']	= ($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $data['start']	= ($this->request->getVar('start')!==null) ? $this->request->getVar('start'):'';
        $data['end']		= ($this->request->getVar('end')!==null) ? $this->request->getVar('end'):'';

        $perPage 		=  20;
        $dataMkt 		= array(
            'info' 	=> $data['info'],
            'start' => $data['start'],
            'end' 	=> $data['end']
        );
        $allMkt = $this->marketing->getAllMarketingNotification($dataMkt);
        
        $countAll = count($allMkt);

        if($allMkt){
            
            service('pager')->makeLinks($page+1, $perPage, $countAll);
            $start = $page * $perPage;

            $data['mktList'] = $this->marketing->getMarketingNotification($dataMkt, $start, $perPage);
            foreach($data['mktList'] as $k => $value){
                $data['mktList'][$k]['index'] = $countAll - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($countAll/$perPage);
        }else{
            $data['mktList'] = false;
        }
        
        $data = $this->getAfterLoginLayout($data, 'TRANG QUẢN LÝ THÔNG BÁO MARKETING', 'AfterLogin/pages/marketing/notification');
        return view('AfterLogin/main', $data);
		
	}

	public function addMarketingNotification(){
		$data['content'] 		= $this->request->getVar('content');
		$data['link'] 			= $this->request->getVar('link');
		$time					= $this->request->getVar('time');

		$public_time = \DateTime::createFromFormat('d/m/Y H:i', $time);
		$data['public_time'] = $public_time->format('Y-m-d H:i');

		$result = $this->setting->addMarketingNotification($data);
		if($result){
			return '1';
		}else{
			return '0';
		}
	}

	public function editMarketingNotification(){
		$id 				= $this->request->getVar('id');
		$data['content'] 	= $this->request->getVar('content');
		$data['link'] 		= $this->request->getVar('link');
		$time 				= $this->request->getVar('public_time');
		$public_time 		= \DateTime::createFromFormat('d/m/Y H:i', $time);
		$data['public_time'] = $public_time->format('Y-m-d H:i');

		$result = $this->setting->editMarketingNotification($id, $data);
		if($result){
			return '1';
		}else{
			return '0';
		}

	}

	public function deleteMktNoti(){
		$id 				= $this->request->getVar('id');
		$data['status'] 	= $this->request->getVar('status');

		$result = $this->setting->editMarketingNotification($id, $data);
		if($result){
			return '1';
		}else{
			return '0';
		}
	}

    public function birthdaySetting(){
        $data['panelTitle']	= 'TRANG QUẢN LÝ THÔNG BÁO SINH NHẬT';
        $data['user'] 		= session()->get('user');

        $data['birthdayNotify'] = $this->setting->getSettingValue(['settingType' => 'birthday', 'settingName' => 'birthday-notify-content']);
        $data['birthdaySMS'] = $this->setting->getSettingValue(['settingType' => 'birthday', 'settingName' => 'birthday-sms-content']);

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/marketing/birthdate');
        return view('AfterLogin/main', $data);
    }

    public function saveBirthdayNotifySetting(){
        $content = $this->request->getVar('birthdayNotifyContent');

        $data_notify_birthday = array(
            'settingType' 	=> 'birthday',
            'settingName' 	=> 'birthday-notify-content',
            'settingValue' 	=> $content
        );
        $this->setting->saveSettingValue($data_notify_birthday);

        return true;
    }

    public function saveBirthdaySMSSetting(){
        $content = $this->request->getVar('birthdaySMSContent');

        $data_sms_birthday = array(
            'settingType' 	=> 'birthday',
            'settingName' 	=> 'birthday-sms-content',
            'settingValue' 	=> $content
        );
        $this->setting->saveSettingValue($data_sms_birthday);

        return true;
    }

    public function birthdayNotification(){
        //nội dung thông báo trên app & tin nhắn SMS
        $notify = $this->setting->getSettingValue(['settingType' => 'birthday', 'settingName' => 'birthday-notify-content']);
        $sms = $this->setting->getSettingValue(['settingType' => 'birthday', 'settingName' => 'birthday-sms-content']);

        $birthday_user = $this->account->countUsers('sinh-nhat');

        foreach ($birthday_user as $k => $user){
            //sđt
            $phone = $user['value'];

            $this->send_notification($phone,$notify['settingValue']);
            $this->sendSMS($phone,'Doctor4U',$sms['settingValue']);
        }
    }

    public function activeUsers(){
        $data['panelTitle'] = 'Danh sách tài khoản hoạt động';
        $data['user'] 	= session()->get('user');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

        $allActiveUsers = $this->marketing->countAllActiveUsersList();
        if(!empty($allActiveUsers)){
            $totalUsers = count($allActiveUsers);
            service('pager')->makeLinks($page+1, $perPage, $totalUsers);
            $start = $page * $perPage;
            $data['posts'] = $this->marketing->getActiveUsersList($start, $perPage);

            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $totalUsers - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($totalUsers/$perPage);

        }else{
            $data['posts'] = false;
        }
        

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách tài khoản hoạt động | Doctor4U', 'AfterLogin/pages/marketing/activeUsers');
		return view('AfterLogin/main', $data);
    }

    public function updateNoteInfo(){
        $phone = $this->request->getVar('phone');
        $note = $this->request->getVar('note');

        return $this->marketing->updateNoteInfo($phone,$note);

    }

    public function commandMarketingNotification(){
		// Check time to get list marketing notifications
		$time = date('Y-m-d H:i:s');
		$result = $this->marketing->getListMarketingNotification($time);
		if(!empty($result)){
			foreach($result as $k => $noti){

				$dataNoti = array(
					'content' 	=> $noti['content'],
					'link'		=> $noti['link']
				);

				$result = $this->sendMarketingNotification($dataNoti);
				
				if(isset($result['uuid']) && $result['uuid'] != ''){
					$this->marketing->updateStatus($noti['id']);
				}
			}
		}
	}

    public function sendMarketingNotification($dataNotification){
		$data = array(
			'content' 	=> $dataNotification['content'],
			'link'		=> $dataNotification['link']
		);
		$result = $this->apiMarketingNotification($data);
		return $result;
	}
  
}
