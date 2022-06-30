<?php

namespace App\Controllers;

require_once 'TechAPI/bootstrap.php';

require 'vendor/autoload.php';

use App\Services\EhcService;
use App\Services\SettingService;
use App\Services\HomeService;


class Ehc extends BaseController {
	
	public function __construct(){
		$this->ehc 	= new EhcService();
		$this->setting 	= new SettingService();
		$this->service 	= new HomeService();

	}

	public function index() {
		
	}

	public function getAuthToken(){
		$token 		= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-token']);
		$username 	= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-username']);
		$password 	= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-password']);
		$dataLogin = array(
			'username' 		=> $username['settingValue'],
			'password' 	=> $password['settingValue'],
		);
		$dataLogin_string = json_encode($dataLogin, true);
		$ehc_baseUrl = 'http://api.1vietnam.net';

		// Gọi API tạo User mới
		$url = $ehc_baseUrl.'/api/Login?api='.$token['settingValue'];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataLogin_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function getAPIResult($apiData){
	
		$token 	= $apiData['token'];
		$post 	= $apiData['post'];
		$url 	= $apiData['url'];
		$type 	= $apiData['type'];	
		header('Content-Type: application/json'); // Specify the type of data
		$ch = curl_init($url); // Initialise cURL
		$post = json_encode($post); // Encode the data array into a JSON string
		$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, $type); // Specify the request method as POST
		if($type == 1){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
		}
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects

		$result = curl_exec($ch); // Execute the cURL statement
	
		curl_close($ch); // Close the cURL connection
		return json_decode($result, true); // Return the received data
	}

