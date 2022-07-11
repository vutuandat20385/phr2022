<?php

namespace App\Controllers;

use App\Services\SettingService;
use App\Forms\Forms;

class Settings extends BaseController {
    
    public function __construct(){
        $this->setting = new SettingService(); 
        $this->form = new Forms(); 
        
	}

    public function index() {
		$data['panelTitle'] = 'CẤU HÌNH HỆ THỐNG';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | CẤU HÌNH HỆ THỐNG | DOCTOR4U', 'AfterLogin/pages/setting/index');
		return view('AfterLogin/main', $data);
	}

    public function notification(){
        $data['panelTitle'] = 'CẤU HÌNH THÔNG BÁO';

        $data['user'] 	= session()->get('user');

        // Thông báo khi import kết quả khám sức khỏe
        $data['title_result_notification'] = $this->setting->getSettingValue(['settingType' => 'notification', 'settingName' => 'title-result-notification']);
        $data['content_result_notification'] = $this->setting->getSettingValue(['settingType' => 'notification', 'settingName' => 'content-result-notification']);

        // Thông báo khi Upload file PDF kết quả test COVID
        $data['title_uploadpdf_covid'] = $this->setting->getSettingValue(['settingType' => 'notification', 'settingName' => 'title-uploadpdf-covid']);
        $data['content_uploadpdf_covid'] = $this->setting->getSettingValue(['settingType' => 'notification', 'settingName' => 'content-uploadpdf-covid']);

        //SMS khi import KQXN
        $data['defaultSMSResult'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'default-sms-result']);
        $data['contentSMSResult_old'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-result-old']);
        $data['contentSMSResult_new'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-result-new']);

        //SMS khi import kết quả test covid
        $data['defaultSMSTestCovid'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'default-sms-test-covid']);
        $data['contentSMSTestCovid_old'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-test-covid-old']);
        $data['contentSMSTestCovid_new'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-test-covid-new']);

        //SMS khi Upload File kết quả test covid
        $data['defaultSMSUploadFileCovid'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'default-sms-uploadFile-covid']);
        $data['contentSMSUploadFileCovid_old'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-uploadfile-covid-old']);
        $data['contentSMSUploadFileCovid_new'] = $this->setting->getSettingValue(['settingType' => 'sms', 'settingName' => 'content-sms-uploadfile-covid-new']);

        // Notification tái khám
        $data['defaultNotifyFollow'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-notify-default']);
        $data['followNotifyContent'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-notify-content']);

        // SMS tái khám 
        $data['defaultSMSFollow'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-sms-default']);
        $data['followSMSContent'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-sms-content']);

        // Ngày nhắc tái khám
        $data['followDate1'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-date-1']);
        $data['followDate2'] = $this->setting->getSettingValue(['settingType' => 'follow', 'settingName' => 'follow-date-2']);

        // Nhắc nhở đơn thuốc
        $data['medicineRemindPhoneList'] = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'medicine-remind-phonelist']);
        $data['medicineRemindContent'] = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'medicine-remind-content']);

        // Nhắc nhở kết thúc tư vấn qua app
        $data['appCompleteRemindContent'] = $this->setting->getSettingValue(['settingType' => 'remind', 'settingName' => 'appointment-complete-remind-content']);

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | CẤU HÌNH THÔNG BÁO | DOCTOR4U', 'AfterLogin/pages/setting/notification');
		return view('AfterLogin/main', $data);
    }

    public function service(){
        $data['panelTitle'] = 'CẤU HÌNH DỊCH VỤ KHÁM';

        $data['user'] 	= session()->get('user');

        $data['allIndex'] = $this->setting->getAllServiceDefine();

        $data['parent'] = $this->setting->getServiceDefineParent();

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | CẤU HÌNH THÔNG BÁO | DOCTOR4U', 'AfterLogin/pages/setting/service');
		return view('AfterLogin/main', $data);
    }

    public function indexValue(){
		$data['panelTitle'] = 'CẤU HÌNH CHỈ SỐ CHUẨN';

        $data['user'] 	= session()->get('user');

        $data['allIndex'] = $this->setting->getGroupStandardIndex();

        foreach ($data['allIndex'] as $k => $parent){
            $data['allIndex'][$k]['child'] = $this->setting->getStandardIndexByGroup($parent['id']);
        }

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | CẤU HÌNH THÔNG BÁO | DOCTOR4U', 'AfterLogin/pages/setting/standard_value');
        return view('AfterLogin/main', $data);
    }

    public function providerManage(){
        
    }

    

    public function template(){
        $data['panelTitle'] = 'CẤU HÌNH TEMPLATE IMPORT';

        $data['user'] 	= session()->get('user');


        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | CẤU HÌNH TEMPLATE IMPORT | DOCTOR4U', 'AfterLogin/pages/setting/template');
		return view('AfterLogin/main', $data);
    }

    public function saveSettings(){
		$settingType = $this->request->getVar('settingType');

		switch ($settingType) {
			case 'sms':
				// Gửi SMS khi Import dữ liệu
					// Cấu hình mặc định
					$data_defaultSMSResult = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'default-sms-result',
						'settingValue' 	=> $this->request->getVar('defaultSMSResult')
					);
					$this->setting->saveSettingValue($data_defaultSMSResult);

					// Nội dung sms tài khoản cũ
					$data_contentSMSResult_old = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'content-sms-result-old',
						'settingValue' 	=> $this->request->getVar('contentSMSResult_old')
					);
					$this->setting->saveSettingValue($data_contentSMSResult_old);

					// Nội dung sms tài khoản mới
					$data_contentSMSResult_new = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'content-sms-result-new',
						'settingValue' 	=> $this->request->getVar('contentSMSResult_new')
					);
					$this->setting->saveSettingValue($data_contentSMSResult_new);

				// Gửi SMS khi Import dữ liệu testCovid
					// Cấu hình mặc định
					$data_defaultSMSTestCovid = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'default-sms-test-covid',
						'settingValue' 	=> $this->request->getVar('defaultSMSTestCovid')
					);
					$this->setting->saveSettingValue($data_defaultSMSTestCovid);

					// Nội dung sms tài khoản cũ
					$data_contentSMSTestCovid_old = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'content-sms-test-covid-old',
						'settingValue' 	=> $this->request->getVar('contentSMSTestCovid_old')
					);
					$this->setting->saveSettingValue($data_contentSMSTestCovid_old);

