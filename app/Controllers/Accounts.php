<?php

namespace App\Controllers;

use App\Services\HomeService;
use App\Services\AccountService;

class Accounts extends BaseController {

    public function __construct(){
        $this->home = new HomeService(); 
        $this->account = new AccountService();	

        $this->db = \Config\Database::connect();
	}

    public function index() {

        $data['panelTitle'] = 'Bảng tổng hợp';
        $data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/accounts/index');
		return view('AfterLogin/main', $data);
	}

    public function providerList(){
        $data['panelTitle'] = 'Danh sách tài khoản Bác sĩ';
        $data['user'] 	= session()->get('user');

        $pager=service('pager');
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $perPage =  20;

        $totalUsers = $this->account->countProvider('all');
        $pager->makeLinks($page+1, $perPage, $totalUsers);
        $start = $page * $perPage;
        $data['posts'] = $this->account->getProviderList($perPage, $start);

        $countAll = $totalUsers;

        foreach($data['posts'] as $k => $value){
            $data['posts'][$k]['index'] = $countAll - $start - $k;
        }

        $data['currentPage'] = $page+1;
        $data['totalPages'] = ceil($totalUsers/$perPage);

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách tài khoản Bac sĩ | Doctor4U', 'AfterLogin/pages/accounts/providerList');
		return view('AfterLogin/main', $data);
    }

    public function patientList(){
        $data['panelTitle'] = 'Danh sách tài khoản khách hàng';
        $data['user'] 	= session()->get('user');

        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

        $totalUsers = $this->account->countUsers('all');
        service('pager')->makeLinks($page+1, $perPage, $totalUsers);
        $start = $page * $perPage;
        $data['posts'] = $this->account->getPatientList($perPage, $start);

        $countAll = $totalUsers;

        foreach($data['posts'] as $k => $value){
            $data['posts'][$k]['index'] = $countAll - $start - $k;
        }

        $data['currentPage'] = $page+1;
        $data['totalPages'] = ceil($totalUsers/$perPage);

        $data['city_village_list'] = $this->account->city_village_list();

        // Get Referred Code
        $sql_referralCode = "SELECT referral_code FROM referral_campaign";   
        $data['referralCode'] = $this->db->query($sql_referralCode)->getResultArray();

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách tài khoản khách hàng | Doctor4U', 'AfterLogin/pages/accounts/patientList');
		return view('AfterLogin/main', $data);
    }

    public function managerList(){
        $data['panelTitle'] = 'TÀI KHOẢN QUẢN TRỊ';

        $data['user'] 	= session()->get('user');


        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | TÀI KHOẢN QUẢN TRỊ | DOCTOR4U', 'AfterLogin/pages/accounts/manager');
		return view('AfterLogin/main', $data);
    }