	public function updateInfo(){

		$time  = 24;
		$limit = 10;
		$start = date('YmdHi',strtotime('-'.$time.' hour'));
		$end = date('YmdHi');

		$hospital = 'Phòng khám Bác sỹ gia đình Doctor4U';
		$hospital_id = 'D4U';
		$token 		= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-token']);
		$checkSMS 	= $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => 'default-sms-result']);

		$ehc_baseUrl = 'http://api.1vietnam.net';
		$getAuthToken = json_decode($this->getAuthToken(), true);

		if($getAuthToken['status'] == 'success'){
			//Toàn bộ đợt điều trị trong khoảng $time
			$all_treatmentApiData = array(
				'token' => $getAuthToken['token'],
				'post' 	=> array(),
				'url'	=> $ehc_baseUrl.'/api/Treatment/All?api='.$token['settingValue'].'&fromdate='.$start.'&todate='.$end.'&limit='.$limit,
				'type' 	=> 0 // type=0: method get; type=1: method post 
			);

			$treatments_All = $this->getAPIResult($all_treatmentApiData);
		
			// Duyệt từng page 
			for($treatments_page=1; $treatments_page <= $treatments_All['totalpage']; $treatments_page++){
			
				// Đối với mỗi page, lấy các đợt điều trị
				$page_treatmentApiData = array(
					'token' => $getAuthToken['token'],
					'post' 	=> array(),
					'url'	=> $ehc_baseUrl.'/api/Treatment/All?api='.$token['settingValue'].'&fromdate='.$start.'&todate='.$end.'&limit='.$limit.'&page='.$treatments_page,
					'type' 	=> 0		// type=0: method get; type=1: method post 
				);
				$treatments = $this->getAPIResult($page_treatmentApiData);

				foreach($treatments['data'] as $k => $trm){
					// if($trm['sodienthoai'] == '0913534333' || $trm['sodienthoai'] == '0989995566'){
						$idluotkham_trm = $trm['idluotkham'];
						$patientPHRApiData = array(
							'token' => $getAuthToken['token'],
							'post' 	=> array(),
							'url'	=> $ehc_baseUrl.'/api/PatientPHR/One?api='.$token['settingValue'].'&idluotkham='.$idluotkham_trm,
							'type' 	=> 0		// type=0: method get; type=1: method post 
						);
						$patientPHR = $this->getAPIResult($patientPHRApiData);
						// echo '<pre>';
						// print_r($treatments);
						// echo '</pre>';die();
						//Nguồn đến 0: Khách lẻ,  1: Khách đoàn
						if($patientPHR['nguon_den'] == '0'){
							
							if($patientPHR['chandoan_icd10'] != 'COVID' && trim($patientPHR['chandoanxacdinh']) != 'mt' && trim($patientPHR['chandoanxacdinh']) != 'MT' && trim($patientPHR['chandoanxacdinh']) != 'ktd' && trim($patientPHR['chandoanxacdinh']) != 'ktđ' && trim($patientPHR['chandoanxacdinh']) != 'KTD' && trim($patientPHR['chandoanxacdinh']) != 'KTĐ'){
								if($trm['gioitinh']=='1'){
									$gender = 'Nam';
								}else{
									$gender = 'Nữ';
								}

								$idluotkham 	= $patientPHR['idluotkham'];
								$tenbenhnhan 	= $patientPHR['tenbenhnhan'];
								$sodienthoai 	= $this->convertPhoneDigit(trim(str_replace(['+84'],'0',$patientPHR['sodienthoai'])));
								$noilamviec 	= $patientPHR['noilamviec'];
								$diachi 		= $patientPHR['diachi'];
								$chandoan 		= $patientPHR['chandoan'];

								//Convert định dạng ngày sinh
									$ngaysinh['year'] 	= substr($patientPHR['ngaysinh'], 0, 4);
									$ngaysinh['month'] 	= substr($patientPHR['ngaysinh'], 4, 2);
									if($ngaysinh['month'] == '00'){
										$ngaysinh['month'] = '01';
									}
									$ngaysinh['day'] 	= substr($patientPHR['ngaysinh'], 6, 2);
									if($ngaysinh['day'] == '00'){
										$ngaysinh['day'] = '01';
									}
									$birthdate 			= $ngaysinh['year'].'-'.$ngaysinh['month'].'-'.$ngaysinh['day'];

								//Convert định dạng ngày khám
									$ngaykham['year'] 	= substr($patientPHR['ngaydontiep'], 0, 4);
									$ngaykham['month'] 	= substr($patientPHR['ngaydontiep'], 4, 2);
									$ngaykham['day'] 	= substr($patientPHR['ngaydontiep'], 6, 2);
									$examination_date 	= $ngaykham['year'].'-'.$ngaykham['month'].'-'.$ngaykham['day'];

								if($chandoan == ''){
									$check_idluotkham = $this->ehc->checkTempTreatment($idluotkham);

									if(empty($check_idluotkham)){
										$dataTemp = array(
											'treatment_id' 	=> $idluotkham,
											'phone_number' 	=> $sodienthoai,
											'patient_name' 	=> $tenbenhnhan,
											'date'			=> $examination_date
										);
	
										$this->ehc->addEhcTempRecord($dataTemp);
									}
									
								}else{
									$check_idluotkham = $this->ehc->checkIdLuotKham($idluotkham);
							
									
									
									// THÔNG TIN CÁ NHÂN
										$report['id'] 			= $idluotkham;
										$report['ma_nv'] 		= '';
										$report['name'] 		= $tenbenhnhan;
										$report['SDT'] 			= $sodienthoai;
										$report['birth'] 		= $ngaysinh['day'].'/'.$ngaysinh['month'].'/'.$ngaysinh['year'];
										$report['gender'] 		= $gender;
										$report['don_vi'] 		= $noilamviec;
										$report['dia_chi'] 		= $diachi;
										$report['chan_doan'] 	= $chandoan;
		
									// KẾT LUẬN TƯ VẤN - ĐỀ NGHỊ
										$report['ketLuan'] 		= $patientPHR['chandoanxacdinh'];
										$report['deNghi'] 		= $patientPHR['loidanbacsi'];
										$report['ketLuanXN'] 	= '';
										$report['don_thuoc'] 	= '';
									
									// KHÁM THỂ LỰC
										$khamTheLuc = $patientPHR['get_List_Sheet'][0];
										$report['theLuc'] = array();
										// Chiều cao
										$chieucao['maDV']		= 'KTL_CC';
										$chieucao['tenDV']		= 'Chiều cao';
										$chieucao['ketqua']		= $khamTheLuc['chieucao'];
										$chieucao['file']		= '';
										array_push($report['theLuc'], $chieucao);

										$cannang['maDV']		= 'KTL_CN';
										$cannang['tenDV']		= 'Cân nặng';
										$cannang['ketqua']		= $khamTheLuc['cannang'];
										$cannang['file']		= '';
										array_push($report['theLuc'], $cannang);

										$huyetap['maDV']		= 'KTL_HA';
										$huyetap['tenDV']		= 'Huyết áp';
										$huyetap['ketqua']		= $khamTheLuc['huyetap_high'].'/'.$khamTheLuc['huyetap_low'];
										$huyetap['file']		= '';	
										array_push($report['theLuc'], $huyetap);
										
										$mach['maDV']			= 'KTL_NT';
										$mach['tenDV']			= 'Nhịp tim';
										$mach['ketqua']			= $khamTheLuc['nhiptim'];
										$mach['file']			= '';
										array_push($report['theLuc'], $mach);
		
									$ds_dichVu		= $patientPHR['get_Oliu_Service'];
									$ds_testCovid 	= array();
									//Danh sách mã dịch vụ đã thực hiện
									$arr_MaDV = array();
									if(!empty($ds_dichVu)){
										foreach($ds_dichVu as $h => $dv){
		
											// Tách test covid
											$covidCodeArray = array('KB_COV','TEST_COV','KB01_COV','KTC','CK0060');
											if(in_array($dv['madv'], $covidCodeArray)){
												array_push($ds_testCovid, $dv);
												unset($ds_dichVu[$h]);
											}else{
												if($dv['ketqua'] != ''){
													array_push($arr_MaDV,array($dv,$dv['madv']));
												}
				
												if($dv['list_detail'] != ''){
													foreach($dv['list_detail'] as $listDetail){
														array_push($arr_MaDV,array($listDetail,$listDetail['madv']));
													}
												}
											}
										}
									}
									
									if(!empty($ds_dichVu)){
										foreach($ds_dichVu as $k => $dv){
											if(!empty($dv['list_detail'])){
												foreach($dv['list_detail'] as $ld){
													array_push($ds_dichVu, $ld);
												}
												$ds_dichVu[$k]['list_detail'] = [];
											}
										}

										foreach($ds_dichVu as $dichvu){
											$this->themChiSoChuan($dichvu, $gender);
										}

									}
								
									if(!empty($ds_dichVu)){
										foreach($ds_dichVu as $dv){
											// KHÁM LÂM SÀNG
											// CHẨN ĐOÁN HÌNH ẢNH
												$array_CDHA = array(
													'C012', 'C014','C017', 'C018','C0121', 'C0122','C0191',	//X quang
													'C001', 'C002','C003', 'C004','C006', 'C0062',	//Siêu âm
													'C024', 'C0027','C013', 'C015','C016', 'C025',	//Siêu âm
													'C0081', 'C007','C0401', 'C0402','TTQ', 'XL002',//Siêu âm
													'C0063','C0064','C0065','C0241',
													'C011', 'ECGO','C005', 'DT0001','LHN'			//Nội soi - Điện tim - Điện não
												);
												$report['chuanDoanHinhAnh'] = array();
												foreach($array_CDHA as $ma_cdha){
													if(!is_null($this->themDichVu($ma_cdha, $arr_MaDV))){
														array_push($report['chuanDoanHinhAnh'], $this->themDichVu($ma_cdha, $arr_MaDV));
													}
													
												}
												
											// HÓA SINH MIỄN DỊCH
												$array_HS = array(
													'A0025','A0026','A0027','A0028','A0031','A0033',
													'A0032','A0034','A0035','A0015','A0016','A0019',
													'A0014','A0038','A0040','A0043','A0047','A0059',
													'A0041.1','A0041.2','A0041.3','A0017','A0018','A0020',
													'A0021','A0022','A0023','A0024','A0029','A0030',
													'A0036','A0037','A0044','A0052','A0105','00AXIT',
													'A0578','A05791','A0132', 'A0133', 'A0134', 'A0135',
													'A0136','A0137','A0138', 'A0139', 'A0147', 'A0169',
													'A0172','A0173','A0177', 'A0156', 'A0781.1', 'A0781.2',
													'A0782.1','A0782.2','A0783.1', 'A0783.2', 'KTC', 'A0155',
													'A0175','A0176','A0787','A0179','A0182','A0471','A0184','A0555',
													'A0129',

												);
		
												$report['hoaSinhMienDich'] = array();
												foreach($array_HS as $ma_hs){
													if(!is_null($this->themDichVu($ma_hs, $arr_MaDV))){
														array_push($report['hoaSinhMienDich'], $this->themDichVu($ma_hs, $arr_MaDV));
													}
													
												}
													
		
											// XÉT NGHIỆM NƯỚC TIỂU
												$array_NT= array(
													'A0080.1','A0080.2','A0080.3','A0080.4','A0080.5',
													'A0080.6','A0080.7','A0080.8','A0080.9','A0080.10','A0080.11',
													'A0051','A0067','A0068', 'A0069', 'A070', 'A0071',
													'A0072','A0073','A0074', 'A0075', 'A0076', 'A0077',
													'A0078','A0084','A0085', 'A0086', 'A0087.1', 'A0087.2',
													'A0087.3','A0087.4','A0089', 'A0090', 'A0092', 'A0093',
													'A0094','A0095','A0096'
												);

												$report['nuocTieu'] = array();
												foreach($array_NT as $ma_nt){
													if(!is_null($this->themDichVu($ma_nt, $arr_MaDV))){
														array_push($report['nuocTieu'], $this->themDichVu($ma_nt, $arr_MaDV));
													}
												}	

											// CÔNG THỨC MÁU
												$array_CTM = array(
													'A0003.1','A0003.2','A0003.3','A0003.4', 'A0003.5','A0003.6','A0003.7','A0003.8',
													'A0003.9','A0003.10','A0003.11','A0003.12', 'A0003.13','A0003.14','A0003.15','A0003.16',
													'A0003.17','A0003.18','A0003.19','A0003.20', 'A0003.21','A0003.22','A0003.23','A0003.24',
													'A0003.25','A0003.26',
													'A0005','A0013','A0006.1', 'A0006.2', 'A0012.1', 'A0012.2',
													'A0012.3','A0012.4','A0012.5', 'A0012.6', 'A0012.7', 'A0012.8',
													'A0001.1','A0001.2','A00011', 'A00012'
												);
		
												$report['congThucMau'] = array();
												foreach($array_CTM as $ma_ctm){
													if(!is_null($this->themDichVu($ma_ctm, $arr_MaDV))){
														array_push($report['congThucMau'], $this->themDichVu($ma_ctm, $arr_MaDV));
													}
												}	
													
											// ĐÔNG MÁU
												$array_DM = array(
													'A0007.1','A0007.2','A0007.3','A0008.1','A0008.2','A0010.1','A0010.2','A0580','A0009'
												);

												$report['dong_mau'] = array();
												foreach($array_DM as $ma_dm){
													if(!is_null($this->themDichVu($ma_dm, $arr_MaDV))){
														array_push($report['dong_mau'], $this->themDichVu($ma_dm, $arr_MaDV));
													}
												}	
												
											// ĐIỆN DI HST
												$array_HST = array(
													'A0012.1','A0012.2','A0012.3','A0012.4','A0012.5','A0012.6','A0012.7','A0012.8'
												);
		

												$report['HST'] = array();
												foreach($array_HST as $ma_hst){
													if(!is_null($this->themDichVu($ma_hst, $arr_MaDV))){
														array_push($report['HST'], $this->themDichVu($ma_hst, $arr_MaDV));
													}
												}	
												
											// SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia
												$array_shpt = array(
													'A0504','A0505','A0506','A0507', 
													'A0508','A0509','A0510','A0511',
													'A0512','A0513','A0514','A0515',
													'A0516','A0517','A0518','A0519',
													'A0520','A0521','A0522'
												);
		
												$report['sinh_hoc_phan_tu'] = array();
												foreach($array_shpt as $ma_shpt){
													if(!is_null($this->themDichVu($ma_shpt, $arr_MaDV))){
														array_push($report['sinh_hoc_phan_tu'], $this->themDichVu($ma_shpt, $arr_MaDV));
													}
												}
												
											// VI SINH
												$array_vs = array(
													'A0110','A0111','A0112','A0117','A0118','A0119','A0121','A0123','A0124','A0283',
													'A0784','A0057','A0283.1','A0283.2','A0057.1','A0057.2','A0057.3','A0115','A0140',
													'A0141','A0142','A0149','A0154','A0216','A0217','A0460','A0785',
													'A03711.0','A03711.1','A03711.2', 'A03711.3', 'A03711.4',
													'A0240','A0451','A0403','A0365','A0364','A0392'
												);
												$report['viSinh'] = array();
												foreach($array_vs as $ma_vs){
													if(!is_null($this->themDichVu($ma_vs, $arr_MaDV))){
														array_push($report['viSinh'], $this->themDichVu($ma_vs, $arr_MaDV));
													}
												}
		
											// SOI TƯƠI ÂM ĐẠO
												$array_stad = array(
													'A0255.1','A0255.2','A0255.3','A0255.4','A0255.5',
													'A0255.6','A0255.7','A0255.8','A0255.9','A0255.10'
												);
												$report['soiTuoiAmDao'] = array();
												foreach($array_stad as $ma_stad){
													if(!is_null($this->themDichVu($ma_stad, $arr_MaDV))){
														array_push($report['soiTuoiAmDao'], $this->themDichVu($ma_stad, $arr_MaDV));
													}
												}

											// NHÓM MÁU
												$array_nm = array(
													'A0001.1','A0001.2'
												);
		
												$report['NhomMau'] = array();
												foreach($array_nm as $ma_nm){
													if(!is_null($this->themDichVu($ma_nm, $arr_MaDV))){
														array_push($report['NhomMau'], $this->themDichVu($ma_nm, $arr_MaDV));
													}
												}
		
											// CHỈ SỐ KHÁC
												$array_csk = array(
													'A0058', 'A0061'
												);
												$report['ChiSoKhac'] = array();
												foreach($array_csk as $ma_csk){
													if(!is_null($this->themDichVu($ma_csk, $arr_MaDV))){
														array_push($report['ChiSoKhac'], $this->themDichVu($ma_csk, $arr_MaDV));
													}
												}
												// $report['ChiSoKhac']['ChiSoKhac'] 			= '';
												
		
											// Date & hospital
												$report['date'] 	= $examination_date;
												$report['hospital'] = $hospital;
										
											$examination_report = json_encode($report);
										}
									}
									$userData = array(
										'username' 		=> $sodienthoai,
										'phoneNumber' 	=> $sodienthoai,
										'password' 		=> '123456',
										'givenName' 	=> $tenbenhnhan,
										'birthdate' 	=> $birthdate,
										'gender' 		=> $gender,
										'role' 			=> 'PATIENT',
										'providerRole'	=> 'c110f9bc-c65f-44a2-a028-2af7e8fff534',
									);	

									$rs = $this->registerPatient($userData);
									if (isset($rs['user']['uuid']) && $rs['user']['uuid'] != '') {
										$create_user = 'Tạo user mới thành công';
										$data['username'] = $sodienthoai;
										$user = $this->service->checkUserApi($data);
										$dataInsert = array(
											'user_id' 			=> $user['user_id'],
											'employee_id' 		=> '',
											'phone_number' 		=> $sodienthoai,
											'company_id' 		=> $noilamviec,
											'full_name' 		=> $tenbenhnhan,
											'gender' 			=> $gender,
											'birthdate' 		=> $birthdate,
											'Occupation' 		=> 'auto',
											'Hospital' 			=> $hospital,
											'hospital_id'		=> $hospital_id,
											'examination_date' 	=> $examination_date,
											'examination_report'=> $examination_report
										);

										//Insert vào DB
										$insert_id = $this->service->addPHR($dataInsert);

											if($insert_id && $chandoan != ''){
												$annual_checkup_id = $insert_id['id'];
												// @Send Notify & SMS
												// if($checkSMS === 'yes'){
													if($chandoan != ''){
														// Send SMS đến SĐT bệnh nhân
														$settingName = CONTENT_SMS_RESULT_NEW;
														$exDate = $ngaykham['day'].'-'.$ngaykham['month'].'-'.$ngaykham['year'];
														$msg = $this->service->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $exDate]);
														// $this->sendSMS($sodienthoai,'Doctor4U',$msg);
													}
													
												// }
													
												// ---

												$result = 'Tạo bản ghi Kết quả xét nghiệm thành công!';

											}else{
												$result = 'Tạo bản ghi thất bại';
												$annual_checkup_id = null;
											}
											
									}else if(isset($rs['error']) && $rs['error']['message'] == '[Số điện thoại đã tồn tại trong hệ thống!]'){
										$create_user = 'Tài khoản đã tồn tại !';
										$data['username'] = $sodienthoai;
										
										$user = $this->service->checkUserApi($data);
										$dataInsert = array(
											'user_id' 			=> $user['user_id'],
											'employee_id' 		=> '',
											'phone_number' 		=> $sodienthoai,
											'company_id' 		=> $trm['noilamviec'],
											'full_name' 		=> $tenbenhnhan,
											'gender' 			=> $gender,
											'birthdate' 		=> $birthdate,
											'Occupation' 		=> 'auto',
											'Hospital' 			=> $hospital,
											'hospital_id'		=> $hospital_id,
											'examination_date' 	=> $examination_date,
											'examination_report'=> $examination_report
										);

										//Insert vào DB
											if(empty($check_idluotkham)){
												$insert_id = $this->service->addPHR($dataInsert);	
												if($insert_id){
													$annual_checkup_id = $insert_id['id'];
													// @Send Notify & SMS
													// if($checkSMS === 'yes'){
														if($chandoan != ''){
															// Send SMS đến SĐT bệnh nhân
															$settingName = CONTENT_SMS_RESULT_OLD;
															$exDate = $ngaykham['day'].'-'.$ngaykham['month'].'-'.$ngaykham['year'];
															$msg = $this->service->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $exDate]);
															// $this->sendSMS($sodienthoai,'Doctor4U',$msg);
														}
														
														
													// }

														//Gửi Notification đến Device cho user
														$date = date('d-m-Y');
														$this->service->send_notification($sodienthoai, $hospital, $date, $insert_id['id']);
													
													// ---
													$result = 'Tạo bản ghi Kết quả xét nghiệm thành công!';
		
												}else{
													$result = 'Tạo bản ghi thất bại';
													$annual_checkup_id = null;	
												}
											}else{
												if(isset($check_idluotkham['annual_checkup_id']) && $check_idluotkham['annual_checkup_id'] != ''){
													$annual_checkup_id = $check_idluotkham['annual_checkup_id'];
													$updateTreatment = $this->service->updatePHR($annual_checkup_id, $dataInsert);
													
													if($updateTreatment && $chandoan != ''){
														// @Send Notify & SMS
														// if($checkSMS === 'yes'){
															if($chandoan != ''){
																// Send SMS đến SĐT bệnh nhân
																$settingName = CONTENT_SMS_RESULT_OLD;
																$exDate = $ngaykham['day'].'-'.$ngaykham['month'].'-'.$ngaykham['year'];
																$msg = $this->service->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $exDate]);
																// $this->sendSMS($sodienthoai,'Doctor4U',$msg);
															}
															
															
														// }
															//Gửi Notification đến Device cho user
															$date = date('d-m-Y');
															$this->service->send_notification($sodienthoai, $hospital, $date, $annual_checkup_id);
														// ---
														$result = 'Cập nhật Kết quả xét nghiệm thành công!';
		
													}else{
														$result = 'Cập nhật thất bại';
														$annual_checkup_id = null;
													}
												}
												
											}
										
									}else{	//Wrong phone number
										$result = 'Thông tin không hợp lệ !';
										$annual_checkup_id = null;
									}	

									if(!is_null($annual_checkup_id)){
										$dataLog = array(
											'id_treatment' 	=> $idluotkham,
											'annual_checkup_id' => $annual_checkup_id,
											'phoneNumber' 	=> $sodienthoai,
											'fullName' 		=> $tenbenhnhan,
											'type'			=> 'khach-le',
											'exam_date' 	=> $examination_date,
											'gender' 		=> $gender,
											'birthdate' 	=> $birthdate,
											'conclusion' 	=> $chandoan,
											'result' 		=> $create_user.' - '.$result
										);
										
										$this->ehc->addHistoryUpdate($dataLog);
									}
								}

							}
							
						}else{
							//Khách đoàn
						}
						
					// }
					
				}
				
				
			}
			
		}else{

		}

		
	}

	public function ehcHistoryKhachLe(){
		
			$data['user'] 	= session()->get('user');
			$pager = \Config\Services::pager();

			$page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
            $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
            $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
            $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

			$perPage =  20;

			$allHistory = $this->ehc->getEHChistoryByType('khach-le', $data['date_from'], $data['date_to'], $data['info']);
			$countAll = count($allHistory);
			if($allHistory){
				if($countAll > 1){
					$pager->makeLinks($page+1, $perPage, $countAll);
					$start = $page * $perPage;
					$finish = ($page+1) * $perPage;
					$history = array();
					for($i=$start; $i<$finish; $i++){
						if(isset($allHistory[$i])){
							array_push($history, $allHistory[$i]);
						}
					}
					foreach($history as $k => $value){
						$history[$k]['index'] = $countAll - $start - $k;
					}
				}else{
					$history = $allHistory;
					$history[0]['index'] = 1;
				}

				$data['history'] = $history;

				$data['currentPage'] = $page+1;
				$data['totalPages'] = ceil($countAll/$perPage);

			}else{
				$data['history'] = false;
			}
			$data['link'] = '/khach-le';
			$data['pageTitle'] = 'HIS - Bệnh án khách lẻ';
			$data['panelTitle'] = 'HIS - Bệnh án khách lẻ';

		$data = $this->getAfterLoginLayout($data, 'Trang quản trị', 'AfterLogin/pages/ehc/khachle');
		return view('AfterLogin/main', $data);

	}

	public function ehcHistoryKhachDoan(){
		if(session()->has('user')){
			$data['user'] 	= session()->get('user');
			$pager = \Config\Services::pager();

			$page				=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
            $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
            $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
            $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

			$perPage =  10;

			$allHistory = $this->ehc->getEHChistoryByType('khach-doan', $data['date_from'], $data['date_to'], $data['info']);
			$countAll = count($allHistory);
			if($allHistory){
				if($countAll > 1){
					$pager->makeLinks($page+1, $perPage, $countAll);
					$start = $page * $perPage;
					$finish = ($page+1) * $perPage;
					$history = array();
					for($i=$start; $i<$finish; $i++){
						if(isset($allHistory[$i])){
							array_push($history, $allHistory[$i]);
						}
						
					}
					foreach($history as $k => $value){
						$history[$k]['index'] = $countAll - $start - $k;
					}
				}else{
					$history = $allHistory;
					$history[0]['index'] = 1;
				}

				$data['history'] = $history;

				$data['currentPage'] = $page+1;
				$data['totalPages'] = ceil($countAll/$perPage);

			}else{
				$data['history'] = false;
			}
			$data['link'] = '/khach-doan';
			$data['pageTitle'] = 'Danh sách cập nhật từ HIS - Kết quả xét nghiệm khách đoàn';
			$data 			= $this->getAfterLoginLayout($data, 'Trang quản trị', 'ehc/history');
			return view('home/main', $data);
		}else{
			return redirect()->to('/login');
		}
	}

	public function ehcHistoryTestCovid(){
		if(session()->has('user')){
			$data['user'] 	= session()->get('user');
			$pager = \Config\Services::pager();

			$page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
            $data['info']		=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
            $data['date_from']	=($this->request->getVar('date_from')!==null)?$this->request->getVar('date_from'):'';
            $data['date_to'] 	=($this->request->getVar('date_to')!==null)?$this->request->getVar('date_to'):'';

			$perPage =  10;

			$allHistory = $this->ehc->getEHChistoryByType('test-covid', $data['date_from'], $data['date_to'], $data['info']);
			$countAll = count($allHistory);
			if($allHistory){
				if($countAll > 1){
					$pager->makeLinks($page+1, $perPage, $countAll);
					$start = $page * $perPage;
					$finish = ($page+1) * $perPage;
					$history = array();
					for($i=$start; $i<$finish; $i++){
						if(isset($allHistory[$i])){
							array_push($history, $allHistory[$i]);
						}
						
					}
					foreach($history as $k => $value){
						$history[$k]['index'] = $countAll - $start - $k;
					}
				}else{
					$history = $allHistory;
					$history[0]['index'] = 1;
				}

				$data['history'] = $history;

				$data['currentPage'] = $page+1;
				$data['totalPages'] = ceil($countAll/$perPage);

			}else{
				$data['history'] = false;
			}
			$data['link'] = '/test-covid';
			$data['pageTitle'] = 'Danh sách cập nhật từ HIS - Kết quả Test COVID';
			$data 			= $this->getAfterLoginLayout($data, 'Trang quản trị', 'ehc/history');
			return view('home/main', $data);
		}else{
			return redirect()->to('/login');
		}
	}

	public function reUpdateEHC(){
		
		$treatmentList = $this->request->getVar('treatmentList');
		$treatmentArray = explode(',',$treatmentList);

		foreach($treatmentArray as $k => $id_treatment){
			if($id_treatment != ''){
				// Lấy annual checkup id
				$trm = $this->ehc->checkIdLuotKham($id_treatment);

				$this->updateTreatment($id_treatment, $trm['annual_checkup_id']);
			}			
		}
	}

	public function updateTreatment($id_treatment, $annual_checkup_id){

		$ehc_baseUrl = 'https://api.1vietnam.net';
		$token 		= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-token']);
		$getAuthToken = json_decode($this->getAuthToken(), true);
		$hospital = 'Phòng khám Bác sỹ gia đình Doctor4U';
		$hospital_id = 'D4U';

		$patientPHRApiData = array(
			'token' => $getAuthToken['token'],
			'post' 	=> array(),
			'url'	=> $ehc_baseUrl.'/api/PatientPHR/One?api='.$token['settingValue'].'&idluotkham='.$id_treatment,
			'type' 	=> 0		// type=0: method get; type=1: method post 
		);
	
		$trm = $this->getAPIResult($patientPHRApiData);
		
		//Nguồn đến 0: Khách lẻ,  1: Khách đoàn
		if($trm['nguon_den'] == '0'){
			if($trm['chandoan_icd10'] != 'COVID'  && trim($trm['chandoanxacdinh']) != 'mt' && trim($trm['chandoanxacdinh']) != 'MT' && trim($trm['chandoanxacdinh']) != 'ktd' && trim($trm['chandoanxacdinh']) != 'ktđ' && trim($trm['chandoanxacdinh']) != 'KTD' && trim($trm['chandoanxacdinh']) != 'KTĐ'){
				if($trm['gioitinh']=='1'){
					$gender = 'Nam';
				}else{
					$gender = 'Nữ';
				}

				$idluotkham = $trm['idluotkham'];
				$tenbenhnhan = $trm['tenbenhnhan'];
				$sodienthoai = trim(str_replace(['+84'],'0',$trm['sodienthoai']));
				$noilamviec = $trm['noilamviec'];
				$diachi = $trm['diachi'];
				$chandoan = $trm['chandoan'];

				// $check_idluotkham = $this->ehc->checkIdLuotKham($idluotkham);
			
				//Convert định dạng ngày sinh
					$ngaysinh['year'] 	= substr($trm['ngaysinh'], 0, 4);
					$ngaysinh['month'] 	= substr($trm['ngaysinh'], 4, 2);
					if($ngaysinh['month'] == '00'){
						$ngaysinh['month'] = '01';
					}
					$ngaysinh['day'] 	= substr($trm['ngaysinh'], 6, 2);
					if($ngaysinh['day'] == '00'){
						$ngaysinh['day'] = '01';
					}
					$birthdate 			= $ngaysinh['year'].'-'.$ngaysinh['month'].'-'.$ngaysinh['day'];

				//Convert định dạng ngày khám
					$ngaykham['year'] 	= substr($trm['ngaydontiep'], 0, 4);
					$ngaykham['month'] 	= substr($trm['ngaydontiep'], 4, 2);
					$ngaykham['day'] 	= substr($trm['ngaydontiep'], 6, 2);
					$examination_date 	= $ngaykham['year'].'-'.$ngaykham['month'].'-'.$ngaykham['day'];
				
				// THÔNG TIN CÁ NHÂN
					$report['id'] 			= $idluotkham;
					$report['ma_nv'] 		= '';
					$report['name'] 		= $tenbenhnhan;
					$report['SDT'] 			= $sodienthoai;
					$report['birth'] 		= $ngaysinh['day'].'/'.$ngaysinh['month'].'/'.$ngaysinh['year'];
					$report['gender'] 		= $gender;
					$report['don_vi'] 		= $noilamviec;
					$report['dia_chi'] 		= $diachi;
					$report['chan_doan'] 	= $chandoan;

				// KẾT LUẬN TƯ VẤN - ĐỀ NGHỊ
					$report['ketLuan'] 		= $trm['chandoanxacdinh'];
					$report['deNghi'] 		= $trm['loidanbacsi'];
					$report['ketLuanXN'] 	= '';
					$report['don_thuoc'] 	= '';
				
				// KHÁM THỂ LỰC
					$khamTheLuc = $trm['get_List_Sheet'][0];
					$report['theLuc'] = array();
					// Chiều cao
					$chieucao['maDV']		= 'KTL_CC';
					$chieucao['tenDV']		= 'Chiều cao';
					$chieucao['ketqua']		= $khamTheLuc['chieucao'];
					$chieucao['file']		= '';
					array_push($report['theLuc'], $chieucao);

					$cannang['maDV']		= 'KTL_CN';
					$cannang['tenDV']		= 'Cân nặng';
					$cannang['ketqua']		= $khamTheLuc['cannang'];
					$cannang['file']		= '';
					array_push($report['theLuc'], $cannang);

					$huyetap['maDV']		= 'KTL_HA';
					$huyetap['tenDV']		= 'Huyết áp';
					$huyetap['ketqua']		= $khamTheLuc['huyetap_high'].'/'.$khamTheLuc['huyetap_low'];
					$huyetap['file']		= '';	
					array_push($report['theLuc'], $huyetap);
					
					$mach['maDV']			= 'KTL_NT';
					$mach['tenDV']			= 'Nhịp tim';
					$mach['ketqua']			= $khamTheLuc['nhiptim'];
					$mach['file']			= '';
					array_push($report['theLuc'], $mach);

				$ds_dichVu		= $trm['get_Oliu_Service'];
				$ds_testCovid 	= array();
				//Danh sách mã dịch vụ đã thực hiện
				$arr_MaDV = array();
				foreach($ds_dichVu as $h => $dv){

					// Tách test covid
					$covidCodeArray = array('KB_COV','TEST_COV','KB01_COV','KTC','CK0060');
					if(in_array($dv['madv'], $covidCodeArray)){
						array_push($ds_testCovid, $dv);
						unset($ds_dichVu[$h]);
					}else{
						// if($dv['ketqua'] != ''){
							array_push($arr_MaDV,array($dv,$dv['madv']));
						// }

						if($dv['list_detail'] != ''){
							foreach($dv['list_detail'] as $listDetail){
								array_push($arr_MaDV,array($listDetail,$listDetail['madv']));
							}
						}
					}
				}
				
				if(!empty($ds_dichVu)){
					foreach($ds_dichVu as $k => $dv){
						if(!empty($dv['list_detail'])){
							foreach($dv['list_detail'] as $ld){
								array_push($ds_dichVu, $ld);
							}
							$ds_dichVu[$k]['list_detail'] = [];
						}

					}

					foreach($ds_dichVu as $dichvu){
						$this->themChiSoChuan($dichvu, $gender);
					}

				}
			
				if(!empty($ds_dichVu)){
					foreach($ds_dichVu as $dv){
						// KHÁM LÂM SÀNG
						// CHẨN ĐOÁN HÌNH ẢNH
							$array_CDHA = array(
								'C012', 'C014','C017', 'C018','C0121', 'C0122','C0191',	//X quang
								'C001', 'C002','C003', 'C004','C006', 'C0062',	//Siêu âm
								'C024', 'C0027','C013', 'C015','C016', 'C025',	//Siêu âm
								'C0081', 'C007','C0401', 'C0402','TTQ', 'XL002',//Siêu âm
								'C0063','C0064','C0065','C0241',
								'C011', 'ECGO','C005', 'DT0001','LHN'			//Nội soi - Điện tim - Điện não
							);
							$report['chuanDoanHinhAnh'] = array();
							foreach($array_CDHA as $ma_cdha){
								if(!is_null($this->themDichVu($ma_cdha, $arr_MaDV))){
									array_push($report['chuanDoanHinhAnh'], $this->themDichVu($ma_cdha, $arr_MaDV));
								}
								
							}
							
						// HÓA SINH MIỄN DỊCH
							$array_HS = array(
								'A0025','A0026','A0027','A0028','A0031','A0033',
								'A0032','A0034','A0035','A0015','A0016','A0019',
								'A0014','A0038','A0040','A0043','A0047','A0059',
								'A0041.1','A0041.2','A0041.3','A0017','A0018','A0020',
								'A0021','A0022','A0023','A0024','A0029','A0030',
								'A0036','A0037','A0044','A0052','A0105','00AXIT',
								'A0578','A05791','A0132', 'A0133', 'A0134', 'A0135',
								'A0136','A0137','A0138', 'A0139', 'A0147', 'A0169',
								'A0172','A0173','A0177', 'A0156', 'A0781.1', 'A0781.2',
								'A0782.1','A0782.2','A0783.1', 'A0783.2', 'KTC', 'A0155',
								'A0175','A0176','A0787','A0179','A0182','A0471','A0184','A0555',
								'A0129',

							);

							$report['hoaSinhMienDich'] = array();
							foreach($array_HS as $ma_hs){
								if(!is_null($this->themDichVu($ma_hs, $arr_MaDV))){
									array_push($report['hoaSinhMienDich'], $this->themDichVu($ma_hs, $arr_MaDV));
								}
								
							}
								

						// XÉT NGHIỆM NƯỚC TIỂU
							$array_NT= array(
								'A0080.1','A0080.2','A0080.3','A0080.4','A0080.5',
								'A0080.6','A0080.7','A0080.8','A0080.9','A0080.10','A0080.11',
								'A0051','A0067','A0068', 'A0069', 'A070', 'A0071',
								'A0072','A0073','A0074', 'A0075', 'A0076', 'A0077',
								'A0078','A0084','A0085', 'A0086', 'A0087.1', 'A0087.2',
								'A0087.3','A0087.4','A0089', 'A0090', 'A0092', 'A0093',
								'A0094','A0095','A0096'
							);

							$report['nuocTieu'] = array();
							foreach($array_NT as $ma_nt){
								if(!is_null($this->themDichVu($ma_nt, $arr_MaDV))){
									array_push($report['nuocTieu'], $this->themDichVu($ma_nt, $arr_MaDV));
								}
							}	

						// CÔNG THỨC MÁU
							$array_CTM = array(
								'A0003.1','A0003.2','A0003.3','A0003.4', 'A0003.5','A0003.6','A0003.7','A0003.8',
								'A0003.9','A0003.10','A0003.11','A0003.12', 'A0003.13','A0003.14','A0003.15','A0003.16',
								'A0003.17','A0003.18','A0003.19','A0003.20', 'A0003.21','A0003.22','A0003.23','A0003.24',
								'A0003.25','A0003.26',
								'A0005','A0013','A0006.1', 'A0006.2', 'A0012.1', 'A0012.2',
								'A0012.3','A0012.4','A0012.5', 'A0012.6', 'A0012.7', 'A0012.8',
								'A0001.1','A0001.2','A00011', 'A00012'
							);

							$report['congThucMau'] = array();
							foreach($array_CTM as $ma_ctm){
								if(!is_null($this->themDichVu($ma_ctm, $arr_MaDV))){
									array_push($report['congThucMau'], $this->themDichVu($ma_ctm, $arr_MaDV));
								}
							}	
								
						// ĐÔNG MÁU
							$array_DM = array(
								'A0007.1','A0007.2','A0007.3','A0008.1','A0008.2','A0010.1','A0010.2','A0580','A0009'
							);

							$report['dong_mau'] = array();
							foreach($array_DM as $ma_dm){
								if(!is_null($this->themDichVu($ma_dm, $arr_MaDV))){
									array_push($report['dong_mau'], $this->themDichVu($ma_dm, $arr_MaDV));
								}
							}	
							
						// ĐIỆN DI HST
							$array_HST = array(
								'A0012.1','A0012.2','A0012.3','A0012.4','A0012.5','A0012.6','A0012.7','A0012.8'
							);


							$report['HST'] = array();
							foreach($array_HST as $ma_hst){
								if(!is_null($this->themDichVu($ma_hst, $arr_MaDV))){
									array_push($report['HST'], $this->themDichVu($ma_hst, $arr_MaDV));
								}
							}	
							
						// SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia
							$array_shpt = array(
								'A0504','A0505','A0506','A0507', 
								'A0508','A0509','A0510','A0511',
								'A0512','A0513','A0514','A0515',
								'A0516','A0517','A0518','A0519',
								'A0520','A0521','A0522'
							);

							$report['sinh_hoc_phan_tu'] = array();
							foreach($array_shpt as $ma_shpt){
								if(!is_null($this->themDichVu($ma_shpt, $arr_MaDV))){
									array_push($report['sinh_hoc_phan_tu'], $this->themDichVu($ma_shpt, $arr_MaDV));
								}
							}
							
						// VI SINH
							$array_vs = array(
								'A0110','A0111','A0112','A0117','A0118','A0119','A0121','A0123','A0124','A0283',
								'A0784','A0057','A0283.1','A0283.2','A0057.1','A0057.2','A0057.3','A0115','A0140',
								'A0141','A0142','A0149','A0154','A0216','A0217','A0460','A0785',
								'A03711.0','A03711.1','A03711.2', 'A03711.3', 'A03711.4',
								'A0240','A0451','A0403','A0365','A0364','A0392'
							);
							$report['viSinh'] = array();
							foreach($array_vs as $ma_vs){
								if(!is_null($this->themDichVu($ma_vs, $arr_MaDV))){
									array_push($report['viSinh'], $this->themDichVu($ma_vs, $arr_MaDV));
								}
							}

						// SOI TƯƠI ÂM ĐẠO
							$array_stad = array(
								'A0255.1','A0255.2','A0255.3','A0255.4','A0255.5',
								'A0255.6','A0255.7','A0255.8','A0255.9','A0255.10'
							);
							$report['soiTuoiAmDao'] = array();
							foreach($array_stad as $ma_stad){
								if(!is_null($this->themDichVu($ma_stad, $arr_MaDV))){
									array_push($report['soiTuoiAmDao'], $this->themDichVu($ma_stad, $arr_MaDV));
								}
							}

						// NHÓM MÁU
							$array_nm = array(
								'A0001.1','A0001.2'
							);

							$report['NhomMau'] = array();
							foreach($array_nm as $ma_nm){
								if(!is_null($this->themDichVu($ma_nm, $arr_MaDV))){
									array_push($report['NhomMau'], $this->themDichVu($ma_nm, $arr_MaDV));
								}
							}

						// CHỈ SỐ KHÁC
							$array_csk = array(
								'A0058', 'A0061'
							);
							$report['ChiSoKhac'] = array();
							foreach($array_csk as $ma_csk){
								if(!is_null($this->themDichVu($ma_csk, $arr_MaDV))){
									array_push($report['ChiSoKhac'], $this->themDichVu($ma_csk, $arr_MaDV));
								}
							}
							// $report['ChiSoKhac']['ChiSoKhac'] 			= '';
							

						// Date & hospital
							$report['date'] 	= $examination_date;
							$report['hospital'] = $hospital;
					
						$examination_report = json_encode($report);
					}
				}
				
				$annualRecord = $this->ehc->getAnnualCheckupRecord($annual_checkup_id);
				log_message('error', json_encode($annualRecord['data']));
				$dataUpdate = array(
					// 'user_id' 			=> $annualRecord['user_id'],
					'employee_id' 		=> '',
					'phone_number' 		=> $sodienthoai,
					'company_id' 		=> $noilamviec,
					'full_name' 		=> $tenbenhnhan,
					'gender' 			=> $gender,
					'birthdate' 		=> $birthdate,
					'Occupation' 		=> 'auto',
					'Hospital' 			=> $hospital,
					'hospital_id'		=> $hospital_id,
					'examination_date' 	=> $examination_date,
					'examination_report'=> $examination_report
				);

				//Update vào DB
				$this->service->updatePHR($annual_checkup_id, $dataUpdate);

				$dataLog = array(
					'id_treatment' 	=> $idluotkham,
					'annual_checkup_id' => $annual_checkup_id,
					'phoneNumber' 	=> $sodienthoai,
					'fullName' 		=> $tenbenhnhan,
					'type'			=> 'khach-le',
					'exam_date' 	=> $examination_date,
					'gender' 		=> $gender,
					'birthdate' 	=> $birthdate,
					'conclusion' 	=> $chandoan,
					'result' 		=> 'Cập nhật thông tin thành công'
				);
				$this->ehc->addHistoryUpdate($dataLog);
			}
			
		}else{
			//Khách đoàn
		}
	}

	public function themDichVu($maDV, $array_DV){
		foreach($array_DV as $dv){
			if($dv[1] == $maDV){
				$result['maDV'] 		= $dv[0]['madv'];
				$result['tenDV'] 		= $dv[0]['tendv'];
				$result['ketqua']		= $dv[0]['ketqua'];
				$result['file']			= $dv[0]['ketquafile'];
			}
		}

		return $result;
	}

	public function themChiSoChuan($data, $gender){
		$standard['fullName'] = trim($data['tendv']);
		$standard['codeName'] = $data['madv'];
		// log_message('error', $standard['codeName']);

		if(trim($data['donvitinh_ketqua']) != ''){
			$standard['unit'] = $data['donvitinh_ketqua'];
		}else{
			$standard['unit'] = '';
		}

		$gtbt = $data['giatribinhthuong'];
		$gtbt = str_replace('–','-',$gtbt);
		$gtbt = str_replace('>','>',$gtbt);
		$gtbt = str_replace('<','<',$gtbt);
		
		if($gender == 'Nam' || $gender == 'nam'){
			$standard['textMale'] = $gtbt;
			if($gtbt != ''){
				if(trim($gtbt) === 'Âm Tính'){
					$standard['minMale'] 	= null;
					$standard['maxMale'] 	= null;
				}else if(strpos($gtbt,'-')){
					$mangGiaTri = explode('-',$gtbt);
					$standard['minMale'] 	= $mangGiaTri[0];
					$standard['maxMale'] 	= $mangGiaTri[1];
				}else if(strpos($gtbt,'>') >= 0){
					$standard['minMale'] 	= trim(str_replace('>','',$gtbt));
					$standard['maxMale'] 	= null;
				}else if(strpos($gtbt,'<') >= 0){
					$standard['minMale'] 	= null;
					$standard['maxMale'] 	= trim(str_replace('<','',$gtbt));
				}else{
					$standard['minMale'] 	= null;
					$standard['maxMale'] 	= null;
				}
			}else{
				$standard['minMale'] 	= null;
				$standard['maxMale'] 	= null;
			}
			
		}else if($gender == 'Nữ' || $gender == 'Nu' || $gender == 'nu'){
			$standard['textFemale'] = $gtbt;
			if($gtbt != ''){
				if(trim($gtbt) === 'Âm Tính'){
					$standard['minFemale'] 	= null;
					$standard['maxFemale'] 	= null;
				}else if(strpos($gtbt,'-')){
					$mangGiaTri = explode('-',$gtbt);
					$standard['minFemale'] 	= trim($mangGiaTri[0]);
					$standard['maxFemale'] 	= trim($mangGiaTri[1]);
				}else if(strpos($gtbt,'>') >= 0){
					$standard['minFemale'] 	= trim(str_replace('>','',$gtbt));
					$standard['maxFemale'] 	= null;
				}else if(strpos($gtbt,'<') >= 0){
					$standard['minFemale'] 	= null;
					$standard['maxFemale'] 	= trim(str_replace('<','',$gtbt));
				}else{
					$standard['minFemale'] 	= null;
					$standard['maxFemale'] 	= null;
				}
			}else{
				$standard['minFemale'] 	= null;
				$standard['maxFemale'] 	= null;
			}
		}

		if($data['ketqua'] != ''){
			$this->ehc->updateStandardValue($standard);
		}
	}

	public function viewPHR($annual_checkup_id){
		if(session()->has('user')){
			$data['phrInfo'] = $this->ehc->getAnnualCheckupRecord($annual_checkup_id);

            if ($data['phrInfo'] && $data['phrInfo']['occ'] == 'auto') {
                $data['examInfo'] = json_decode($data['phrInfo']['data'], true);

                $data['the_luc']            = $data['examInfo']['theLuc'];
                $data['kham_lam_san']       = $data['examInfo']['khamLamSan'];
                $data['chan_doan_hinh_anh'] = $data['examInfo']['chuanDoanHinhAnh'];
                $data['hoa_sinh']           = $data['examInfo']['hoaSinhMienDich'];
                $data['nuoc_tieu']          = $data['examInfo']['nuocTieu'];
                $data['cong_thuc_mau']      = $data['examInfo']['congThucMau'];
                $data['dong_mau']           = $data['examInfo']['dong_mau'];
                $data['hst']                = $data['examInfo']['HST'];
                $data['sinh_hoc_phan_tu']   = $data['examInfo']['sinh_hoc_phan_tu'];
                $data['vi_sinh']            = $data['examInfo']['viSinh'];
                $data['nhom_mau']           = $data['examInfo']['NhomMau'];
                $data['khac']               = $data['examInfo']['ChiSoKhac'];
            }
			$data = $this->getMasterLayout($data, 'Trang quản trị', 'ehc/viewPHR');
			return view('home/main', $data);
		}else{
			return redirect()->to('/login');
		}
		
	}

	public function getPresDrug(){
		$ehc_baseUrl = 'https://api.1vietnam.net';
		$getAuthToken = json_decode($this->getAuthToken(), true);

		$token 		= $this->setting->getSettingValue(['settingType'=> 'ehc', 'settingName' => 'ehc-token']);

		$time = 720;
		$limit = 300;
		$start = date('YmdHi',strtotime('-'.$time.' hour'));
		$end = date('YmdHi');

		if($getAuthToken['status'] == 'success'){
			//Toàn bộ đợt điều trị trong khoảng $time
			$all_PresDrugApiData = array(
				'token' => $getAuthToken['token'],
				'post' 	=> array(),
				'url'	=> $ehc_baseUrl.'/api/PresDrug/All?api='.$token['settingValue'].'&fromdate='.$start.'&todate='.$end.'&limit='.$limit,
				'type' 	=> 0		// type=0: method get; type=1: method post 
			);
			$presDrug_All = $this->getAPIResult($all_PresDrugApiData);
			print_r($presDrug_All);die();
		}
	}

	public function phrDetail($visitId){
        if(session()->has('user')){
            $data['user'] 		= session()->get('user');
            $phrInfo			= $this->ehc->getVisitInfo('khach-le', 'd4u', $visitId);
            $data['phrInfo']	= $phrInfo['info'];
            // dd($data['phrInfo']);
            if($data['phrInfo'] != null){
                $data['examInfo']	= json_decode($data['phrInfo']['examination_report'], true);

                $examInfo 			= json_decode($data['phrInfo']['examination_report'], true);

                // Khám Lâm Sàng
                $data['kham_lam_sang'] = false;
                if(isset($examInfo['khamLamSan'])){
                    $khamLamSang 		= $examInfo['khamLamSan'];
                    $khamLamSang_key	= array_keys( $khamLamSang );
                    foreach($khamLamSang_key as $k => $kls){
                        if(strpos($kls, 'check') !== false){
                            unset($khamLamSang_key[$k]);
                        }
                    }

                    foreach($khamLamSang_key as  $value){
                        $gender = null;
                        $shortName = $value;
                        $indexValue = $khamLamSang[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['kham_lam_sang'][$value] = $this->showIndex($indexValue, $shortName, $gender, null);
                        }

                    }
                }else{
                    $data['kham_lam_sang'] = false;
                }

                // Chẩn đoán hình ảnh
                $data['chan_doan_hinh_anh'] = false;
                if(isset($examInfo['chuanDoanHinhAnh'])){
                    $chanDoanHinhAnh = $examInfo['chuanDoanHinhAnh'];
                    $chanDoanHinhAnh_key	= array_keys( $chanDoanHinhAnh );

                    foreach($chanDoanHinhAnh_key as $k => $cdha){
                        if(strpos($cdha, 'check') !== false){
                            unset($chanDoanHinhAnh_key[$k]);
                        }
                    }

                    foreach($chanDoanHinhAnh_key as  $value){
                        $gender = null;
                        $shortName = $value;
                        $indexValue = $chanDoanHinhAnh[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['chan_doan_hinh_anh'][$value] = $this->showIndex($indexValue, $shortName, $gender, null);
                        }
                    }
                }else{
                    $data['chan_doan_hinh_anh'] = false;
                }

                // Thăm dò chức năng
                $data['tham_do_chuc_nang'] = false;
                if(isset($examInfo['thamDoChucNang'])){
                    $thamDoChucNang = $examInfo['thamDoChucNang'];
                    $thamDoChucNang_key	= array_keys( $thamDoChucNang );

                    foreach($thamDoChucNang_key as $k => $tdcn){
                        if(strpos($tdcn, 'check') !== false){
                            unset($thamDoChucNang_key[$k]);
                        }
                    }

                    foreach($thamDoChucNang_key as  $value){

                        $gender = null;
                        $shortName = $value;
                        $indexValue = $thamDoChucNang[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['tham_do_chuc_nang'][$value] = $this->showIndex($indexValue, $shortName, $gender, null);
                        }
                    }

                }else{
                    $data['tham_do_chuc_nang'] = false;
                }

                // Hóa sinh miễn dịch
                $data['hoa_sinh_mien_dich'] = false;
                if(isset($examInfo['hoaSinhMienDich'])){
                    $hoaSinhMienDich = $examInfo['hoaSinhMienDich'];
                    $hoaSinhMienDich_key	= array_keys( $hoaSinhMienDich );

                    foreach($hoaSinhMienDich_key as $k => $hsmd){
                        if(strpos($hsmd, 'check') !== false){
                            unset($hoaSinhMienDich_key[$k]);
                        }
                    }

                    foreach($hoaSinhMienDich_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $hoaSinhMienDich[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['hoa_sinh_mien_dich'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['hoa_sinh_mien_dich'] = false;
                }
                // Nước tiểu
                $data['nuoc_tieu'] = false;
                if(isset($examInfo['nuocTieu'])){
                    $nuocTieu = $examInfo['nuocTieu'];
                    $nuocTieu_key	= array_keys( $nuocTieu );

                    foreach($nuocTieu_key as $k => $nt){
                        if(strpos($nt, 'check') !== false){
                            unset($nuocTieu_key[$k]);
                        }
                    }

                    foreach($nuocTieu_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $nuocTieu[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['nuoc_tieu'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['nuoc_tieu'] = false;
                }
                // Công thức máu
                $data['cong_thuc_mau'] = false;
                if(isset($examInfo['congThucMau'])){
                    $congThucMau = $examInfo['congThucMau'];
                    $congThucMau_key	= array_keys( $congThucMau );

                    foreach($congThucMau_key as $k => $ctm){
                        if(strpos($ctm, 'check') !== false){
                            unset($congThucMau_key[$k]);
                        }
                    }

                    foreach($congThucMau_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $congThucMau[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['cong_thuc_mau'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['cong_thuc_mau'] = false;
                }
                // Đông máu
                $data['dongmau'] = false;
                if(isset($examInfo['dong_mau'])){
                    $dong_mau = $examInfo['dong_mau'];
                    $dong_mau_key	= array_keys( $dong_mau );

                    foreach($dong_mau_key as $k => $dm){
                        if(strpos($dm, 'check') !== false){
                            unset($dong_mau_key[$k]);
                        }
                    }

                    foreach($dong_mau_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $dong_mau[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['dongmau'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['dongmau'] = false;
                }

                // Nhóm máu
                $data['nhom_mau'] = false;
                if(isset($examInfo['NhomMau'])){
                    $NhomMau = $examInfo['NhomMau'];
                    $NhomMau_key	= array_keys( $NhomMau );

                    foreach($NhomMau_key as $k => $nm){
                        if(strpos($nm, 'check') !== false){
                            unset($NhomMau_key[$k]);
                        }
                    }

                    foreach($NhomMau_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $NhomMau[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['nhom_mau'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['nhom_mau'] = false;
                }
                // Điện di Huyết Sắc Tố
                $data['huyet_sac_to'] = false;
                if(isset($examInfo['HST'])){
                    $HST = $examInfo['HST'];
                    $HST_key	= array_keys( $HST );

                    foreach($HST_key as $k => $hst){
                        if(strpos($hst, 'check') !== false){
                            unset($HST_key[$k]);
                        }
                    }

                    foreach($HST_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $HST[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['huyet_sac_to'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['huyet_sac_to'] = false;
                }
                // Sinh học phân tử
                $data['shpt'] = false;
                if(isset($examInfo['sinh_hoc_phan_tu'])){
                    $sinh_hoc_phan_tu = $examInfo['sinh_hoc_phan_tu'];
                    $sinh_hoc_phan_tu_key	= array_keys( $sinh_hoc_phan_tu );

                    foreach($sinh_hoc_phan_tu_key as $k => $shpt){
                        if(strpos($shpt, 'check') !== false){
                            unset($sinh_hoc_phan_tu_key[$k]);
                        }
                    }

                    foreach($sinh_hoc_phan_tu_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $sinh_hoc_phan_tu[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['shpt'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['shpt'] = false;
                }

                // Vi sinh
                $data['vi_sinh'] = false;
                if(isset($examInfo['viSinh'])){
                    $viSinh = $examInfo['viSinh'];
                    $viSinh_key	= array_keys( $viSinh );

                    foreach($viSinh_key as $k => $vs){
                        if(strpos($vs, 'check') !== false){
                            unset($viSinh_key[$k]);
                        }
                    }

                    foreach($viSinh_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $viSinh[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['vi_sinh'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['vi_sinh'] = false;
                }

                // Chỉ số khác
                $data['chi_so_khac'] = false;
                if(isset($examInfo['ChiSoKhac'])){
                    $ChiSoKhac = $examInfo['ChiSoKhac'];
                    $ChiSoKhac_key	= array_keys( $ChiSoKhac );

                    foreach($ChiSoKhac_key as $k => $csk){
                        if(strpos($csk, 'check') !== false){
                            unset($ChiSoKhac_key[$k]);
                        }
                    }

                    foreach($ChiSoKhac_key as  $k => $value){
                        $gender = $data['phrInfo']['gender'];
                        $shortName = $value;
                        $indexValue = $ChiSoKhac[$value];
                        if($indexValue != '' && $indexValue != null){
                            $data['chi_so_khac'][$value] = $this->showIndex($indexValue, $shortName, $gender, $k+1);
                        }
                    }
                }else{
                    $data['chi_so_khac'] = false;
                }

            }
            // $data = $this->getMasterLayout($data, 'Thông tin khám bệnh', 'AfterLogin/pages/listPHR/detailPHR');
			$data = $this->getMasterLayout($data, 'Trang quản trị', 'ehc/detailPHR');
            // return view('AfterLogin/main', $data);
			return view('home/main', $data);
        }else{
            return redirect()->to('/dang-nhap');
        }
    }

	public function showIndex($indexValue, $shortName, $gender, $key){
        $standardValue = $this->standard->where(['shortName' => $shortName, 'gender' => $gender])->first();
        $parent = $this->standard->where(['id' => $standardValue['parentId']])->first();
        $standardValue['parent'] = $parent['shortName'];
        switch ($standardValue['parent']) {
            case 'khamLamSan':
                $result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 font-weight-bold">'.$indexValue.'</span>';
                break;
            case 'chuanDoanHinhAnh':
                $result = '<span class="col-md-4">'.$standardValue['fullName'].': </span><span class="col-md-2 font-weight-bold">'.$indexValue.'</span>';
                break;
            case 'thamDoChucNang':
                $result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 font-weight-bold">'.$indexValue.'</span>';
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
                $result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 font-weight-bold">'.$indexValue.'</span>';
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
                $result = '<span class="col-md-2">'.$standardValue['fullName'].': </span><span class="col-md-4 font-weight-bold">'.$indexValue.'</span>';
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

}