					// Nội dung sms tài khoản mới
					$data_contentSMSTestCovid_new = array(
						'settingType' 	=> 'sms',
						'settingName' 	=> 'content-sms-test-covid-new',
						'settingValue' 	=> $this->request->getVar('contentSMSTestCovid_new')
					);
					$this->setting->saveSettingValue($data_contentSMSTestCovid_new);

				// Gửi SMS khi upload file ket qua testCovid
				// Cấu hình mặc định
				$data_defaultSMSUploadFileCovid = array(
					'settingType' 	=> 'sms',
					'settingName' 	=> 'default-sms-uploadFile-covid',
					'settingValue' 	=> $this->request->getVar('defaultSMSUploadFileCovid')
				);
				$this->setting->saveSettingValue($data_defaultSMSUploadFileCovid);

				// Nội dung cho tài khoản cũ
				$data_contentSMSUploadFileCovid_old = array(
					'settingType' 	=> 'sms',
					'settingName' 	=> 'content-sms-uploadfile-covid-old',
					'settingValue' 	=> $this->request->getVar('contentSMSUploadFileCovid_old')
				);
				$this->setting->saveSettingValue($data_contentSMSUploadFileCovid_old);

				// Nội dung cho tài khoản mới
				$data_contentSMSUploadFileCovid_new = array(
					'settingType' 	=> 'sms',
					'settingName' 	=> 'content-sms-uploadfile-covid-new',
					'settingValue' 	=> $this->request->getVar('contentSMSUploadFileCovid_new')
				);
				$this->setting->saveSettingValue($data_contentSMSUploadFileCovid_new);

				break;
		
			case 'notification':
				//Gửi Notification khi import dữ liệu
				$data_titleNotification = array(
					'settingType' 	=> 'notification',
					'settingName' 	=> 'title-result-notification',
					'settingValue' 	=> $this->request->getVar('resultTitleNotificationValue')
				);
				$this->setting->saveSettingValue($data_titleNotification);

				$data_contentNotification = array(
					'settingType' 	=> 'notification',
					'settingName' 	=> 'content-result-notification',
					'settingValue' 	=> $this->request->getVar('resultContentNotificationValue')
				);
				$this->setting->saveSettingValue($data_contentNotification);

				// Thông báo khi upload file PDF kết quả Test COVID
				$data_titleUploadpdfCovid = array(
					'settingType' 	=> 'notification',
					'settingName' 	=> 'title-uploadpdf-covid',
					'settingValue' 	=> $this->request->getVar('titleUploadpdfCovid')
				);
				$this->setting->saveSettingValue($data_titleUploadpdfCovid);

				$data_contentUploadpdfCovid = array(
					'settingType' 	=> 'notification',
					'settingName' 	=> 'content-uploadpdf-covid',
					'settingValue' 	=> $this->request->getVar('contentUploadpdfCovid')
				);
				$this->setting->saveSettingValue($data_contentUploadpdfCovid);

