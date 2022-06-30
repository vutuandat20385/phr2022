<?php

namespace App\Controllers;

require_once 'TechAPI/bootstrap.php';

require 'vendor/autoload.php';

use App\Services\PHRService;
use App\Services\SettingService;
use App\Services\HistoryService;

use App\Forms\Forms;

class PHR extends BaseController {
	
	public function __construct(){
		$this->phr = new PHRService();
		$this->setting = new SettingService();
		$this->history = new HistoryService();
		$this->form = new Forms();
	}

	public function index() {
		
	}

	public function d4uSingle(){
		$data['panelTitle'] = 'Danh sách Bệnh án Khách lẻ';
        $data['user'] 	= session()->get('user');
		$data['type'] = 'khach-le';

		$formData = array(
			'title' 			=> 'THÊM BỆNH ÁN KHÁM LẺ - TRỰC TIẾP',
			'hospital_id' 		=> 'D4U',
			'hospital'			=> 'Phòng khám Bác sỹ gia đình Doctor4U',
			'default_send_SMS'	=> $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => DEFAULT_SMS_RESULT]),
			'type'				=> $data['type'],
			'action'			=> 'trang-quan-tri/benh-an/d4u-khach-le/import'
		);
		$data['form_importModal'] = $this->form->form_importModal($formData);

		$info=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

		$data['info'] = $info;

		$dataAllPHR = array(
			'start' => '',
			'limit' => '',
			'info' => $info,
		);
        $allPHR = $this->phr->getAllD4USinglePHR($dataAllPHR);

        if(!empty($allPHR)){
            $totalPHR = count($allPHR);
            service('pager')->makeLinks($page+1, $perPage, $totalPHR);
            $start = $page * $perPage;
			$dataPHR = array(
				'start' => $start,
				'limit' => $perPage,
				'info' => $info,
			);
            $data['posts'] = $this->phr->getAllD4USinglePHR($dataPHR);

            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $totalPHR - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($totalPHR/$perPage);

        }else{
            $data['posts'] = false;
        }
        

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách Bệnh án Khách lẻ | Doctor4U', 'AfterLogin/pages/phr/d4uSingle');
		return view('AfterLogin/main', $data);
	}

	public function d4uGroup(){
		$data['panelTitle'] = 'Danh sách Bệnh án Khách đoàn';
        $data['user'] 	= session()->get('user');

		$info=($this->request->getVar('info')!==null) ? $this->request->getVar('info'):'';
        $page=(int)(($this->request->getVar('page')!==null)?$this->request->getVar('page'):1)-1;
		$perPage =  20;

		$data['info'] = $info;

		$formData = array(
			'title' 			=> 'THÊM BỆNH ÁN KHÁM ĐOÀN - TRỰC TIẾP',
			'hospital_id' 		=> 'D4U',
			'hospital'			=> 'Phòng khám Bác sỹ gia đình Doctor4U',
			'default_send_SMS'	=> $this->setting->getSettingValue(['settingType'=> 'sms', 'settingName' => DEFAULT_SMS_RESULT]),
			'type'				=> $data['type'],
			'action'			=> 'trang-quan-tri/benh-an/d4u-khach-doan/import'
		);
		$data['form_importModal'] = $this->form->form_importModal($formData);

		$dataAllPHR = array(
			'start' => '',
			'limit' => '',
			'info' => $info,
		);
        $allPHR = $this->phr->getAllD4UGroupPHR($dataAllPHR);

        if(!empty($allPHR)){
            $totalPHR = count($allPHR);
            service('pager')->makeLinks($page+1, $perPage, $totalPHR);
            $start = $page * $perPage;
			$dataPHR = array(
				'start' => $start,
				'limit' => $perPage,
				'info' => $info,
			);
            $data['posts'] = $this->phr->getAllD4UGroupPHR($dataPHR);

            foreach($data['posts'] as $k => $value){
                $data['posts'][$k]['index'] = $totalPHR - $start - $k;
            }

            $data['currentPage'] = $page+1;
            $data['totalPages'] = ceil($totalPHR/$perPage);

        }else{
            $data['posts'] = false;
        }
        

        $data = $this->getAfterLoginLayout($data, 'Trang quản trị | Danh sách Bệnh án Khách đoàn | Doctor4U', 'AfterLogin/pages/phr/d4uGroup');
		return view('AfterLogin/main', $data);
	}

	public function d4uCovid(){
		
	}

	public function detailSingle($visitId){
		$data['panelTitle'] = 'Chi tiết bệnh án';
        $data['user'] 	= session()->get('user');

		$type = 'khach-le';
		$phrInfo = $this->phr->getVisitInfo($type, $visitId);

		$data['phrInfo']	= $phrInfo['info'];

		if($data['phrInfo'] != null){
			if($data['phrInfo']['Occupation'] != 'auto'){
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
								$data['kham_lam_sang'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, null);
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
								$data['chan_doan_hinh_anh'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, null);
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
								$data['tham_do_chuc_nang'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, null);
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
								$data['hoa_sinh_mien_dich'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['nuoc_tieu'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['cong_thuc_mau'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['dongmau'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['nhom_mau'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['shpt'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
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
								$data['vi_sinh'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
							}
						}
					}else{
						$data['vi_sinh'] = false;
					}
	
				// Soi Tươi Âm Đạo
					$data['soi_tuoi_am_dao'] = false;
					if(isset($examInfo['soiTuoiAmDao'])){
						$soiTuoiAmDao = $examInfo['soiTuoiAmDao'];
						$soiTuoiAmDao_key	= array_keys( $soiTuoiAmDao );
						
						foreach($soiTuoiAmDao_key as $k => $stad){
							if(strpos($stad, 'check') !== false){
								unset($soiTuoiAmDao_key[$k]);
							}
						}
			
						foreach($soiTuoiAmDao_key as  $k => $value){
							$gender = $data['phrInfo']['gender'];
							$shortName = $value;
							$indexValue = $soiTuoiAmDao[$value];
							if($indexValue != '' && $indexValue != null){
								$data['soi_tuoi_am_dao'][$value] = $this->phr->showIndex($indexValue, $shortName, $gender, $k+1);
							}
						}
					}else{
						$data['soi_tuoi_am_dao'] = false;
					}
		
				// Chỉ số khác
					$data['chi_so_khac'] = false;
					if(isset($examInfo['ChiSoKhac'])){
	
						$data['chi_so_khac'] = $examInfo['ChiSoKhac'];
					}else{
						$data['chi_so_khac'] = false;
					}
	
				$data = $this->getAfterLoginLayout($data, 'Trang quản trị | Chi tiết bệnh án | Doctor4U', 'AfterLogin/pages/phr/detailSingle');
			}else{

				$data['examInfo'] = json_decode($data['phrInfo']['examination_report'], true);

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

				$data = $this->getAfterLoginLayout($data, 'Trang quản trị | Chi tiết bệnh án | Doctor4U', 'AfterLogin/pages/phr/detailSingleAuto');
			}	
		}
		
		return view('AfterLogin/main', $data);
	}


}
