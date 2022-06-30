<?php
namespace App\Services;

use App\Models\UserActiveNoteModel;

class MarketingService extends BaseService{
    public function __construct(){
        $this->db = \Config\Database::connect();
        $this->userActive = new UserActiveNoteModel();
    }

    public function getActiveUsersList($start, $limit){
    
        $query = "select pa.value, pn.given_name, ual.date_created as last_active, uan.note from user_active_log as ual
            left join users as u ON u.username = ual.user_name
            left join person as p ON p.person_id = u.person_id
            left join person_name as pn ON p.person_id = pn.person_id
            left join person_attribute as pa ON p.person_id = pa.person_id
            left join user_active_note as uan ON uan.phoneNumber = pa.value
            where u.retired=0 AND ual.phone_number NOT IN (SELECT pa.value FROM provider pro LEFT JOIN person_attribute pa ON pa.person_id = pro.person_id WHERE pro.retired = 0)
            group by ual.phone_number
            ORDER BY ual.date_created DESC ";

        if($start !== '' && $limit !== ''){
            $query .= "LIMIT ".$start.",".$limit;
        }

        $activeList = $this->db->query($query);
        return $activeList->getResultArray();
    }

    public function updateNoteInfo($phone,$note){
        $dataUser = session()->get('user');
        // Check phoneNumber
        $checkPhoneNumber = $this->userActive->where(['phoneNumber' => $phone])->first();
        if(!empty($checkPhoneNumber)){
            $data = array(
                'note' => $note,
                'user_update' => $dataUser['user_id'],
                'username_update' => $dataUser['username']
            );
            return $this->userActive->set($data)->where(['phoneNumber' => $phone])->update();
        }else{
            $data = array(
                'phoneNumber' => $phone,
                'note' => $note,
                'user_update' => $dataUser['user_id'],
                'username_update' => $dataUser['username']
            );
            return $this->userActive->save($data);
        }
    }

    public function getAllMarketingNotification($data){
        $query = "SELECT * FROM manage_marketing_notifications WHERE status >= 0";
        if($data['info'] != ''){
            $query .= " AND content like '%".$data['info']."%'";
        }

        if($data['start'] != ''){
            $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $data['start']);
            $query .= " AND public_time >= '".$newDateStart->format('Y-m-d H:i')."'";
            // log_message('error', $newDateStart->format('Y-m-d H:i'));
        }

        if($data['end'] != ''){
            $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $data['end']);
            $query .= " AND public_time <= '".$newDateEnd->format('Y-m-d H:i')."'";
            // log_message('error', $newDateEnd->format('Y-m-d H:i'));
        }

        $query .= " ORDER BY status DESC, public_time DESC";
        log_message('error', $query);
        return $this->db->query($query)->getResultArray(); 
    }

    public function getMarketingNotification($data, $start, $limit){
        $query = "SELECT * FROM manage_marketing_notifications WHERE status >= 0";
        if($data['info'] != ''){
            $query .= " AND content like '%".$data['info']."%'";
        }

        if($data['start'] != ''){
            $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $data['start']);
            $query .= " AND public_time >= '".$newDateStart->format('Y-m-d H:i')."'";
            // log_message('error', $newDateStart->format('Y-m-d H:i'));
        }

        if($data['end'] != ''){
            $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $data['end']);
            $query .= " AND public_time <= '".$newDateEnd->format('Y-m-d H:i')."'";
            // log_message('error', $newDateEnd->format('Y-m-d H:i'));
        }

        $query .= " ORDER BY status DESC, public_time DESC LIMIT ".$start.",".$limit;
        log_message('error', $query);
        return $this->db->query($query)->getResultArray(); 
    }

}