				break;

			case 'other':
				$data_other = array(
					'settingType' 	=> $this->request->getVar('settingType'),
					'settingName' 	=> $this->request->getVar('settingName'),
					'settingValue' 	=> $this->request->getVar('settingValue')
				);
				$this->setting->saveSettingValue($data_other);
				break;
			case 'ehc':
				$data_token = array(
					'settingType' 	=> 'ehc',
					'settingName' 	=> $this->request->getVar('settingName_token'),
					'settingValue' 	=> $this->request->getVar('settingValue_token')
				);
				$this->setting->saveSettingValue($data_token);

				$data_username = array(
					'settingType' 	=> 'ehc',
					'settingName' 	=> $this->request->getVar('settingName_username'),
					'settingValue' 	=> $this->request->getVar('settingValue_username')
				);
				$this->setting->saveSettingValue($data_username);

				$data_password = array(
					'settingType' 	=> 'ehc',
					'settingName' 	=> $this->request->getVar('settingName_password'),
					'settingValue' 	=> $this->request->getVar('settingValue_password')
				);
				$this->setting->saveSettingValue($data_password);
				break;

			case 'follow':
				// SMS nhắc lịch tái khám
				$data_followSMSDefault = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-sms-default',
					'settingValue' 	=> $this->request->getVar('defaultFollowSMS')
				);
				$this->setting->saveSettingValue($data_followSMSDefault);

				$data_followSMSContent = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-sms-content',
					'settingValue' 	=> $this->request->getVar('contentFollowSMS')
				);
				$this->setting->saveSettingValue($data_followSMSContent);

				// Notify nhắc lịch tái khám
				$data_followNotifyDefault = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-notify-default',
					'settingValue' 	=> $this->request->getVar('defaultFollowNotify')
				);
				$this->setting->saveSettingValue($data_followNotifyDefault);

				$data_followNotifyContent = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-notify-content',
					'settingValue' 	=> $this->request->getVar('contentFollowNotify')
				);
				$this->setting->saveSettingValue($data_followNotifyContent);

				// Ngày nhắc
				$data_followDate1 = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-date-1',
					'settingValue' 	=> $this->request->getVar('followDate1')
				);
				$this->setting->saveSettingValue($data_followDate1);

				$data_followDate2 = array(
					'settingType' 	=> 'follow',
					'settingName' 	=> 'follow-date-2',
					'settingValue' 	=> $this->request->getVar('followDate2')
				);
				$this->setting->saveSettingValue($data_followDate2);
				break;
			default:
				# code...
				break;
		}

		return true;
	}

    public function saveMedicineRemindSetting(){
		$phoneList = $this->request->getVar('phoneList');
		$contentRemind = $this->request->getVar('contentRemind');

		$data_medicineRemindPhoneList = array(
			'settingType' 	=> 'remind',
			'settingName' 	=> 'medicine-remind-phonelist',
			'settingValue' 	=> $phoneList
		);
		$this->setting->saveSettingValue($data_medicineRemindPhoneList);

		$data_medicineRemindContent = array(
			'settingType' 	=> 'remind',
			'settingName' 	=> 'medicine-remind-content',
			'settingValue' 	=> $contentRemind
		);
		$this->setting->saveSettingValue($data_medicineRemindContent);

		return true;
	}

    public function saveAppCompleteRemindSetting(){
		$appContentRemind = $this->request->getVar('appContentRemind');

		$data_appContentRemind = array(
			'settingType' 	=> 'remind',
			'settingName' 	=> 'appointment-complete-remind-content',
			'settingValue' 	=> $appContentRemind
		);
		$this->setting->saveSettingValue($data_appContentRemind);

		return true;
	}

    public function addNewService() {
        $data = [
            'name' => $this->request->getVar('name'),
            'codeName' => $this->request->getVar('codeName'),
            'parentId' => $this->request->getVar('parent')
        ];

        //ADD
        $result = $this->setting->newServiceDefine($data);

        if ($result){
            return true;
        } else {
            return false;
        }
    }

    public function editService($id){
        $data = [
            'name' => $this->request->getVar('name'),
            'codeName' => $this->request->getVar('codeName'),
            'parentId' => $this->request->getVar('parent')
        ];

        //UDPATE
        $result = $this->setting->updateServiceDefine($id,$data);

        if ($result){
            return true;
        } else {
            return false;
        }
    }

    public function deleteService($id){
        $result = $this->setting->deleteServiceDefine($id);

        if ($result){
            return true;
        } else {
            return false;
        }
    }

    public function restoreService($id){
        $result = $this->setting->restoreServiceDefine($id);

        if ($result){
            return true;
        } else {
            return false;
        }
    }

}