    public function updateAccountInfo(){

		$target 		= './public/csvfile';
		$file 			= $this->request->getFile('uploadFile');
		$ext 			= $file->guessExtension();
		$timeLabel 		= time().'_'.date('Ymd');	

		if (in_array($ext, ['csv', 'xls', 'xlsx'])) {
           
			$newName 	= $file->getName().'_update_'.$timeLabel.'.'.$ext;
			$inputFileName = $target.'/'.$newName;
			$file->move( $target , $newName);
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
			$spreadsheet = $spreadsheet->getActiveSheet();
			$data_array =  $spreadsheet->toArray();

			$count_success = 0;			// Đếm số bản ghi import thành công
			$count_fail = 0;			// Đếm số bản ghi import thất bại
			$arrayHistory = array();

			foreach($data_array as $i => $value){
				if($i > 1 && $value[3] !='' && $value[4] !=''){
					$phoneNumber =  preg_replace('/\s+/', '', $value[3]);
                    $phoneNumber = $this->convertPhoneDigit($phoneNumber);

					//Ngày sinh
					if(trim($value[4]) != ''){
						if(strpos($value[4], '/')){
							$bd = \DateTime::createFromFormat('d/m/Y', $value[4]);
							$birthdate = $bd->format('Y-m-d');
						}else{
							$birthdate = $value[4].'-01-01';
						}
					}else{
						$birthdate = null;
					}
					 $dataUpdate = array(
						 'phoneNumber' 	=> $phoneNumber,
						 'birthdate'	=> $birthdate
					 );

					$update = $this->account->updateAccount($dataUpdate);
					if($update){
						$count_success++;
						$dataSuccess = array(
							'type'			=> 'success',
							'id'			=> $value[0],
							'employee_id'	=> $value[1],
							'full_name' 	=> '',
							'phone_number' 	=> $phoneNumber,
							'status' 		=> 'Update thành công'
						);

						array_push($arrayHistory, $dataSuccess);
					}else{
						$count_fail++;
						$dataFail = array(
							'type'			=> 'success',
							'id'			=> $value[0],
							'employee_id'	=> $value[1],
							'full_name' 	=> '',
							'phone_number' 	=> $phoneNumber,
							'status' 		=> '<span class="text-danger">Update thất bại</span>'
						);

						array_push($arrayHistory, $dataFail);
					}
				}

			}
			// Lưu dữ liệu vào history_import
			$data_insertHistory = array(
				'file_name' => $inputFileName,
				'count_success' => $count_success,
				'count_false' => $count_fail,
				'date' => date("Y-m-d H:i:s"),
				'report' => json_encode($arrayHistory, true),
			);
			
			$this->account->addHistoryImport($data_insertHistory);

			session()->set('import-result', true);
		} else{
            session()->set('import-result', false);
        }
		
        return redirect()->to('trang-quan-tri/tai-khoan/khach-hang');
	}

    public function searchAccount(){

        $data['panelTitle'] = 'Danh sách tài khoản khách hàng';
        $data['user'] 	= session()->get('user');
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
        $perPage =  20;

        $start = $page * $perPage;

        $info = ($this->request->getVar('info')!==null)?$this->request->getVar('info') : '';
        $city = ($this->request->getVar('city')!==null)?$this->request->getVar('city') : '';
        $rCode = ($this->request->getVar('rCode')!==null)?$this->request->getVar('rCode') : '';

        $data['posts'] = $this->account->getUserList_searchResult($perPage, $start, $info, $city, $rCode);

        $countAll = count($this->account->countUsers_searchResult($info, $city, $rCode));

        foreach($data['posts'] as $k => $value){
            $data['posts'][$k]['index'] = $countAll - $start - $k;
        }

        $data['currentPage'] = $page+1;
        $data['totalPages'] = ceil($countAll/$perPage);
        service('pager')->makeLinks($page+1, $perPage, $countAll);
        $data['city_village_list'] = $this->account->city_village_list();

        $data['info'] = $info;
        $data['city'] = $city;
        $data['rCode'] = $rCode;

        // Get Referred Code
        $sql_referralCode = "SELECT referral_code FROM referral_campaign";   
        $data['referralCode'] = $this->db->query($sql_referralCode)->getResultArray();

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách tài khoản khách hàng | Doctor4U', 'AfterLogin/pages/accounts/patientListResult');
    
        return view('AfterLogin/main', $data);
		
	}

    public function countUsers_searchResult($info, $city, $rCode){
        $query = "SELECT users.username, person_name.given_name, person.gender, person.birthdate, users.date_created,
                users.email, person_address.address1, person_address.city_village, person_attribute.value, referral_history.referral_code as `code`
            FROM person
            INNER JOIN person_name ON person_name.person_id = person.person_id
            INNER JOIN users ON users.person_id = person.person_id
            LEFT JOIN  person_address ON person_address.person_id = person.person_id
            LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
            LEFT JOIN referral_history ON referral_history.phone_number = person_attribute.value
            WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null  and users.retired=0";
        if ($city != '') {
            $query .= " AND person_address.city_village = '" . $city . "'";
        }

        if ($info != '') {
            $query .= " AND (person_name.given_name like '%" . $info . "%' OR person_attribute.value = '" . $info . "')";
        }

        if ($rCode != '') {
            $query .= " AND (referral_history.referral_code = '$rCode')";
        }

        $query .= " ORDER BY users.person_id DESC";
        // dd($query);
        $list = $this->db->query($query);
        $result = $list->getResultArray();

        return $result;
    }

  
}
