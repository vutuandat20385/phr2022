<?php
namespace App\Services;
use App\Models\User2Model;
use App\Models\ProviderManageModel;
use App\Models\DeviceTokenRemindModel;
use App\Models\AppointmentRemindStatusModel;

class RemindService extends BaseService{

    public function __construct(){

        $this->user2 = new User2Model();
        $this->providerManage = new ProviderManageModel();
        $this->deviceToken = new DeviceTokenRemindModel();
        $this->remindStatus = new AppointmentRemindStatusModel();
        $this->db = \Config\Database::connect();
    }

    public function getAppointmentList($start, $perPage){  

        $startDate = date('Y-m-d 00:00:01', strtotime('-1 week'));

        $sql = "SELECT aa.appointment_code, patient_name.given_name as patient_name, patient_info.`value` as patient_phone, provider.provider_id, provider_name.given_name as provider_name, provider_info.`value` as provider_phone, ats.start_date, ats.end_date, aa.`status` FROM appointmentscheduling_appointment as aa 
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users creator ON creator.user_id = ats.creator
				LEFT JOIN provider ON provider.person_id = creator.person_id
				
                LEFT JOIN person_name patient_name ON patient_name.person_id = aa.patient_id
				LEFT JOIN person_attribute patient_info ON patient_info.person_id = aa.patient_id
				
				LEFT JOIN person_name provider_name ON provider_name.person_id = creator.person_id
				LEFT JOIN person_attribute provider_info ON provider_info.person_id = creator.person_id
				
                WHERE (aa.`status` = 'SCHEDULED' OR aa.`status` = 'INCONSULTATION') AND ats.start_date >= '$startDate' 
                GROUP BY aa.appointment_code ORDER BY ats.start_date DESC LIMIT $start, $perPage";

                log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray(); 
        if(!empty($result)){
            foreach($result as $k => $res){
                switch ($res['status']) {
                    case 'SCHEDULED':
                        $result[$k]['status'] = 'Sắp diễn ra';
                        break;
                    case 'INCONSULTATION':
                        $result[$k]['status'] = 'Đang diễn ra';
                        break;
                    default:
                        # code...
                        break;
                }
            }

            return $result;
        }else
            return false;
        return $result;
    }

    public function getAllAppointmentList(){  

        $startDate = date('Y-m-d 00:00:01', strtotime('-1 week'));

        $sql = "SELECT aa.appointment_code, patient_name.given_name as patient_name, patient_info.`value` as patient_phone, provider.provider_id, provider_name.given_name as provider_name, provider_info.`value` as provider_phone, ats.start_date, ats.end_date, aa.`status` FROM appointmentscheduling_appointment as aa 
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users creator ON creator.user_id = ats.creator
				LEFT JOIN provider ON provider.person_id = creator.person_id
				
                LEFT JOIN person_name patient_name ON patient_name.person_id = aa.patient_id
				LEFT JOIN person_attribute patient_info ON patient_info.person_id = aa.patient_id
				
				LEFT JOIN person_name provider_name ON provider_name.person_id = creator.person_id
				LEFT JOIN person_attribute provider_info ON provider_info.person_id = creator.person_id
				
                WHERE (aa.`status` = 'SCHEDULED' OR aa.`status` = 'INCONSULTATION') AND ats.start_date >= '$startDate' 
                GROUP BY aa.appointment_code ORDER BY ats.start_date DESC";

                log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray(); 
        if(!empty($result)){
            foreach($result as $k => $res){
                switch ($res['status']) {
                    case 'SCHEDULED':
                        $result[$k]['status'] = 'Sắp diễn ra';
                        break;
                    case 'INCONSULTATION':
                        $result[$k]['status'] = 'Đang diễn ra';
                        break;
                    default:
                        # code...
                        break;
                }
            }

            return $result;
        }else
            return false;
        return $result;
    }

