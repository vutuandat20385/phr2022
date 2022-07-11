<?php

namespace App\Controllers;
require_once 'TechAPI/bootstrap.php';
require 'vendor/autoload.php';

use App\Services\ImportService;
use App\Services\HistoryService;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Services\ListPHRService;

class ImportController extends BaseController{
    public function __construct(){
        $this->import = new ImportService();
        $this->history = new HistoryService();
	}

    public function d4uListSingleImport(){
        $hospital 		= $this->request->getVar('hospital');
		$hospital_id 	= $this->request->getVar('hospitalId');

		$target 		= './public/csvfile';
		$file 			= $this->request->getFile('uploadFile');

		$checkSMS		= $this->request->getVar('checkSMS');

		$ext 			= $file->guessExtension();
		$timeLabel 		= time().'_'.date('Ymd');

		if (in_array($ext, ['csv', 'xls', 'xlsx'])) {
			$newName 	= $file->getName().'_'.$timeLabel.'.'.$ext;
			$inputFileName = './public/csvfile/'.$newName;
			
			$file->move( $target , $newName);

			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
			$spreadsheet = $spreadsheet->getActiveSheet();
			$data_array =  $spreadsheet->toArray();

			$dataPHR = array();
			$count_success = 0;			// Đếm số bản ghi import thành công
			$count_fail = 0;			// Đếm số bản ghi import thất bại
			$arrayHistory = array();	// Mảng lưu lịch sử các bản ghi import

			foreach($data_array as $i => $value){
				if($i > 1 && $value[2] !='' && $value[3] !=''){
					$phoneNumber =  preg_replace('/\s+/', '', $value[3]);
                    $phoneNumber    = $this->convertPhoneDigit($phoneNumber);
					// print_r($value);die();
					
					//Tên bệnh nhân
					if(trim($value[2]) != ''){
						$name = trim($value[2]);
					}else{
						$name = null;
					}
					
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
				
					//Giới tính
					if(trim($value[5]) != ''){
						$gender = trim($value[5]);
					}else{
						$gender = 'NON';
					}

					//Ngày khám
					if(trim($value[207]) != ''){
						$ed = \DateTime::createFromFormat('d/m/Y', trim($value[207]));
						$exam_date = $ed->format('Y-m-d');
					}else{
						$exam_date = null;
					}

				// THÔNG TIN CÁ NHÂN
					$dataPHR[$i]['ma_nv'] 		= $value[1];
					$dataPHR[$i]['name'] 		= $value[2];
					$dataPHR[$i]['SDT'] 		= $phoneNumber;
					$dataPHR[$i]['birth'] 		= $birthdate;
					$dataPHR[$i]['gender'] 		= $value[5];
					$dataPHR[$i]['don_vi'] 		= $value[6];
					$dataPHR[$i]['dia_chi'] 	= $value[7];
					$dataPHR[$i]['chan_doan'] 	= $value[8];
			
				// KẾT LUẬN TƯ VẤN - ĐỀ NGHỊ
					$dataPHR[$i]['ketLuan'] 	= $value[9];
					$dataPHR[$i]['deNghi'] 		= $value[10];
					$dataPHR[$i]['ketLuanXN'] 	= $value[11];
					$dataPHR[$i]['don_thuoc'] 	= $value[12];

				// KHÁM THỂ LỰC
					$dataPHR[$i]['theLuc']['chieu_cao'] 			= $value[13];
					$dataPHR[$i]['theLuc']['can_nang'] 				= $value[14];
					$dataPHR[$i]['theLuc']['huyet_ap'] 				= $value[15];	
					$dataPHR[$i]['theLuc']['mach'] 					= $value[16];

				// KHÁM LÂM SÀNG
					$dataPHR[$i]['khamLamSan']['tuan_hoan'] 				= $value[17];	
					$dataPHR[$i]['khamLamSan']['ho_hap'] 					= $value[18];
					$dataPHR[$i]['khamLamSan']['tieu_hoa'] 					= $value[19];
					$dataPHR[$i]['khamLamSan']['than'] 						= $value[20];

					$dataPHR[$i]['khamLamSan']['noi_tiet'] 					= $value[21];
					$dataPHR[$i]['khamLamSan']['xuong_khop'] 				= $value[22];
					$dataPHR[$i]['khamLamSan']['than_kinh'] 				= $value[23];
					$dataPHR[$i]['khamLamSan']['tam_than'] 					= $value[24];

					$dataPHR[$i]['khamLamSan']['eye'] 						= $value[25];
					$dataPHR[$i]['khamLamSan']['tai_mui_hong'] 				= $value[26];
					$dataPHR[$i]['khamLamSan']['rang_ham_mat'] 				= $value[27];
					$dataPHR[$i]['khamLamSan']['da_lieu'] 					= $value[28];

					$dataPHR[$i]['khamLamSan']['san_phu_khoa'] 				= $value[29];

				// CHẨN ĐOÁN HÌNH ẢNH
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBungTongQuat']		= $value[30];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu']			= $value[31];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap']			= $value[32];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmCoXuongKhop']		= $value[33];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau']		= $value[34];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimTongQuat']		= $value[35];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmMach'] 				= $value[36];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmMachCanh']			= $value[37];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBiu']				= $value[38];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmThai']				= $value[39];
					$dataPHR[$i]['chuanDoanHinhAnh']['holterHuyetAp']			= $value[40];
					$dataPHR[$i]['chuanDoanHinhAnh']['doLoangXuong']			= $value[41];
					$dataPHR[$i]['chuanDoanHinhAnh']['doLuuHuyetNao']			= $value[42];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTienLietTuyen']		= $value[43];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBuongTrung']		= $value[44];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhopGoi']			= $value[45];
					$dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTinhMachChiDuoi']	= $value[46];
					$dataPHR[$i]['chuanDoanHinhAnh']['noiSoiTaiMuiHong']		= $value[47];
					$dataPHR[$i]['chuanDoanHinhAnh']['dienTimThuong']			= $value[48];

					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_ngucthang']		= $value[49];
					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_cotay']			= $value[50];
					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_cotsongco']		= $value[51];
					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_cotsongthatlung']	= $value[52];
					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_thang']			= $value[53];
					$dataPHR[$i]['chuanDoanHinhAnh']['xquang_2tuthe']			= $value[54];

				// HÓA SINH
					$dataPHR[$i]['hoaSinhMienDich']['crp'] 								= $value[69];
					$dataPHR[$i]['hoaSinhMienDich']['glucose'] 							= $value[70];
					$dataPHR[$i]['hoaSinhMienDich']['napDuong1H'] 						= $value[71];
					$dataPHR[$i]['hoaSinhMienDich']['napDuong2H'] 						= $value[72];

					$dataPHR[$i]['hoaSinhMienDich']['HbA1C'] 							= $value[73];
					$dataPHR[$i]['hoaSinhMienDich']['Ure'] 								= $value[74];
					$dataPHR[$i]['hoaSinhMienDich']['Creatinin'] 						= $value[75];
					$dataPHR[$i]['hoaSinhMienDich']['Acid_uric'] 						= $value[76];

					$dataPHR[$i]['hoaSinhMienDich']['Cholesterol_total'] 				= $value[77];
					$dataPHR[$i]['hoaSinhMienDich']['Cholesterol_HDL'] 					= $value[78];
					$dataPHR[$i]['hoaSinhMienDich']['Cholesterol_LDL'] 					= $value[79];
					$dataPHR[$i]['hoaSinhMienDich']['Triglycerid'] 						= $value[80];

					$dataPHR[$i]['hoaSinhMienDich']['AST_GOT'] 							= $value[81];
					$dataPHR[$i]['hoaSinhMienDich']['ALT_GPT'] 							= $value[82];
					$dataPHR[$i]['hoaSinhMienDich']['Albumin'] 							= $value[83];
					$dataPHR[$i]['hoaSinhMienDich']['GGT'] 								= $value[84];

					$dataPHR[$i]['hoaSinhMienDich']['ck_26_140_u_l'] 					= $value[85];
					$dataPHR[$i]['hoaSinhMienDich']['amylase'] 							= $value[86];
					$dataPHR[$i]['hoaSinhMienDich']['BilirubinTotal'] 					= $value[87];
					$dataPHR[$i]['hoaSinhMienDich']['BilirubinTrucTiep'] 				= $value[88];

					$dataPHR[$i]['hoaSinhMienDich']['BilirubinGianTiep'] 				= $value[89];
					$dataPHR[$i]['hoaSinhMienDich']['FEHuyetThanh'] 					= $value[90];
					$dataPHR[$i]['hoaSinhMienDich']['Ferritin'] 						= $value[91];
					$dataPHR[$i]['hoaSinhMienDich']['calci_toan_phan'] 					= $value[92];

					$dataPHR[$i]['hoaSinhMienDich']['ca2_11_129_mmol_l'] 				= $value[93];
					$dataPHR[$i]['hoaSinhMienDich']['Na'] 								=$value[94];
					$dataPHR[$i]['hoaSinhMienDich']['K'] 								= $value[95];
					$dataPHR[$i]['hoaSinhMienDich']['Cl'] 								= $value[96];

					$dataPHR[$i]['hoaSinhMienDich']['alp'] 								= $value[97];
					$dataPHR[$i]['hoaSinhMienDich']['prealbumin_20_40_mg_dl'] 			= $value[98];
					$dataPHR[$i]['hoaSinhMienDich']['ige_100_u_ml'] 					= $value[99];

				// MIỄN DỊCH
					$dataPHR[$i]['hoaSinhMienDich']['estradiol_pg_ml'] 					= $value[100];
					$dataPHR[$i]['hoaSinhMienDich']['progesterone_nmol_l'] 			= $value[101];
					$dataPHR[$i]['hoaSinhMienDich']['testosterone_24_87_ng_ml'] 		= $value[102];
					$dataPHR[$i]['hoaSinhMienDich']['T3'] 								= $value[103];

					$dataPHR[$i]['hoaSinhMienDich']['FT4'] 								= $value[104];
					$dataPHR[$i]['hoaSinhMienDich']['TSH'] 								= $value[105];
					$dataPHR[$i]['hoaSinhMienDich']['lh_2_12_iu_l'] 					= $value[106];
					$dataPHR[$i]['hoaSinhMienDich']['cortisol_171_536_nmol_l'] 			= $value[107];

					$dataPHR[$i]['hoaSinhMienDich']['fsh'] 							= $value[108];
					$dataPHR[$i]['hoaSinhMienDich']['Cryfra21_1'] 						= $value[109];
					$dataPHR[$i]['hoaSinhMienDich']['nse'] 								= $value[110];
					$dataPHR[$i]['hoaSinhMienDich']['SCC'] 								= $value[111];

					$dataPHR[$i]['hoaSinhMienDich']['AFP'] 								= $value[112];
					$dataPHR[$i]['hoaSinhMienDich']['CEA'] 								= $value[113];
					$dataPHR[$i]['hoaSinhMienDich']['PSA'] 								= $value[114];
					$dataPHR[$i]['hoaSinhMienDich']['psa_tu_do'] 						= $value[115];

					$dataPHR[$i]['hoaSinhMienDich']['CA72_4'] 							= $value[116];
					$dataPHR[$i]['hoaSinhMienDich']['CA15_3'] 							= $value[117];
					$dataPHR[$i]['hoaSinhMienDich']['CA125'] 							= $value[118];
					$dataPHR[$i]['hoaSinhMienDich']['CA19_9'] 							= $value[119];

					$dataPHR[$i]['hoaSinhMienDich']['anti_ccp'] 						= $value[209];
					$dataPHR[$i]['hoaSinhMienDich']['rf_dinhluong'] 					= $value[212];
					$dataPHR[$i]['hoaSinhMienDich']['anti_tg'] 							= $value[213];

				// XÉT NGHIỆM NƯỚC TIỂU
					$dataPHR[$i]['nuocTieu']['URO']							= $value[120];
					$dataPHR[$i]['nuocTieu']['GLU']							= $value[121];
					$dataPHR[$i]['nuocTieu']['BIL']							= $value[122];
					$dataPHR[$i]['nuocTieu']['KET']							= $value[123];

					$dataPHR[$i]['nuocTieu']['SG']							= $value[124];
					$dataPHR[$i]['nuocTieu']['BLD']							= $value[125];
					$dataPHR[$i]['nuocTieu']['pH']							= $value[126];
					$dataPHR[$i]['nuocTieu']['PRO']							= $value[127];

					$dataPHR[$i]['nuocTieu']['NT']							= $value[128];
					$dataPHR[$i]['nuocTieu']['LEU']							= $value[129];
					$dataPHR[$i]['nuocTieu']['VTC']							= $value[130];

				// CÔNG THỨC MÁU
					$dataPHR[$i]['congThucMau']['WBC'] 						= $value[131];
					$dataPHR[$i]['congThucMau']['NEU'] 						= $value[132];
					$dataPHR[$i]['congThucMau']['LYM'] 						= $value[133];
					$dataPHR[$i]['congThucMau']['MONO'] 					= $value[134];

					$dataPHR[$i]['congThucMau']['EOS'] 						= $value[135];
					$dataPHR[$i]['congThucMau']['BASO'] 					= $value[136];
					$dataPHR[$i]['congThucMau']['IG_TyLe'] 					= $value[137];
					$dataPHR[$i]['congThucMau']['BachCauTrungTinh'] 		= $value[138];

					$dataPHR[$i]['congThucMau']['BachCauLympho'] 			= $value[139];
					$dataPHR[$i]['congThucMau']['BachCauMoMo'] 				= $value[140];
					$dataPHR[$i]['congThucMau']['bachCauAcid'] 				= $value[141];
					$dataPHR[$i]['congThucMau']['BachCauBase'] 				= $value[142];

					$dataPHR[$i]['congThucMau']['IG_SoLuong'] 				= $value[143];
					$dataPHR[$i]['congThucMau']['RBC'] 						= $value[144];
					$dataPHR[$i]['congThucMau']['HGB'] 						= $value[145];
					$dataPHR[$i]['congThucMau']['HCT'] 						= $value[146];

					$dataPHR[$i]['congThucMau']['MCV'] 						= $value[147];
					$dataPHR[$i]['congThucMau']['MCH'] 						= $value[148];
					$dataPHR[$i]['congThucMau']['MCHC'] 					= $value[149];
					$dataPHR[$i]['congThucMau']['RDW_SD'] 					= $value[150];

					$dataPHR[$i]['congThucMau']['RDW_CV'] 					= $value[151];
					$dataPHR[$i]['congThucMau']['PLT'] 						= $value[152];
					$dataPHR[$i]['congThucMau']['MPV'] 						= $value[153];
					$dataPHR[$i]['congThucMau']['PCT'] 						= $value[154];

					$dataPHR[$i]['congThucMau']['PDW'] 						= $value[155];
					$dataPHR[$i]['congThucMau']['P_LCR'] 					= $value[156];
					$dataPHR[$i]['congThucMau']['maulang_1h'] 				= $value[210];
					$dataPHR[$i]['congThucMau']['maulang_2h'] 				= $value[211];

				// ĐÔNG MÁU
					$dataPHR[$i]['dong_mau']['pt']							= $value[157];
					$dataPHR[$i]['dong_mau']['pt70_140']					= $value[158];
					$dataPHR[$i]['dong_mau']['inr'] 						= $value[159];
					$dataPHR[$i]['dong_mau']['aptt_s'] 						= $value[160];
					$dataPHR[$i]['dong_mau']['aptt_phantram'] 				= $value[161];
					$dataPHR[$i]['dong_mau']['Fibrinogen'] 					= $value[162];
					$dataPHR[$i]['dong_mau']['D_dimer'] 					= $value[163];
					$dataPHR[$i]['dong_mau']['HuyetDo'] 					= $value[164];

				// ĐIỆN DI HST
					$dataPHR[$i]['HST']['HbA1']								= $value[165];
					$dataPHR[$i]['HST']['HbA2']								= $value[166];
					$dataPHR[$i]['HST']['HbF']								= $value[167];
					$dataPHR[$i]['HST']['HbE']								= $value[168];

					$dataPHR[$i]['HST']['hbs_zone_2']						= $value[169];
					$dataPHR[$i]['HST']['HbH']								= $value[170];
					$dataPHR[$i]['HST']['hbd_puhjab']						= $value[171];
					$dataPHR[$i]['HST']['Hb_Bart']							= $value[172];

				// SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia
					$dataPHR[$i]['sinh_hoc_phan_tu']['sea']					= $value[173];
					$dataPHR[$i]['sinh_hoc_phan_tu']['a37']					= $value[174];
					$dataPHR[$i]['sinh_hoc_phan_tu']['a42']					= $value[175];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cs']					= $value[176];

					$dataPHR[$i]['sinh_hoc_phan_tu']['qs']					= $value[177];
					$dataPHR[$i]['sinh_hoc_phan_tu']['shpt_28_a_g']			= $value[178];
					$dataPHR[$i]['sinh_hoc_phan_tu']['shpt_29_a_g']			= $value[179];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cap_aaac']			= $value[180];

					$dataPHR[$i]['sinh_hoc_phan_tu']['int_tg']				= $value[181];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cd14_15g']			= $value[182];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cd17_at']				= $value[183];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cd27_28c']			= $value[184];

					$dataPHR[$i]['sinh_hoc_phan_tu']['be_ga']				= $value[185];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cd_31_c']				= $value[186];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cs41_42_ttct']		= $value[187];
					$dataPHR[$i]['sinh_hoc_phan_tu']['cd43_gt']				= $value[188];

					$dataPHR[$i]['sinh_hoc_phan_tu']['cd71_72a']			= $value[189];
					$dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i1_ft']			= $value[190];
					$dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i1_ga']			= $value[191];
					$dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i5_gc']			= $value[192];

					$dataPHR[$i]['sinh_hoc_phan_tu']['ivs_ii_654_ct']		= $value[193];

				// VI SINH
					$dataPHR[$i]['viSinh']['HBsAgAuto'] 					= $value[194];
					$dataPHR[$i]['viSinh']['HBsAbDinhLuong'] 				= $value[195];
					$dataPHR[$i]['viSinh']['HBcAg'] 						= $value[196];
					$dataPHR[$i]['viSinh']['HBcAb'] 						= $value[197];

					$dataPHR[$i]['viSinh']['HBeAg'] 						= $value[198];
					$dataPHR[$i]['viSinh']['HBeAb'] 						= $value[199];
					$dataPHR[$i]['viSinh']['HAV'] 							= $value[200];
					$dataPHR[$i]['viSinh']['HAV_IgM'] 						= $value[201];

					$dataPHR[$i]['viSinh']['AntiHCV'] 						= $value[202];
					$dataPHR[$i]['viSinh']['hbv_dna'] 						= $value[203];
					
				// NHÓM MÁU
					$dataPHR[$i]['NhomMau']['ABO'] 							= $value[204];
					$dataPHR[$i]['NhomMau']['RH'] 							= $value[205];

				// CHỈ SỐ KHÁC
					$dataPHR[$i]['ChiSoKhac']['ChiSoKhac']					= $value[206];

				// SOI TƯƠI DỊCH ÂM ĐẠO
					$dataPHR[$i]['soiTuoiAmDao']['bachCau']					= $value[215];
					$dataPHR[$i]['soiTuoiAmDao']['cauKhuan']				= $value[216];
					$dataPHR[$i]['soiTuoiAmDao']['trucKhuan']				= $value[217];
					$dataPHR[$i]['soiTuoiAmDao']['songCauKhuan']			= $value[218];
					$dataPHR[$i]['soiTuoiAmDao']['bachCauHat']				= $value[219];
					$dataPHR[$i]['soiTuoiAmDao']['hongCau']					= $value[220];
					$dataPHR[$i]['soiTuoiAmDao']['nam']						= $value[221];
					$dataPHR[$i]['soiTuoiAmDao']['teBaoBieuMo']				= $value[222];
					$dataPHR[$i]['soiTuoiAmDao']['trichomonass']			= $value[223];
					$dataPHR[$i]['soiTuoiAmDao']['clueCell']				= $value[224];

				// Date & hospital
					$dataPHR[$i]['date'] 									= $value[207];
					$dataPHR[$i]['hospital'] 								= $value[208];

				// Tạo chuỗi json chứa các chỉ số
				$examination_report = json_encode($dataPHR[$i]);

					if($exam_date !== null && $exam_date <= date("Y-m-d") && $name != null && $phoneNumber !=''){
					
						$userData = array(
							'username' 		=> $phoneNumber,
							'phoneNumber' 	=> $phoneNumber,
							'password' 		=> '123456',
							'givenName' 	=> $name,
							'birthdate' 	=> $birthdate,
							'gender' 		=> $gender,
							'role' 			=> 'PATIENT',
							'providerRole'	=> 'c110f9bc-c65f-44a2-a028-2af7e8fff534',
						);	

						$rs = $this->registerPatient($userData);

						if (isset($rs['user']['uuid']) && $rs['user']['uuid'] != '') {	
						
							$account = 'Tài khoản mới tạo.';

							$data['username'] = $phoneNumber;
							$data['retired'] = 0;
							$user = $this->import->checkUserApi($data);

							$dataInsert = array(
								'user_id' 			=> $user['user_id'],
								'employee_id' 		=> $value[1],
								'phone_number' 		=> $phoneNumber,
								'company_id' 		=> $value[6],
								'full_name' 		=> $name,
								'gender' 			=> $gender,
								'birthdate' 		=> $birthdate,
								'Hospital' 			=> $hospital,
								'hospital_id'		=> $hospital_id,
								'examination_date' 	=> $exam_date,
								'examination_report'=> $examination_report
							);
							
							//Insert vào DB
							$insert_id = $this->import->addPHR($dataInsert);

							if($insert_id['status'] === true){
								//Gửi Notification đến Device cho user
								$date = date('d-m-Y');
								$this->import->send_notification($phoneNumber, $hospital, $date, $insert_id['id']);
								$count_success++; 

								// Chuẩn bị $dataSuccess để lưu vào history_import table
								$dataSuccess = array(
									'type'			=> 'success',
									'id'			=> $value[0],
									'employee_id'	=> $value[1],
									'full_name' 	=> $name,
									'phone_number' 	=> $phoneNumber,
									'status' 		=> $insert_id['msg'].' - '.$account
								);

								array_push($arrayHistory, $dataSuccess);

								if($checkSMS == 'sms'){
									// Send SMS đến SĐT bệnh nhân
									$settingName = CONTENT_SMS_RESULT_NEW;
									$msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
									$this->sendSMS($phoneNumber,'Doctor4U',$msg);
								}
								
							}else{
								$count_fail++;
								
								// Chuẩn bị $dataFail để lưu vào history_import table
								$dataFail = array(
									'type'			=> 'error',
									'id'			=> $value[0],
									'employee_id'	=> $value[1],
									'full_name' 	=> $name,
									'phone_number' 	=> $phoneNumber,
									'status' 		=> $insert_id['status']
								);

								array_push($arrayHistory, $dataFail);
							}
						}else if(isset($rs['error']) && $rs['error']['message'] == '[Số điện thoại đã tồn tại trong hệ thống!]'){
						
							$account = '';
							$data_check = array(
								'username' 	=> $phoneNumber,
								'retired'	=> 0
							);
							$check_user = $this->import->checkUserApi($data_check);
							$dataInsert = array(
								'user_id' 			=> $check_user['user_id'],
								'employee_id' 		=> $value[1],
								'phone_number' 		=> $phoneNumber,
								'company_id' 		=> $value[6],
								'full_name' 		=> $name,
								'gender' 			=> $gender,
								'birthdate' 		=> $birthdate,
								'Hospital' 			=> $hospital,
								'hospital_id'		=> $hospital_id,
								'examination_date' 	=> $exam_date,
								'examination_report'=> $examination_report
							);

							//Insert vào DB
							$insert_id = $this->import->addPHR($dataInsert);
							if($insert_id['status']){
								$count_success++;

								//Gửi Notification đến Device cho user
								$date = date('d-m-Y');
								$this->import->send_notification($phoneNumber, $hospital, $date, $insert_id['id']);

								// Chuẩn bị $dataSuccess để lưu vào history_import table
								$dataSuccess = array(
									'type'			=> 'success',
									'id'			=> $value[0],
									'employee_id'	=> $value[1],
									'full_name' 	=> $name,
									'phone_number' 	=> $phoneNumber,
									'status' 		=> $insert_id['msg']
								);

								array_push($arrayHistory, $dataSuccess);

								if($checkSMS == 'sms'){
									// Send SMS đến SĐT bệnh nhân
									$settingName = CONTENT_SMS_RESULT_OLD;
									$msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
									$this->sendSMS($phoneNumber,'Doctor4U',$msg);
								}

							}else{
								$count_fail++;
								// Chuẩn bị $dataFail để lưu vào history_import table
								$dataFail = array(
									'type'			=> 'error',
									'id'			=> $value[0],
									'employee_id'	=> $value[1],
									'full_name' 	=> $value[2],
									'phone_number' 	=> $phoneNumber,
									'status' 		=> $insert_id['msg']
								);
								array_push($arrayHistory, $dataFail);
							}
						}else{
								$count_fail++;
								// Chuẩn bị $dataFail để lưu vào history_import table
								$dataFail = array(
									'type'			=> 'error',
									'id'			=> $value[0],
									'employee_id'	=> $value[1],
									'full_name' 	=> $value[2],
									'phone_number' 	=> $phoneNumber,
									'status' 		=> 'Số điện thoại không hợp lệ'
								);
								array_push($arrayHistory, $dataFail);
						}	

					}else{
						$count_fail++; 
						$status = 'Thông tin cá nhân không đầy đủ';

						if($exam_date > date("Y-m-d")){
							$status = 'Ngày khám lớn hơn ngày hiện tại';
						}

						// Chuẩn bị $dataFail để lưu vào history_import table
						$dataFail = array(
							'type'			=> 'error',
							'id'			=> $value[0],
							'employee_id'	=> $value[1],
							'full_name' 	=> $value[2],
							'phone_number' 	=> $phoneNumber,
							'status' 		=> $status
						);

						array_push($arrayHistory, $dataFail);
					}

				}else if($i > 1 && ($value[2] =='' || $value[3] =='')){

					$count_fail++; 
					// Chuẩn bị $dataFail để lưu vào history_import table
					$dataFail = array(
						'type'			=> 'error',
						'id'			=> $value[0],
						'employee_id'	=> $value[1],
						'full_name' 	=> $value[2],
						'phone_number' 	=> $value[3],
						'status' 		=> 'Thông tin cá nhân không đầy đủ'
					);

					array_push($arrayHistory, $dataFail);
				}

			}
			// Lưu dữ liệu vào history_import
			$data_insertHistory = array(
				'file_name' => $inputFileName,
				'count_success' => $count_success,
				'count_false' => $count_fail,
				'date' => date("Y-m-d H:i:s"),
				'report' => json_encode($arrayHistory, true),
                'user_id' => session()->get('user')['id'],
                'type' => 1,
			);

			$this->history->addHistoryImport($data_insertHistory);
			return redirect()->to('trang-quan-tri/benh-an/d4u-khach-le');
		} else {
            return redirect()->to('trang-quan-tri/benh-an/d4u-khach-le');
		}
    }

    public function d4uListGroupImport(){
        $hospital 		= $this->request->getVar('hospital');
        $hospital_id 	= $this->request->getVar('hospitalId');
        $company        = $this->request->getVar('company');

		$apiURL 		= 'http://localhost:8080';
		$target 		= './public/csvfile/'.$company;
		$file 			= $this->request->getFile('uploadFile');
		$ext 			= $file->guessExtension();

		$checkSMS		= $this->request->getVar('checkSMS');

		$timeLabel 		= time().'_'.date('Ymd');

        if (in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $newName 	= $file->getName().'_'.$timeLabel.'.'.$ext;
            $inputFileName = './public/csvfile/'.$company.'/'.$newName;

            $file->move( $target , $newName);

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet = $spreadsheet->getActiveSheet();
            $data_array =  $spreadsheet->toArray();

			$dataPHR = array();
			$count_success = 0;			// Đếm số bản ghi import thành công
			$count_fail = 0;			// Đếm số bản ghi import thất bại
			$arrayHistory = array();	// Mảng lưu các bản ghi import thất bại

            foreach($data_array as $i => $value){
                if($i > 1 && $value[2] !='' && $value[3] !=''){
                    $phoneNumber =  preg_replace('/\s+/', '', $value[3]);
                    $phoneNumber    = $this->convertPhoneDigit($phoneNumber);
                    // print_r($value);die();

                    //Tên bệnh nhân
                    if(trim($value[2]) != ''){
                        $name = trim($value[2]);
                    }else{
                        $name = null;
                    }

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

                    //Giới tính
                    if(trim($value[5]) != ''){
                        $gender = trim($value[5]);
                    }else{
                        $gender = 'NON';
                    }

                    //Ngày khám
                    if(trim($value[207]) != ''){
                        $ed = \DateTime::createFromFormat('d/m/Y', trim($value[207]));
                        $exam_date = $ed->format('Y-m-d');
                    }else{
                        $exam_date = null;
                    }

                    // THÔNG TIN CÁ NHÂN
                        $dataPHR[$i]['ma_nv'] 		= $value[1];
                        $dataPHR[$i]['name'] 		= $name;
                        $dataPHR[$i]['SDT'] 		= $phoneNumber;
                        $dataPHR[$i]['birth'] 		= $birthdate;
                        $dataPHR[$i]['gender'] 		= $gender;
                        $dataPHR[$i]['don_vi'] 		= $value[6];
                        $dataPHR[$i]['dia_chi'] 	= $value[7];
                        $dataPHR[$i]['chan_doan'] 	= $value[8];

                    // KẾT LUẬN TƯ VẤN - ĐỀ NGHỊ
                        $dataPHR[$i]['ketLuan'] 	= $value[9];
                        $dataPHR[$i]['deNghi'] 		= $value[10];
                        $dataPHR[$i]['ketLuanXN'] 	= $value[11];
                        $dataPHR[$i]['don_thuoc'] 	= $value[12];

                    // KHÁM THỂ LỰC
                        $dataPHR[$i]['theLuc']['chieu_cao'] 			= $value[13];
                        $dataPHR[$i]['theLuc']['can_nang'] 				= $value[14];
                        $dataPHR[$i]['theLuc']['huyet_ap'] 				= $value[15];
                        $dataPHR[$i]['theLuc']['mach'] 					= $value[16];

                    // KHÁM LÂM SÀNG
                        $dataPHR[$i]['khamLamSan']['tuan_hoan'] 				= $value[17];
                        $dataPHR[$i]['khamLamSan']['ho_hap'] 					= $value[18];
                        $dataPHR[$i]['khamLamSan']['tieu_hoa'] 					= $value[19];
                        $dataPHR[$i]['khamLamSan']['than'] 						= $value[20];

                        $dataPHR[$i]['khamLamSan']['noi_tiet'] 					= $value[21];
                        $dataPHR[$i]['khamLamSan']['xuong_khop'] 				= $value[22];
                        $dataPHR[$i]['khamLamSan']['than_kinh'] 				= $value[23];
                        $dataPHR[$i]['khamLamSan']['tam_than'] 					= $value[24];

                        $dataPHR[$i]['khamLamSan']['eye'] 						= $value[25];
                        $dataPHR[$i]['khamLamSan']['tai_mui_hong'] 				= $value[26];
                        $dataPHR[$i]['khamLamSan']['rang_ham_mat'] 				= $value[27];
                        $dataPHR[$i]['khamLamSan']['da_lieu'] 					= $value[28];

                        $dataPHR[$i]['khamLamSan']['san_phu_khoa'] 				= $value[29];

                    // CHẨN ĐOÁN HÌNH ẢNH
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBung'] 						= $value[30];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBung_anh1'] 				= $value[31];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBung_anh2'] 				= $value[32];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBung_nhanxet'] 				= $value[33];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmBung_ketluan'] 				= $value[34];

                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap'] 				=$value[35];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap_anh1'] 			=$value[36];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap_anh2'] 			=$value[37];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap_nhanxet'] 		=$value[38];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmtuyenGiap_ketluan'] 		=$value[39];

                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu'] 					= $value[40];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu_anh1'] 				= $value[41];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu_anh2'] 				= $value[42];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu_nhanxet'] 			= $value[43];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTuyenVu_ketluan'] 			= $value[44];

                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTim'] 						= $value[45];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTim_anh1'] 					= $value[46];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTim_anh2'] 					= $value[47];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTim_nhanxet'] 				= $value[48];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTim_ketluan'] 				= $value[49];

                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau'] 				= $value[50];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau_anh1'] 		= $value[51];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau_anh2'] 		= $value[52];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau_nhanxet'] 		= $value[53];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmTimChuyenSau_ketluan'] 		= $value[54];

                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhop'] 						= $value[55];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhop_anh1'] 				= $value[56];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhop_anh2'] 				= $value[57];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhop_nhanxet'] 				= $value[58];
                    $dataPHR[$i]['chuanDoanHinhAnh']['sieuAmKhop_ketluan'] 				= $value[59];

                    $dataPHR[$i]['chuanDoanHinhAnh']['x_quang'] 						= $value[60];
                    $dataPHR[$i]['chuanDoanHinhAnh']['x_quang_anh1'] 					= $value[61];
                    $dataPHR[$i]['chuanDoanHinhAnh']['x_quang_anh2'] 					= $value[62];
                    $dataPHR[$i]['chuanDoanHinhAnh']['x_quang_nhanxet'] 				= $value[63];
                    $dataPHR[$i]['chuanDoanHinhAnh']['x_quang_ketluan'] 				= $value[64];

                    $dataPHR[$i]['chuanDoanHinhAnh']['noiSoiTai'] 						= $value[65];
                    $dataPHR[$i]['chuanDoanHinhAnh']['noiSoiMui'] 						= $value[66];
                    $dataPHR[$i]['chuanDoanHinhAnh']['noiSoiHong'] 						= $value[67];

                    // THĂM DÒ CHỨC NĂNG
                    $dataPHR[$i]['thamDoChucNang']['DienTamDo'] 			= $value[68];

                    // HÓA SINH
                    $dataPHR[$i]['hoaSinhMienDich']['crp'] 								= $value[69];
                    $dataPHR[$i]['hoaSinhMienDich']['glucose'] 							= $value[70];
                    $dataPHR[$i]['hoaSinhMienDich']['napDuong1H'] 						= $value[71];
                    $dataPHR[$i]['hoaSinhMienDich']['napDuong2H'] 						= $value[72];

                    $dataPHR[$i]['hoaSinhMienDich']['HbA1C'] 							= $value[73];
                    $dataPHR[$i]['hoaSinhMienDich']['Ure'] 								= $value[74];
                    $dataPHR[$i]['hoaSinhMienDich']['Creatinin'] 						= $value[75];
                    $dataPHR[$i]['hoaSinhMienDich']['Acid_uric'] 						= $value[76];

                    $dataPHR[$i]['hoaSinhMienDich']['Cholesterol_total'] 				= $value[77];
                    $dataPHR[$i]['hoaSinhMienDich']['Cholesterol_HDL'] 					= $value[78];
                    $dataPHR[$i]['hoaSinhMienDich']['Cholesterol_LDL'] 					= $value[79];
                    $dataPHR[$i]['hoaSinhMienDich']['Triglycerid'] 						= $value[80];

                    $dataPHR[$i]['hoaSinhMienDich']['AST_GOT'] 							= $value[81];
                    $dataPHR[$i]['hoaSinhMienDich']['ALT_GPT'] 							= $value[82];
                    $dataPHR[$i]['hoaSinhMienDich']['Albumin'] 							= $value[83];
                    $dataPHR[$i]['hoaSinhMienDich']['GGT'] 								= $value[84];

                    $dataPHR[$i]['hoaSinhMienDich']['ck_26_140_u_l'] 					= $value[85];
                    $dataPHR[$i]['hoaSinhMienDich']['amylase'] 							= $value[86];
                    $dataPHR[$i]['hoaSinhMienDich']['BilirubinTotal'] 					= $value[87];
                    $dataPHR[$i]['hoaSinhMienDich']['BilirubinTrucTiep'] 				= $value[88];

                    $dataPHR[$i]['hoaSinhMienDich']['BilirubinGianTiep'] 				= $value[89];
                    $dataPHR[$i]['hoaSinhMienDich']['FEHuyetThanh'] 					= $value[90];
                    $dataPHR[$i]['hoaSinhMienDich']['Ferritin'] 						= $value[91];
                    $dataPHR[$i]['hoaSinhMienDich']['calci_toan_phan'] 					= $value[92];

                    $dataPHR[$i]['hoaSinhMienDich']['ca2_11_129_mmol_l'] 				= $value[93];
                    $dataPHR[$i]['hoaSinhMienDich']['Na'] 								=$value[94];
                    $dataPHR[$i]['hoaSinhMienDich']['K'] 								= $value[95];
                    $dataPHR[$i]['hoaSinhMienDich']['Cl'] 								= $value[96];

                    $dataPHR[$i]['hoaSinhMienDich']['alp'] 								= $value[97];
                    $dataPHR[$i]['hoaSinhMienDich']['prealbumin_20_40_mg_dl'] 			= $value[98];
                    $dataPHR[$i]['hoaSinhMienDich']['ige_100_u_ml'] 					= $value[99];

                    // MIỄN DỊCH
                    $dataPHR[$i]['hoaSinhMienDich']['estradiol_pg_ml'] 					= $value[100];
                    $dataPHR[$i]['hoaSinhMienDich']['progesterone_nmol_l'] 			= $value[101];
                    $dataPHR[$i]['hoaSinhMienDich']['testosterone_24_87_ng_ml'] 		= $value[102];
                    $dataPHR[$i]['hoaSinhMienDich']['T3'] 								= $value[103];

                    $dataPHR[$i]['hoaSinhMienDich']['FT4'] 								= $value[104];
                    $dataPHR[$i]['hoaSinhMienDich']['TSH'] 								= $value[105];
                    $dataPHR[$i]['hoaSinhMienDich']['lh_2_12_iu_l'] 					= $value[106];
                    $dataPHR[$i]['hoaSinhMienDich']['cortisol_171_536_nmol_l'] 			= $value[107];

                    $dataPHR[$i]['hoaSinhMienDich']['fsh'] 							= $value[108];
                    $dataPHR[$i]['hoaSinhMienDich']['Cryfra21_1'] 						= $value[109];
                    $dataPHR[$i]['hoaSinhMienDich']['nse'] 								= $value[110];
                    $dataPHR[$i]['hoaSinhMienDich']['SCC'] 								= $value[111];

                    $dataPHR[$i]['hoaSinhMienDich']['AFP'] 								= $value[112];
                    $dataPHR[$i]['hoaSinhMienDich']['CEA'] 								= $value[113];
                    $dataPHR[$i]['hoaSinhMienDich']['PSA'] 								= $value[114];
                    $dataPHR[$i]['hoaSinhMienDich']['psa_tu_do'] 						= $value[115];

                    $dataPHR[$i]['hoaSinhMienDich']['CA72_4'] 							= $value[116];
                    $dataPHR[$i]['hoaSinhMienDich']['CA15_3'] 							= $value[117];
                    $dataPHR[$i]['hoaSinhMienDich']['CA125'] 							= $value[118];
                    $dataPHR[$i]['hoaSinhMienDich']['CA19_9'] 							= $value[119];

					$dataPHR[$i]['hoaSinhMienDich']['anti_ccp'] 						= $value[209];
					$dataPHR[$i]['hoaSinhMienDich']['rf_dinhluong'] 					= $value[212];
					$dataPHR[$i]['hoaSinhMienDich']['anti_tg'] 							= $value[213];

				// XÉT NGHIỆM NƯỚC TIỂU
					$dataPHR[$i]['nuocTieu']['URO']							= $value[120];
					$dataPHR[$i]['nuocTieu']['GLU']							= $value[121];
					$dataPHR[$i]['nuocTieu']['BIL']							= $value[122];
					$dataPHR[$i]['nuocTieu']['KET']							= $value[123];

                    $dataPHR[$i]['nuocTieu']['SG']							= $value[124];
                    $dataPHR[$i]['nuocTieu']['BLD']							= $value[125];
                    $dataPHR[$i]['nuocTieu']['pH']							= $value[126];
                    $dataPHR[$i]['nuocTieu']['PRO']							= $value[127];

                    $dataPHR[$i]['nuocTieu']['NT']							= $value[128];
                    $dataPHR[$i]['nuocTieu']['LEU']							= $value[129];
                    $dataPHR[$i]['nuocTieu']['VTC']							= $value[130];

                    // CÔNG THỨC MÁU
                    $dataPHR[$i]['congThucMau']['WBC'] 						= $value[131];
                    $dataPHR[$i]['congThucMau']['NEU'] 						= $value[132];
                    $dataPHR[$i]['congThucMau']['LYM'] 						= $value[133];
                    $dataPHR[$i]['congThucMau']['MONO'] 					= $value[134];

                    $dataPHR[$i]['congThucMau']['EOS'] 						= $value[135];
                    $dataPHR[$i]['congThucMau']['BASO'] 					= $value[136];
                    $dataPHR[$i]['congThucMau']['IG_TyLe'] 					= $value[137];
                    $dataPHR[$i]['congThucMau']['BachCauTrungTinh'] 		= $value[138];

                    $dataPHR[$i]['congThucMau']['BachCauLympho'] 			= $value[139];
                    $dataPHR[$i]['congThucMau']['BachCauMoMo'] 				= $value[140];
                    $dataPHR[$i]['congThucMau']['bachCauAcid'] 				= $value[141];
                    $dataPHR[$i]['congThucMau']['BachCauBase'] 				= $value[142];

                    $dataPHR[$i]['congThucMau']['IG_SoLuong'] 				= $value[143];
                    $dataPHR[$i]['congThucMau']['RBC'] 						= $value[144];
                    $dataPHR[$i]['congThucMau']['HGB'] 						= $value[145];
                    $dataPHR[$i]['congThucMau']['HCT'] 						= $value[146];

                    $dataPHR[$i]['congThucMau']['MCV'] 						= $value[147];
                    $dataPHR[$i]['congThucMau']['MCH'] 						= $value[148];
                    $dataPHR[$i]['congThucMau']['MCHC'] 					= $value[149];
                    $dataPHR[$i]['congThucMau']['RDW_SD'] 					= $value[150];

                    $dataPHR[$i]['congThucMau']['RDW_CV'] 					= $value[151];
                    $dataPHR[$i]['congThucMau']['PLT'] 						= $value[152];
                    $dataPHR[$i]['congThucMau']['MPV'] 						= $value[153];
                    $dataPHR[$i]['congThucMau']['PCT'] 						= $value[154];

					$dataPHR[$i]['congThucMau']['PDW'] 						= $value[155];
					$dataPHR[$i]['congThucMau']['P_LCR'] 					= $value[156];
					$dataPHR[$i]['congThucMau']['maulang_1h'] 				= $value[210];
					$dataPHR[$i]['congThucMau']['maulang_2h'] 				= $value[211];

                    // ĐÔNG MÁU
                    $dataPHR[$i]['dong_mau']['pt']							= $value[157];
                    $dataPHR[$i]['dong_mau']['pt70_140']					= $value[158];
                    $dataPHR[$i]['dong_mau']['inr'] 						= $value[159];
                    $dataPHR[$i]['dong_mau']['aptt_s'] 						= $value[160];
                    $dataPHR[$i]['dong_mau']['aptt_phantram'] 				= $value[161];
                    $dataPHR[$i]['dong_mau']['Fibrinogen'] 					= $value[162];
                    $dataPHR[$i]['dong_mau']['D_dimer'] 					= $value[163];
                    $dataPHR[$i]['dong_mau']['HuyetDo'] 					= $value[164];

                    // ĐIỆN DI HST
                    $dataPHR[$i]['HST']['HbA1']								= $value[165];
                    $dataPHR[$i]['HST']['HbA2']								= $value[166];
                    $dataPHR[$i]['HST']['HbF']								= $value[167];
                    $dataPHR[$i]['HST']['HbE']								= $value[168];

                    $dataPHR[$i]['HST']['hbs_zone_2']						= $value[169];
                    $dataPHR[$i]['HST']['HbH']								= $value[170];
                    $dataPHR[$i]['HST']['hbd_puhjab']						= $value[171];
                    $dataPHR[$i]['HST']['Hb_Bart']							= $value[172];

                    // SINH HỌC PHÂN TỬ - Xét nghiệm gen Thalassemia
                    $dataPHR[$i]['sinh_hoc_phan_tu']['sea']					= $value[173];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['a37']					= $value[174];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['a42']					= $value[175];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cs']					= $value[176];

                    $dataPHR[$i]['sinh_hoc_phan_tu']['qs']					= $value[177];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['shpt_28_a_g']			= $value[178];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['shpt_29_a_g']			= $value[179];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cap_aaac']			= $value[180];

                    $dataPHR[$i]['sinh_hoc_phan_tu']['int_tg']				= $value[181];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd14_15g']			= $value[182];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd17_at']				= $value[183];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd27_28c']			= $value[184];

                    $dataPHR[$i]['sinh_hoc_phan_tu']['be_ga']				= $value[185];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd_31_c']				= $value[186];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cs41_42_ttct']		= $value[187];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd43_gt']				= $value[188];

                    $dataPHR[$i]['sinh_hoc_phan_tu']['cd71_72a']			= $value[189];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i1_ft']			= $value[190];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i1_ga']			= $value[191];
                    $dataPHR[$i]['sinh_hoc_phan_tu']['ivs_i5_gc']			= $value[192];

                    $dataPHR[$i]['sinh_hoc_phan_tu']['ivs_ii_654_ct']		= $value[193];

                    // VI SINH
                    $dataPHR[$i]['viSinh']['HBsAgAuto'] 					= $value[194];
                    $dataPHR[$i]['viSinh']['HBsAbDinhLuong'] 				= $value[195];
                    $dataPHR[$i]['viSinh']['HBcAg'] 						= $value[196];
                    $dataPHR[$i]['viSinh']['HBcAb'] 						= $value[197];

                    $dataPHR[$i]['viSinh']['HBeAg'] 						= $value[198];
                    $dataPHR[$i]['viSinh']['HBeAb'] 						= $value[199];
                    $dataPHR[$i]['viSinh']['HAV'] 							= $value[200];
                    $dataPHR[$i]['viSinh']['HAV_IgM'] 						= $value[201];

                    $dataPHR[$i]['viSinh']['AntiHCV'] 						= $value[202];
                    $dataPHR[$i]['viSinh']['hbv_dna'] 						= $value[203];

                    // NHÓM MÁU
                    $dataPHR[$i]['NhomMau']['ABO'] 							= $value[204];
                    $dataPHR[$i]['NhomMau']['RH'] 							= $value[205];

                    // CHỈ SỐ KHÁC
                    $dataPHR[$i]['ChiSoKhac']['ChiSoKhac']					= $value[206];

				// SOI TƯƠI DỊCH ÂM ĐẠO
					$dataPHR[$i]['soiTuoiAmDao']['bachCau']					= $value[215];
					$dataPHR[$i]['soiTuoiAmDao']['cauKhuan']				= $value[216];
					$dataPHR[$i]['soiTuoiAmDao']['trucKhuan']				= $value[217];
					$dataPHR[$i]['soiTuoiAmDao']['songCauKhuan']			= $value[218];
					$dataPHR[$i]['soiTuoiAmDao']['bachCauHat']				= $value[219];
					$dataPHR[$i]['soiTuoiAmDao']['hongCau']					= $value[220];
					$dataPHR[$i]['soiTuoiAmDao']['nam']						= $value[221];
					$dataPHR[$i]['soiTuoiAmDao']['teBaoBieuMo']				= $value[222];
					$dataPHR[$i]['soiTuoiAmDao']['trichomonass']			= $value[223];
					$dataPHR[$i]['soiTuoiAmDao']['clueCell']				= $value[224];

				// Date & hospital
					$dataPHR[$i]['date'] 									= $value[207];
					$dataPHR[$i]['hospital'] 								= $value[208];

                    // Tạo chuỗi json chứa các chỉ số
                    $examination_report = json_encode($dataPHR[$i]);

                    if($exam_date !== null && $exam_date <= date("Y-m-d") && $name != null && $phoneNumber !=''){

                        $userData = array(
                            'username' 		=> $phoneNumber,
                            'phoneNumber' 	=> $phoneNumber,
                            'password' 		=> '123456',
                            'givenName' 	=> $name,
                            'birthdate' 	=> $birthdate,
                            'gender' 		=> $gender,
                            'role' 			=> 'PATIENT',
                            'providerRole'	=> 'c110f9bc-c65f-44a2-a028-2af7e8fff534',
                        );

                        $rs = $this->registerPatient($userData);

        //                        print_r($rs);
        //                        die();

                        if (isset($rs['user']['uuid']) && $rs['user']['uuid'] != '') {

                            $account = 'Tài khoản mới tạo.';

                            $data['username'] = $phoneNumber;
                            $data['retired'] = 0;
                            $user = $this->import->checkUserApi($data);

                            $dataInsert = array(
                                'user_id' 			=> $user['user_id'],
                                'employee_id' 		=> $value[1],
                                'phone_number' 		=> $phoneNumber,
                                'company_id' 		=> $company,
                                'full_name' 		=> $name,
                                'gender' 			=> $gender,
                                'birthdate' 		=> $birthdate,
                                'Hospital' 			=> $hospital,
                                'hospital_id'		=> $hospital_id,
                                'examination_date' 	=> $exam_date,
                                'examination_report'=> $examination_report
                            );

                            //Insert vào DB
                            $insert_id = $this->import->addPHRGroup($dataInsert);

                            if($insert_id['status'] === true){
                                //Gửi Notification đến Device cho user
                                $date = date('d-m-Y');
                                $this->import->send_notification($phoneNumber, $hospital, $date, $insert_id['id']);
                                $count_success++;

                                // Chuẩn bị $dataSuccess để lưu vào history_import table
                                $dataSuccess = array(
                                    'type'			=> 'success',
                                    'id'			=> $value[0],
                                    'employee_id'	=> $value[1],
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $insert_id['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataSuccess);

                                if($checkSMS == 'sms'){
                                    // Send SMS đến SĐT bệnh nhân
                                    $settingName = CONTENT_SMS_RESULT_NEW;
                                    $msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
                                    $this->sendSMS($phoneNumber,'Doctor4U',$msg);
                                }

                            }else{
                                $count_fail++;

                                // Chuẩn bị $dataFail để lưu vào history_import table
                                $dataFail = array(
                                    'type'			=> 'error',
                                    'id'			=> $value[0],
                                    'employee_id'	=> $value[1],
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $insert_id['msg']
                                );

                                array_push($arrayHistory, $dataFail);
                            }
                        }else if(isset($rs['error']) && $rs['error']['message'] == '[Số điện thoại đã tồn tại trong hệ thống!]'){

                            $account = '';
                            $data_check = array(
                                'username' 	=> $phoneNumber,
                                'retired'	=> 0
                            );
                            $check_user = $this->import->checkUserApi($data_check);
                            $dataInsert = array(
                                'user_id' 			=> $check_user['user_id'],
                                'employee_id' 		=> $value[1],
                                'phone_number' 		=> $phoneNumber,
                                'company_id' 		=> $value[6],
                                'full_name' 		=> $name,
                                'gender' 			=> $gender,
                                'birthdate' 		=> $birthdate,
                                'Hospital' 			=> $hospital,
                                'hospital_id'		=> $hospital_id,
                                'examination_date' 	=> $exam_date,
                                'examination_report'=> $examination_report
                            );

                            //Insert vào DB
                            $insert_id = $this->import->addPHRGroup($dataInsert);
                            if($insert_id['status']){
                                $count_success++;

                                //Gửi Notification đến Device cho user
                                $date = date('d-m-Y');
                                $this->import->send_notification($phoneNumber, $hospital, $date, $insert_id['id']);

                                // Chuẩn bị $dataSuccess để lưu vào history_import table
                                $dataSuccess = array(
                                    'type'			=> 'success',
                                    'id'			=> $value[0],
                                    'employee_id'	=> $value[1],
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $insert_id['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataSuccess);

                                if($checkSMS == 'sms'){
                                    // Send SMS đến SĐT bệnh nhân
                                    $settingName = CONTENT_SMS_RESULT_OLD;
                                    $msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
                                    $this->sendSMS($phoneNumber,'Doctor4U',$msg);
                                }

                            }else{
                                $count_fail++;
                                // Chuẩn bị $dataFail để lưu vào history_import table
                                $dataFail = array(
                                    'type'			=> 'error',
                                    'id'			=> $value[0],
                                    'employee_id'	=> $value[1],
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $insert_id['msg']
                                );
                                array_push($arrayHistory, $dataFail);
                            }
                        }else{
                            $count_fail++;
                            // Chuẩn bị $dataFail để lưu vào history_import table
                            $dataFail = array(
                                'type'			=> 'error',
                                'id'			=> $value[0],
                                'employee_id'	=> $value[1],
                                'full_name' 	=> $value[2],
                                'phone_number' 	=> $value[3],
                                'status' 		=> 'Số điện thoại không hợp lệ'
                            );
                            array_push($arrayHistory, $dataFail);
                        }

                    }else{
                        $count_fail++;
                        $status = 'Thông tin cá nhân không đầy đủ';

                        if($exam_date > date("Y-m-d")){
                            $status = 'Ngày khám lớn hơn ngày hiện tại';
                        }

                        // Chuẩn bị $dataFail để lưu vào history_import table
                        $dataFail = array(
                            'type'			=> 'error',
                            'id'			=> $value[0],
                            'employee_id'	=> $value[1],
                            'full_name' 	=> $name,
                            'phone_number' 	=> $phoneNumber,
                            'status' 		=> $status
                        );

                        array_push($arrayHistory, $dataFail);
                    }

                }else if($i > 1 && ($value[2] =='' || $value[3] =='')){

                    $count_fail++;
                    // Chuẩn bị $dataFail để lưu vào history_import table
                    $dataFail = array(
                        'type'			=> 'error',
                        'id'			=> $value[0],
                        'employee_id'	=> $value[1],
                        'full_name' 	=> $value[2],
                        'phone_number' 	=> $value[3],
                        'status' 		=> 'Thông tin cá nhân không đầy đủ'
                    );

                    array_push($arrayHistory, $dataFail);
                }

            }
            // Lưu dữ liệu vào history_import
            $data_insertHistory = array(
                'file_name' => $inputFileName,
                'count_success' => $count_success,
                'count_false' => $count_fail,
                'date' => date("Y-m-d H:i:s"),
                'report' => json_encode($arrayHistory, true),
                'user_id' => session()->get('user')['id'],
                'type' => 2,
            );

            $this->history->addHistoryImport($data_insertHistory);
            return redirect()->to('/trang-quan-tri/benh-an/d4u-khach-doan');
        } else {
            return redirect()->to('/trang-quan-tri/benh-an/d4u-khach-doan');
        }
    }

    public function d4uListCovidImport(){
      
        $hospital = 'Doctor4U';

        $apiURL 		= 'http://localhost:8080';
        $target 		= './public/csvfile';
        $file 			= $this->request->getFile('uploadFile');

        $checkSMS		= $this->request->getVar('checkSMS');

        $ext 			= $file->guessExtension();
        $timeLabel 		= time().'_'.date('Ymd');

        if (in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $newName 	= $file->getName().'_'.$timeLabel.'.'.$ext;
            $inputFileName = './public/csvfile/'.$newName;

            $file->move( $target , $newName);

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet = $spreadsheet->getActiveSheet();
            $data_array =  $spreadsheet->toArray();

            $dataPHR = array();
            $count_success = 0;			// Đếm số bản ghi import thành công
            $count_fail = 0;			// Đếm số bản ghi import thất bại
            $arrayHistory = array();	// Mảng lưu lịch sử các bản ghi import

            foreach($data_array as $i => $value){

                if($i > 1 && $value[1] !='' && $value[2] !='' && $value[6] !=''){

                    //Loại bỏ ký tự đặc biệt trong số điện thoại
                    $phoneNumber =  str_replace('.', '', $value[2]);
                    $phoneNumber =  str_replace(' ', '', $phoneNumber);


                    //Tên bệnh nhân
                    if(trim($value[1]) != ''){
                        $name = trim($value[1]);
                    }else{
                        $name = null;
                    }
                    //Ngày sinh
                    if(trim($value[3]) != ''){
                        if(strpos($value[3], '/')){
                            $d_arr = explode('/',trim($value[3]));
                            print_r($d_arr);
                            $birthdate = (int)trim($d_arr[2]).'-'.(int)trim($d_arr[1]).'-'.(int)trim($d_arr[0]);
                        }else{
                            $birthdate = $value[3].'-01-01';
                        }
                    }else{
                        $birthdate = null;
                    }

                    //Giới tính
                    if(trim($value[4]) != ''){
                        $gender = trim($value[4]);
                    }else{
                        $gender = 'NON';
                    }

                    //Ngày khám
                    if(trim($value[6]) != ''){
                        $d_arr1 = explode('/',trim($value[6]));
                        $exam_date = (int)trim($d_arr1[2]).'-'.(int)trim($d_arr1[1]).'-'.(int)trim($d_arr1[0]);
                    }else{
                        $exam_date = null;
                    }

                    //Kết quả
                    if(trim($value[7]) != ''){
                        $result = trim($value[7]);
                    }else{
                        $result = '';
                    }

                    //Loại Test
                    if(trim($value[8]) != ''){
                        $type = trim($value[8]);
                    }else{
                        $type = '';
                    }

                    // Kiểm tra dữ liệu: Ngày khám
                    if($exam_date != null && strtotime($exam_date) <= strtotime(date("Y-m-d")) && $name != null && $phoneNumber != ''){

                        $userData = array(
                            'username' 		=> $phoneNumber,
                            'phoneNumber' 	=> $phoneNumber,
                            'password' 		=> '123456',
                            'givenName' 	=> $name,
                            'birthdate' 	=> $birthdate,
                            'gender' 		=> $gender,
                            'role' 			=> 'PATIENT',
                            'providerRole'	=> 'c110f9bc-c65f-44a2-a028-2af7e8fff534',
                        );

                        $rs = $this->registerPatient($userData);
                        // print_r($rs);die();
                        if (isset($rs['user']['uuid']) && $rs['user']['uuid'] != '') {

                            $account = 'Tài khoản mới tạo.';
                            $data['username'] = $phoneNumber;
                            $data['retired'] = 0;
                            $user = $this->import->checkUserApi($data);

                            $dataInsert = array(
                                'patient_id' 		=> $user['user_id'],
                                'patient_name' 		=> $name,
                                'phone_number' 		=> $phoneNumber,
                                'date' 				=> $exam_date,
                                'result' 			=> $result,
                                'type' 				=> $type
                            );

                            //Insert vào DB
                            $addTestResult = $this->import->addCovidTest($dataInsert);

                            if($addTestResult['result']){
                                $insert_id = $addTestResult['id'];

                                if($insert_id != ''){
                                    //Gửi Notification đến Device cho user
                                    $date = date('d-m-Y');
                                    // $this->import->send_notification($phoneNumber, $hospital, $date, $insert_id);
                                }

                                $count_success++;

                                // Chuẩn bị $dataSuccess để lưu vào history_import table
                                $dataSuccess = array(
                                    'type'			=> 'success',
                                    'id'			=> $value[0],
                                    'employee_id'	=> '',
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $addTestResult['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataSuccess);

                                if($checkSMS == 'sms'){
                                    // Send SMS đến SĐT bệnh nhân
                                    $settingName = CONTENT_SMS_TEST_COVID_NEW;
                                    $msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
                                    $this->sendSMS($phoneNumber,'Doctor4U',$msg);
                                }

                            }else{
                                $count_fail++;

                                // Chuẩn bị $dataFail để lưu vào history_import table
                                $dataFail = array(
                                    'type'			=> 'error',
                                    'id'			=> $value[0],
                                    'employee_id'	=> '',
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $addTestResult['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataFail);
                            }
                        }else if(isset($rs['error']) && $rs['error']['message'] == '[Số điện thoại đã tồn tại trong hệ thống!]'){

                            $account = '';
                            $data_check = array(
                                'username' => $phoneNumber,
                                'retired' => 0
                            );
                            $check_user = $this->import->checkUserApi($data_check);

                            $dataInsert = array(
                                'patient_id' 		=> $check_user['user_id'],
                                'patient_name' 		=> $name,
                                'phone_number' 		=> $phoneNumber,
                                'date' 				=> $exam_date,
                                'result' 			=> $result,
                                'type' 				=> $type
                            );

                            //Insert vào DB
                            $addTestResult = $this->import->addCovidTest($dataInsert);

                            if($addTestResult['result']){

                                $count_success++;
                                $insert_id = $addTestResult['id'];
                                if($insert_id != ''){
                                    //Gửi Notification đến Device cho user
                                    $date = date('d-m-Y');
                                    // $this->import->send_notification($phoneNumber, $hospital, $date, $insert_id);
                                }

                                // Chuẩn bị $dataSuccess để lưu vào history_import table
                                $dataSuccess = array(
                                    'type'			=> 'success',
                                    'id'			=> $value[0],
                                    'employee_id'	=> '',
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $addTestResult['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataSuccess);

                                if($checkSMS == 'sms'){
                                    // Send SMS đến SĐT bệnh nhân
                                    $settingName = CONTENT_SMS_TEST_COVID_OLD;
                                    $msg = $this->import->getSMSContent($settingName, 'sms', ['hospital' => $hospital, 'date' => $date]);
                                    $this->sendSMS($phoneNumber,'Doctor4U',$msg);
                                }

                            }else{
                                $count_fail++;

                                // Chuẩn bị $dataFail để lưu vào history_import table
                                $dataFail = array(
                                    'type'			=> 'error',
                                    'id'			=> $value[0],
                                    'employee_id'	=> '',
                                    'full_name' 	=> $name,
                                    'phone_number' 	=> $phoneNumber,
                                    'status' 		=> $addTestResult['msg'].' - '.$account
                                );

                                array_push($arrayHistory, $dataFail);
                            }
                        }else{
                            // log_message('error', 'Lỗi!');
                            // print_r($rs);die();
                            $count_fail++;
                            // Chuẩn bị $dataFail để lưu vào history_import table
                            $dataFail = array(
                                'type'			=> 'error',
                                'id'			=> $value[0],
                                'employee_id'	=> '',
                                'full_name' 	=> $name,
                                'phone_number' 	=> $phoneNumber,
                                'status' 		=> 'Số điện thoại không hợp lệ'
                            );
                            array_push($arrayHistory, $dataFail);
                        }


                    }else{

                        $count_fail++;
                        $status = 'Thông tin không đầy đủ';

                        if($exam_date > date("Y-m-d")){
                            $status = 'Ngày khám lớn hơn ngày hiện tại';
                        }

                        // Chuẩn bị $dataFail để lưu vào history_import table
                        $dataFail = array(
                            'type'			=> 'error',
                            'id'			=> $value[0],
                            'employee_id'	=> '',
                            'full_name' 	=> $name,
                            'phone_number' 	=> $phoneNumber,
                            'status' 		=> $status
                        );

                        array_push($arrayHistory, $dataFail);
                    }

                }else if($i > 1 && ($value[1] =='' || $value[2] =='' || $value[6] =='')){

                    $count_fail++;
                    // Chuẩn bị $dataFail để lưu vào history_import table
                    $dataFail = array(
                        'type'			=> 'error',
                        'id'			=> $value[0],
                        'employee_id'	=> '',
                        'full_name' 	=> $value[1],
                        'phone_number' 	=> $value[2],
                        'status' 		=> 'Thông tin không đầy đủ'
                    );

                    array_push($arrayHistory, $dataFail);
                }

            }
            // Lưu dữ liệu vào history_import
            $data_insertHistory = array(
                'file_name' 	=> $inputFileName,
                'count_success' => $count_success,
                'count_false' 	=> $count_fail,
                'date' 			=> date("Y-m-d H:i:s"),
                'report' 		=> json_encode($arrayHistory, true),
                'user_id' => session()->get('user')['id'],
                'type' => 3,
            );

            $this->history->addHistoryImport($data_insertHistory);
            return redirect()->to('/trang-quan-tri/benh-an/d4u-test-covid');
        } else {
            return redirect()->to('/trang-quan-tri/benh-an/d4u-test-covid');
        }

    }

    public function createAccountProcess(){
        $target 		= './public/csvfile';
        $file 			= $this->request->getFile('uploadFile');
        $ext 			= $file->guessExtension();
        $timeLabel 		= time().'_'.date('Ymd');

        if (in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $newName 	= $file->getName().'_create_'.$timeLabel.'.'.$ext;
            $inputFileName = './public/csvfile/'.$newName;
            $file->move( $target , $newName);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet = $spreadsheet->getActiveSheet();
            $data_array =  $spreadsheet->toArray();

            $count_success = 0;			// Đếm số bản ghi import thành công
            $count_fail = 0;			// Đếm số bản ghi import thất bại
            $arrayHistory = array();

            foreach($data_array as $i => $value){
                if($i > 1 && $value[3] !='' && $value[4] !=''){
                    $ho_ten = trim($value[1]);
                    $phoneNumber =  preg_replace('/\s+/', '', $value[2]);
                    $phoneNumber = $this->convertPhoneDigit($phoneNumber);
                    //Ngày sinh
                    if(trim($value[3]) != ''){
                        if(strpos($value[3], '/')){
                            $bd = \DateTime::createFromFormat('d/m/Y', $value[3]);
                            $birthdate = $bd->format('Y-m-d');
                        }else{
                            $birthdate = $value[3].'-01-01';
                        }
                    }else{
                        $birthdate = null;
                    }

                    //Giới tính
                    if(trim($value[4]) != ''){
                        $gender = trim($value[4]);
                    }else{
                        $gender = 'NON';
                    }

                    $userData = array(
                        'username' 		=> $phoneNumber,
                        'phoneNumber' 	=> $phoneNumber,
                        'password' 		=> '123456',
                        'givenName' 	=> $ho_ten,
                        'birthdate' 	=> $birthdate,
                        'gender' 		=> $gender,
                        'role' 			=> 'PATIENT',
                        'providerRole'	=> 'c110f9bc-c65f-44a2-a028-2af7e8fff534',
                    );

                    $rs = $this->registerPatient($userData);

                    if(isset($rs['user']['uuid']) && $rs['user']['uuid'] != ''){
                        $count_success++;
                        $dataSuccess = array(
                            'type'			=> 'success',
                            'id'			=> $value[0],
                            'employee_id'	=> '',
                            'full_name' 	=> $ho_ten,
                            'phone_number' 	=> $phoneNumber,
                            'status' 		=> 'Tạo tài khoản thành công'
                        );

                        array_push($arrayHistory, $dataSuccess);
                    }else if(isset($rs['error']) && $rs['error']['message'] == '[Số điện thoại đã tồn tại trong hệ thống!]'){
                        $count_fail++;
                        $dataFail = array(
                            'type'			=> 'success',
                            'id'			=> $value[0],
                            'employee_id'	=> '',
                            'full_name' 	=> $ho_ten,
                            'phone_number' 	=> $phoneNumber,
                            'status' 		=> '<span class="text-warning">Tạo tài khoản thất bại - Số điện thoại đã tồn tại</span>'
                        );

                        array_push($arrayHistory, $dataFail);
                    }else{
                        $count_fail++;
                        $dataFail = array(
                            'type'			=> 'success',
                            'id'			=> $value[0],
                            'employee_id'	=> '',
                            'full_name' 	=> $ho_ten,
                            'phone_number' 	=> $phoneNumber,
                            'status' 		=> '<span class="text-danger">Tạo tài khoản thất bại - Số điện thoại không hợp lệ</span>'
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
                'user_id' => session()->get('user')['id'],
                'type' => 5,
            );

            $this->import->addHistoryImport($data_insertHistory);

            return redirect()->to('/history-import/tai-khoan-benh-nhan/thanh-cong');
        } else {
            return redirect()->to('/history-import/tai-khoan-benh-nhan/that-bai');
        }
    }
}
