<?php
namespace App\Services;

use App\Models\AnnualCheckupModel;
use App\Models\CovidTestModel;
use App\Models\DeviceTokenModel;
use App\Models\GroupAnnualCheckupModel;
use App\Models\SettingsModel;
use App\Models\UserModel;

class ImportService extends BaseService{

    public function __construct(){
        $this->annual           = new AnnualCheckupModel();
        $this->groupAnnual      = new GroupAnnualCheckupModel();
        $this->covid            = new CovidTestModel();     
        $this->user             = new UserModel();
        $this->token            = new DeviceTokenModel();
        $this->setting          = new SettingsModel();
        $this->settingService   = new SettingService();
        
        $this->db = \Config\Database::connect();
    }
    
    public function checkUserApi($data){
        return $this->user->where($data)->first();
    }

    public function getDeviceToken($data){
        return $this->token->where($data)->find();
    }

    public function content_notification($regId, $osType, $hospital, $date, $insert_id, $sdt){

        //Lấy tiêu đề & nội dung thông báo theo Cấu hình Notification
        $dataTitleNotification = array(
            'settingType' => 'notification',
            'settingName' => 'title-result-notification'
        );
        $arr_title = $this->settingService->getSettingValue($dataTitleNotification);
        $titleNotification = $arr_title['settingValue'];

        $dataContentNotification = array(
            'settingType' => 'notification',
            'settingName' => 'content-result-notification'
        );
        $arr_content = $this->settingService->getSettingValue($dataContentNotification);
        $contentNotification = str_replace('[benh-vien]', $hospital, $arr_content['settingValue']);
        $contentNotification = str_replace('[ngay]', $date, $contentNotification);

        $NotificationArray = array();
        $NotificationArray["content_available"] = true;
        $NotificationArray["title"] = $titleNotification;
        $NotificationArray["body"]  = $contentNotification;
        $NotificationArray["sound"] = "default";
        // $NotificationArray["badge"] = 1;
        $data = array(
            'data' => $sdt,
            'type' => 'notification'
        );

        $result = $this->send_push_notification($regId, $NotificationArray, $osType, $data);
    }

    public function send_push_notification($regId, $notification, $device_type, $data) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        if ($device_type == "android") {
            $fields = array(
                'content_available' => true,
                'to'                => $regId,
                'notification'      => $notification,
                'data'              => $data
            );
        } else {
            $fields = array(
                'content_available' => true,
                'to'                => $regId,
                'notification'      => $notification,
                'data'              => $data
            );
        }
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

        // dd($result);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    public function send_notification($sdt, $hospital, $date, $insert_id){

        // Dùng số điện thoại để lấy thông tin user
        $dataUser = array(
            'username' => $sdt,
        );
        $user = $this->checkUserApi($dataUser);

        // Lấy tất cả Device Token (ứng với tất cả các điện thoại user đó đã dùng để đăng nhập app)
        $dataCheckDevice = array(
            'creator'   => $user['user_id'],
            'voided'    => 0
        );
        $rs_token = $this->getDeviceToken($dataCheckDevice);

        // Trường hợp tồn tại ít nhất 1 Device Token => gửi Notification
        if (count($rs_token) > 0) {
            foreach ($rs_token as $val) {
                $this->content_notification($val['token'], $val['os_type'], $hospital, $date, $insert_id, $sdt);
            }
        }
    }

    public function getSMSContent($settingName, $settingType, $data){

        $content = $this->setting->where(['settingName' => $settingName, 'settingType' => $settingType])->first();
        $smsContent = $content['settingValue'];
        if (isset($data['hospital']) && $data['hospital'] != '') {
            $smsContent = str_replace('[benh-vien]', $data['hospital'], $smsContent);
        }

        if (isset($data['date']) && $data['date'] != '') {
            $smsContent = str_replace('[ngay]', $data['date'], $smsContent);
        }

        if (isset($data['ketqua'])) {
            $smsContent = str_replace('[ketqua]', $data['ketqua'], $smsContent);
        }

        return $smsContent;
    }

    public function addPHR($phr){
        // Check
        $check = $this->annual->where(['phone_number' => $phr['phone_number'], 'examination_date' => $phr['examination_date'], 'Hospital' => $phr['hospital']])->first();
        if ($check == null) {
            $add = $this->annual->save($phr);
            if ($add) {
                return array(
                    'status'    => true,
                    'msg'       => 'Thêm thông tin thành công',
                    'id'        => $this->annual->getInsertID()
                );
            } else {
                return array(
                    'status'    => false,
                    'msg'       => 'Lỗi không xác định',
                    'id'        => ''
                );
            }
        } else
            return array(
                'status'    => false,
                'msg'       => 'Bệnh nhân đã có kết quả khám bệnh trong ngày.',
                'id'        => ''
            );
    }

    public function updatePHR($id, $data){
        return $this->annual->where(['annual_checkup_id' => $id])->set($data)->update();
    }

    public function addPHRGroup($phr){
        $check = $this->groupAnnual->where(['phone_number' => $phr['phone_number'], 'examination_date' => $phr['examination_date'], 'Hospital' => $phr['hospital']])->first();
        if ($check == null) {
            $add = $this->groupAnnual->save($phr);
            if ($add) {
                return array(
                    'status'    => true,
                    'msg'       => 'Thêm thông tin thành công',
                    'id'        => $this->groupAnnual->getInsertID()
                );
            } else {
                return array(
                    'status'    => false,
                    'msg'       => 'Lỗi không xác định',
                    'id'        => ''
                );
            }
        } else
            return array(
                'status'    => false,
                'msg'       => 'Bệnh nhân đã có kết quả khám bệnh trong ngày.',
                'id'        => ''
            );
    }

    public function addCovidTest($dataInsert){
        $check = $this->covid->where(['patient_id' => $dataInsert['patient_id'], 'date' => $dataInsert['date'], 'type' => $dataInsert['type']])->first();
        if ($check == null) {
            // dd($dataInsert);

            $result = $this->covid->save($dataInsert);

            if ($result) {
                return array(
                    'result' => true,
                    'msg' => 'Thêm kết quả thành công',
                    'id'    => $this->db->insertID()
                );
            } else {
                return array(
                    'result' => false,
                    'msg' => 'Lỗi không xác định!',
                    'id' => ''
                );
            }
        } else {
            return array(
                'result'    => false,
                'msg'       => 'Bệnh nhân đã có kết quả test COVID trong ngày',
                'id' => ''
            );
        }
    }
}