    public function getAppointmentManage($userID, $start, $perPage){

        $startDate = date('Y-m-d 00:00:01', strtotime('-1 week'));
        
        $sql = "SELECT aa.appointment_code, patient_name.given_name as patient_name, patient_info.`value` as patient_phone, provider.provider_id, provider_name.given_name as provider_name, provider_info.`value` as provider_phone, ats.start_date, ats.end_date, aa.`status` FROM appointmentscheduling_appointment as aa 
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users creator ON creator.user_id = ats.creator
				LEFT JOIN provider ON provider.person_id = creator.person_id
				
                LEFT JOIN person_name patient_name ON patient_name.person_id = aa.patient_id
				LEFT JOIN person_attribute patient_info ON patient_info.person_id = aa.patient_id
				
				LEFT JOIN person_name provider_name ON provider_name.person_id = creator.person_id
				LEFT JOIN person_attribute provider_info ON provider_info.person_id = creator.person_id
				
                WHERE (aa.`status` = 'SCHEDULED' OR aa.`status` = 'INCONSULTATION') AND ats.start_date >= '$startDate' AND provider.person_id in (SELECT provider.person_id FROM provider_manage 
                        LEFT JOIN provider ON provider.provider_id = provider_manage.provider_id ";
            if($userID != 1){
                $sql .= " WHERE admin_id = $userID" ; 
            }    
            $sql .= " ) GROUP BY aa.appointment_code ORDER BY ats.start_date DESC LIMIT $start, $perPage";
            // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray(); 
        if(!empty($result)){
            foreach($result as $k => $res){
                switch ($res['status']) {
                    case 'SCHEDULED':
                        $result[$k]['status'] = 'Sắp diễn ra';
                        break;
                    case 'INCONSULTATION':
                        $result[$k]['status'] = 'Đang diễn ra';
                        break;
                    default:
                        # code...
                        break;
                }
                
            }

            return $result;
        }else
            return false;
        
    }

    public function getAllAppointmentManage($userID){

        $startDate = date('Y-m-d 00:00:01', strtotime('-1 week'));
        
        $sql = "SELECT aa.appointment_code, patient_name.given_name as patient_name, patient_info.`value` as patient_phone, provider.provider_id, provider_name.given_name as provider_name, provider_info.`value` as provider_phone, ats.start_date, ats.end_date, aa.`status` FROM appointmentscheduling_appointment as aa 
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users creator ON creator.user_id = ats.creator
				LEFT JOIN provider ON provider.person_id = creator.person_id
				
                LEFT JOIN person_name patient_name ON patient_name.person_id = aa.patient_id
				LEFT JOIN person_attribute patient_info ON patient_info.person_id = aa.patient_id
				
				LEFT JOIN person_name provider_name ON provider_name.person_id = creator.person_id
				LEFT JOIN person_attribute provider_info ON provider_info.person_id = creator.person_id
				
                WHERE (aa.`status` = 'SCHEDULED' OR aa.`status` = 'INCONSULTATION') AND ats.start_date >= '$startDate' AND provider.person_id in (SELECT provider.person_id FROM provider_manage 
                        LEFT JOIN provider ON provider.provider_id = provider_manage.provider_id ";
            if($userID != 1){
                $sql .= " WHERE admin_id = $userID" ; 
            }    
            $sql .= " ) GROUP BY aa.appointment_code ORDER BY ats.start_date DESC";
            // log_message('error', $sql);
        $appointment = $this->db->query($sql);
        $result = $appointment->getResultArray(); 
        if(!empty($result)){
            foreach($result as $k => $res){
                switch ($res['status']) {
                    case 'SCHEDULED':
                        $result[$k]['status'] = 'Sắp diễn ra';
                        break;
                    case 'INCONSULTATION':
                        $result[$k]['status'] = 'Đang diễn ra';
                        break;
                    default:
                        # code...
                        break;
                }
                
            }

            return $result;
        }else
            return false;
        
    }

    public function addUser2($data){
        // check username exits
        $check = $this->user2->where(['username' => $data['username']])->first();
        if(empty($check)){
            $this->user2->save($data);
            return array(
                'status'    => 'Success'
            );
        }else{
            return array(
                'status'    => 'Username đã tồn tại'
            );
        }
    }

    public function checkUser2($data){
        $check = $this->user2->where(['username' => $data['username']])->first();
        if(!empty($check)){     
            if($check['password'] == hash('sha512',$data['password'].$check['salt'])){
                unset($check['password']);
                unset($check['salt']);
                unset($check['create_date']);
                unset($check['modify_date']);
                // Check token
                $checkToken = $this->deviceToken->where(['admin_id' => $check['id'], 'token' => $data['device_token']])->first();
                if(empty($checkToken)){
                    $dataToken = array(
                        'admin_id' => $check['id'],
                        'token' => $data['device_token']
                    );
                    $this->deviceToken->save($dataToken);
                }
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
                'msg'       => 'Tên đăng nhập không tồn tại',
                'user'      => null
            );
        }

        
    }

    public function getMedicineRemind($status, $timeCheck){
        $query = "SELECT aa.appointment_code, pn.given_name, ats.start_date, ats.end_date FROM appointmentscheduling_appointment aa 
                LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                LEFT JOIN users u ON u.user_id = ats.creator
                LEFT JOIN person_name pn ON pn.person_id = u.person_id
                WHERE aa.status = '".$status."' AND aa.date_changed >= '".$timeCheck."'";

        $remind =  $this->db->query($query);
        $result =  $remind->getResultArray();
        return $result;
    }

    public function getAllAppointment($status, $date){
        $query = "SELECT aa.appointment_id, aa.appointment_code, aa.visit_id, aa.patient_id, aa.time_slot_id, pa.value 
                    FROM appointmentscheduling_appointment aa 
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    LEFT JOIN users u ON u.user_id = ats.creator
                    LEFT JOIN person_attribute pa ON pa.person_id = u.person_id 
                    WHERE  aa.status = '".$status."' AND aa.date_created >= '".$date." 00:00:01' AND aa.date_created <= '".$date." 23:59:59'
                    GROUP BY pa.`value`";
        log_message('error', $query);
        return $this->db->query($query)->getResultArray();
    }

    public function getUser2Info($user_id){
        return $this->user2->where(['id' => $user_id])->first();
    }

    public function getAllManageProvider(){
        $allManager = $this->providerManage->where(['retired' => 0])->groupBy('admin_id')->findAll();
        if(!empty($allManager)){
            foreach( $allManager as $k => $manager){
                $admin_id = $manager['admin_id'];
                $providerArray = [];
                $allProvider = $this->providerManage->where(['admin_id' => $admin_id, 'retired' => 0])->findAll();
                if(!empty($allProvider)){
                    foreach($allProvider as $p){
                        array_push($providerArray, $p['provider_id']);
                    }
                }

                $allManager[$k]['provider'] = implode(',', $providerArray);
            }
        }

        return $allManager;
    }

    public function getApointmentByProvider(){
        $startDate = date('Y-m-d 00:00:01');
        $endDate = date('Y-m-d 23:59:59');
        $query = "SELECT aa.appointment_id, aa.appointment_code, aa.time_slot_id, aa.`status`, aa.date_changed, ats.creator, ats.start_date
                    FROM appointmentscheduling_appointment aa 
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    WHERE (ats.start_date >= '".$startDate."' AND ats.start_date <= '".$endDate."') AND aa.status = 'SCHEDULED'" ; //AND ats.creator = ".$provider
         
        $remind =  $this->db->query($query);
        $result =  $remind->getResultArray();
        return $result;
    }

    public function getTuVanSapDienRa($time){
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime('+'.$time.' minute'));
        $query = "SELECT aa.appointment_id, aa.appointment_code, aa.time_slot_id, aa.`status`, aa.date_changed, ats.creator, ats.start_date
                    FROM appointmentscheduling_appointment aa 
                    LEFT JOIN appointmentscheduling_time_slot ats ON ats.time_slot_id = aa.time_slot_id
                    WHERE (ats.start_date >= '".$startDate."' AND ats.start_date <= '".$endDate."') AND aa.status = 'SCHEDULED'" ; //AND ats.creator = ".$provider
        log_message('error', $query);
        $remind =  $this->db->query($query);
        $result =  $remind->getResultArray();
        return $result;
    }

    public function getAllToken($user_id){
        return $this->deviceToken->where(['admin_id' => $user_id])->findAll();
    }

    public function saveAppointmentRemindStatus($data){
        $check = $this->remindStatus->where($data)->first();
        if(empty($check)){
            
            return $this->remindStatus->save($data);
        }else{
            return true;
        }
        
    }

    public function checkRemind($appointment_id, $admin_id){
        $check = $this->remindStatus->where(['appointment_id' => $appointment_id, 'admin_id' => $admin_id])->first();
        if(empty($check)){
            return true;
        }else
            return false;
    }

}