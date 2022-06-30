<?php
namespace App\Services;

use App\Models\AnnualCheckupModel;
use App\Models\GroupAnnualCheckupModel;
use App\Models\StandardValueModel;
use App\Models\UserModel;
use App\Models\DeviceTokenModel;

class PHRService extends BaseService{

    public function __construct(){

        $this->annual = new AnnualCheckupModel();
        $this->groupAnnual = new GroupAnnualCheckupModel();
        $this->standard = new StandardValueModel();
		$this->user = new UserModel();
		$this->token = new DeviceTokenModel();

        $this->db = \Config\Database::connect();
    }

    public function getAllD4USinglePHR($data){

        $query = "SELECT ac.annual_checkup_id, ac.user_id, ac.full_name, p.birthdate, p.gender, ac.phone_number, CONCAT(pad.state_province ,',' ,pad.city_village) as address, ac.Hospital, ac.hospital_id, ac.Occupation, ac.examination_date FROM annual_checkup ac
                    LEFT JOIN users u ON u.user_id = ac.user_id 
                    LEFT JOIN person p ON p.person_id = u.person_id
                    LEFT JOIN person_address pad ON pad.person_id = u.person_id
                    WHERE (hospital_id = 'd4u' AND (Occupation = 'auto' OR Occupation is null) )";
        if ($data['info'] !== '') {
            $query .= " AND (full_name like '%".$data['info']."%' OR phone_number like '%".$data['info']."%')";
        }

        $query .= " GROUP BY phone_number";

        if($data['start'] !== '' && $data['limit'] !== ''){
            $query .= "  LIMIT ".$data['start'].",".$data['limit'];
        }
        
        $allPHR = $this->db->query($query)->getResultArray();

        if (!empty($allPHR)) {

            foreach ($allPHR as $k => $phr) {
                $allPHR[$k]['history']      = $this->getPatientPHR('khach-le', $phr['user_id'], 'd4u');
                $allPHR[$k]['lastCheckup']  = $this->getPatientLastCheckup('khach-le', $phr['user_id'], 'd4u');
              
            }
        }

        return $allPHR;
    }

    public function getAllD4UGroupPHR($data){

        $query = "SELECT ac.annual_checkup_id, ac.user_id, ac.full_name, p.birthdate, p.gender, ac.phone_number, CONCAT(pad.state_province ,',' ,pad.city_village) as address, ac.Hospital, ac.hospital_id, ac.Occupation, ac.examination_date FROM group_annual_checkup ac
                    LEFT JOIN users u ON u.user_id = ac.user_id 
                    LEFT JOIN person p ON p.person_id = u.person_id
                    LEFT JOIN person_address pad ON pad.person_id = u.person_id
                    WHERE (hospital_id = 'd4u')";
        if ($data['info'] !== '') {
            $query .= " AND (full_name like '%".$data['info']."%' OR phone_number like '%".$data['info']."%')";
        }

        $query .= " GROUP BY phone_number";

        if($data['start'] !== '' && $data['limit'] !== ''){
            $query .= "  LIMIT ".$data['start'].",".$data['limit'];
        }
        
        $allPHR = $this->db->query($query)->getResultArray();

        if (!empty($allPHR)) {

            foreach ($allPHR as $k => $phr) {
                $allPHR[$k]['history']      = $this->getPatientPHR('khach-doan', $phr['user_id'], 'd4u');
                $allPHR[$k]['lastCheckup']  = $this->getPatientLastCheckup('khach-doan', $phr['user_id'], 'd4u');
              
            }
        }

        return $allPHR;
    }

    public function getPatientPHR($type, $userId, $hospital_id){
        if ($type == 'khach-le') {
            $patientPHR = $this->annual->select('annual_checkup_id, user_id, phone_number, examination_date')
                ->where(['user_id' => $userId, 'hospital_id' => $hospital_id])
                ->orderBy('examination_date', 'DESC')
                ->findAll();
        } else {
            $patientPHR = $this->groupAnnual->select('annual_checkup_id, user_id, phone_number, examination_date')
                ->where(['user_id' => $userId, 'hospital_id' => $hospital_id])
                ->orderBy('examination_date', 'DESC')
                ->findAll();
        }

        if ($patientPHR != []) {
            return $patientPHR;
        } else
            return false;
    }

    public function getPatientLastCheckup($type, $user_id, $hospital_id){
        if ($type == 'khach-le') {
            $annualExaminationDate = $this->annual->select('examination_date')
                ->where(['user_id' => $user_id, 'hospital_id' => $hospital_id])
                ->orderBy('examination_date', 'DESC')
                ->first();
        } else {
            $annualExaminationDate = $this->groupAnnual->select('examination_date')
                ->where(['user_id' => $user_id, 'hospital_id' => $hospital_id])
                ->orderBy('examination_date', 'DESC')
                ->first();
        }

        return strtotime($annualExaminationDate['examination_date']);
    }

    public function getVisitInfo($type, $visitId){
        $hostpitalId = 'd4u';
        if ($type == 'khach-le') {
            return array(
                'hospital_id' => $hostpitalId,
                'info' => $this->annual->where(['annual_checkup_id' => $visitId, 'hospital_id' => $hostpitalId])->first()
            );
        } else if ($type == 'khach-doan') {
            return array(
                'hospital_id' => $hostpitalId,
                'info' => $this->groupAnnual->where(['annual_checkup_id' => $visitId, 'hospital_id' => $hostpitalId])->first()
            );
        }
    }

    public function showIndex($indexValue, $shortName, $gender, $key){	
		$standardValue = $this->standard->where(['shortName' => $shortName, 'gender' => $gender])->first();
		$parent = $this->standard->where(['id' => $standardValue['parentId']])->first();		
		$standardValue['parent'] = $parent['shortName'];
		switch ($standardValue['parent']) {
			case 'khamLamSan':
				$result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 text-primary">'.$indexValue.'</span>';
				break;
			case 'chuanDoanHinhAnh':
				$result = '<div class="col-md-12"><span class="">'.$standardValue['fullName'].': </span><span class="text-primary">'.$indexValue.'</span></div>';
				break;
			case 'thamDoChucNang':
				$result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 text-primary">'.$indexValue.'</span>';
				break;
			case 'hoaSinhMienDich':
				$min = ($standardValue['min']) ? ($standardValue['min']) : -9999;
				$max = ($standardValue['max']) ? ($standardValue['max']) : +9999;
				if($indexValue <= $min || $indexValue >= $max){
					$classValue = 'text-danger';
				}else{
					$classValue = '';
				}
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center '.$classValue.'">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'soiTuoiAmDao':
				$min = ($standardValue['min']) ? ($standardValue['min']) : -9999;
				$max = ($standardValue['max']) ? ($standardValue['max']) : +9999;
				if($indexValue <= $min || $indexValue >= $max){
					$classValue = 'text-danger';
				}else{
					$classValue = '';
				}
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center '.$classValue.'">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'nuocTieu':
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'congThucMau':
				$min = ($standardValue['min']) ? ($standardValue['min']) : -9999;
				$max = ($standardValue['max']) ? ($standardValue['max']) : +9999;
				if($indexValue <= $min || $indexValue >= $max){
					$classValue = 'text-danger';
				}else{
					$classValue = '';
				}
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center '.$classValue.'">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'dong_mau':
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'NhomMau':
				$result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 text-primary">'.$indexValue.'</span>';
				break;
			case 'HST':
				$min = ($standardValue['min']) ? ($standardValue['min']) : -9999;
				$max = ($standardValue['max']) ? ($standardValue['max']) : +9999;
				if($indexValue <= $min || $indexValue >= $max){
					$classValue = 'text-danger';
				}else{
					$classValue = '';
				}
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center '.$classValue.'">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'sinh_hoc_phan_tu':
				$min = ($standardValue['min']) ? ($standardValue['min']) : -9999;
				$max = ($standardValue['max']) ? ($standardValue['max']) : +9999;
				if($indexValue <= $min || $indexValue >= $max){
					$classValue = 'text-danger';
				}else{
					$classValue = '';
				}
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center '.$classValue.'">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'viSinh':
				$result = '	<tr>
								<td class="text-center">'.$key.'</td>
								<td>'.$standardValue['fullName'].'</td>
								<td class="text-center"><span class="text-center">'.$indexValue.'</span></td>
								<td class="text-center">'.$standardValue['text'].'</td>
								<td class="text-center">'.$standardValue['unit'].'</td>
							</tr>';
				break;
			case 'ChiSoKhac':
				$result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 text-primary">'.$indexValue.'</span>';
				break;
			default:
				$result = '';
				break;
		}
		return $result;
	}

	public function checkUserApi($data){
        return $this->user->where($data)->first();
    }

	public function addPHR($phr){
        // Check
        $check = $this->annual->where(['phone_number' => $phr['phone_number'], 'examination_date' => $phr['examination_date'], 'Hospital' => $phr['hospital']])->first();
        if (empty($check)) {
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
        return $this->annual->set($data)->where(['annual_checkup_id' => $id])->update();
    }

	


}