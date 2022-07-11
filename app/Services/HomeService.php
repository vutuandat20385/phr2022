<?php

namespace App\Services;

require_once 'TechAPI/bootstrap.php';

use App\Models\UserModel;
use App\Models\User2Model;
use App\Models\AnnualCheckupModel;
use App\Models\GroupAnnualCheckupModel;
use App\Models\SettingsModel;
use App\Models\UserActiveModel;

use App\Services\SettingService;
use App\Services\ReportService;

class HomeService extends BaseService
{

    public function __construct()
    {
        parent::__construct();
        $this->annual               = new AnnualCheckupModel();
        $this->groupAnnual          = new GroupAnnualCheckupModel();
        $this->user                 = new UserModel();
        $this->user2                = new User2Model();
        $this->setting              = new SettingsModel();
        $this->settingService       = new SettingService();

        $this->active = new UserActiveModel();
        $this->report = new ReportService();

        $this->db = \Config\Database::connect();
    }

    public function get_hospital_user($phone){
        $data = $this->annual->where(['phone_number'=> $phone, 'status' => 1])->orderBy('examination_date', 'DESC')->findAll();
        // dd($data);
        if ($data) {
            $arr_h = array();
            foreach ($data as $val) {
                $arr = array(
                    'id_report' => $val['annual_checkup_id'],
                    'hospital' => $val['Hospital'],
                    'occ' => $val['Occupation'],
                    'date' => date("d", strtotime($val['examination_date'])) . ' tháng ' . date("m", strtotime($val['examination_date'])) . ', ' . date("Y", strtotime($val['examination_date'])),
                );
                array_push($arr_h, $arr);
            }
            return $arr_h;
        } else {
            return false;
        }
    }

    public function get_hospital_user_group($phone){
        $data = $this->groupAnnual->where(['phone_number'=> $phone, 'status' => 1])->orderBy('examination_date', 'DESC')->findAll();
        // dd($data);
        if ($data) {
            $arr_h = array();
            foreach ($data as $val) {
                $arr = array(
                    'id_report' => $val['annual_checkup_id'],
                    'hospital' => $val['Hospital'],
                    'occ' => $val['Occupation'],
                    'date' => date("d", strtotime($val['examination_date'])) . ' tháng ' . date("m", strtotime($val['examination_date'])) . ', ' . date("Y", strtotime($val['examination_date'])),
                );
                array_push($arr_h, $arr);
            }
            return $arr_h;
        } else {
            return false;
        }
    }

    public function get_phr_user($id){
        $data = $this->annual->where('annual_checkup_id', $id)->orderBy('annual_checkup_id', 'DESC')->first();

        if ($data) {
            $report = json_decode($data['examination_report'], true);
            if ($report['theLuc'] != null) {
                $report['theLuc']['check']              = $this->checkGroupEmpty($report['theLuc']);
            } else
                $report['theLuc']['check']              = 0;

            if ($report['khamLamSan'] != null) {
                $report['khamLamSan']['check']          = $this->checkGroupEmpty($report['khamLamSan']);
            } else
                $report['khamLamSan']['check']          = 0;

            if ($report['chuanDoanHinhAnh'] != null) {
                $report['chuanDoanHinhAnh']['check']    = $this->checkGroupEmpty($report['chuanDoanHinhAnh']);
            } else
                $report['chuanDoanHinhAnh']['check']    = 0;

            if ($report['thamDoChucNang'] != null) {
                $report['thamDoChucNang']['check']      = $this->checkGroupEmpty($report['thamDoChucNang']);
            } else
                $report['thamDoChucNang']['check']      = 0;

            if ($report['hoaSinhMienDich'] != null) {
                $report['hoaSinhMienDich']['check']     = $this->checkGroupEmpty($report['hoaSinhMienDich']);
            } else
                $report['hoaSinhMienDich']['check']     = 0;
            
            // if ($report['hoaSinh'] != null) {
            //     $report['hoaSinh']['check']             = $this->checkGroupEmpty($report['hoaSinh']);
            // } else
            //     $report['hoaSinh']['check']             = 0;

            if ($report['nuocTieu'] != null) {
                $report['nuocTieu']['check']            = $this->checkGroupEmpty($report['nuocTieu']);
            } else
                $report['nuocTieu']['check']            = 0;

            if ($report['congThucMau'] != null) {
                $report['congThucMau']['check']         = $this->checkGroupEmpty($report['congThucMau']);
            } else
                $report['congThucMau']['check']         = 0;

            $report['dong_mau']['HuyetDo']              = $report['HuyetDo'];
            if ($report['dong_mau'] != null) {
                $report['dong_mau']['check']            = $this->checkGroupEmpty($report['dong_mau']);
            } else
                $report['dong_mau']['check']            = 0;

            if ($report['HST'] != null) {
                $report['HST']['check']                 = $this->checkGroupEmpty($report['HST']);
            } else
                $report['HST']['check']                 = 0;

            if ($report['viSinh'] != null) {
                $report['viSinh']['check']              = $this->checkGroupEmpty($report['viSinh']);
            } else
                $report['viSinh']['check']              = 0;

            if ($report['NhomMau'] != null) {
                $report['NhomMau']['check']             = $this->checkGroupEmpty($report['NhomMau']);
            }

            if ($report['soiTuoiAmDao'] != null) {
                $report['soiTuoiAmDao']['check']             = $this->checkGroupEmpty($report['soiTuoiAmDao']);
            } else
                $report['soiTuoiAmDao']['check']             = 0;

            if ($report['ChiSoKhac'] != null) {
                $report['ChiSoKhac']['check']             = $this->checkGroupEmpty($report['ChiSoKhac']);
            } else
                $report['ChiSoKhac']['check']             = 0;


            if (strpos($report['birth'], '-')) {
                $report['birth'] = date_format(date_create($report['birth']), "d/m/Y");
            }

            if (!isset($report['ma_nv']) || $report['ma_nv'] == null) {
                $report['ma_nv'] = '';
            }
            // print_r($report);
            $data['examination_report'] = json_encode($report);
            return array(
                'hospitalId' => $data['hospital_id'],
                'data' => $data['examination_report']
            );
        } else {
            return false;
        }
    }

    public function get_phr_user_2($id){
        $data = $this->annual->where('annual_checkup_id', $id)->orderBy('annual_checkup_id', 'DESC')->first();

        $examination_report = json_decode($data['examination_report'], true);
        $gender = $examination_report['gender'];

       
        // theLuc
        foreach($examination_report['theLuc'] as $k => $value){
            $examination_report['theLuc'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // chuanDoanHinhAnh
        foreach($examination_report['chuanDoanHinhAnh'] as $k => $value){
            $examination_report['chuanDoanHinhAnh'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // hoaSinh
        foreach($examination_report['hoaSinhMienDich'] as $k => $value){
            $examination_report['hoaSinhMienDich'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // nuocTieu
        foreach($examination_report['nuocTieu'] as $k => $value){
            $examination_report['nuocTieu'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // congThucMau
        foreach($examination_report['congThucMau'] as $k => $value){
            $examination_report['congThucMau'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // dong_mau
        foreach($examination_report['dong_mau'] as $k => $value){
            $examination_report['dong_mau'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // HST
        foreach($examination_report['HST'] as $k => $value){
            $examination_report['HST'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        // sinh_hoc_phan_tu
        foreach($examination_report['sinh_hoc_phan_tu'] as $k => $value){
            $examination_report['sinh_hoc_phan_tu'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

         // viSinh
         foreach($examination_report['viSinh'] as $k => $value){
            $examination_report['viSinh'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

         // NhomMau
         foreach($examination_report['NhomMau'] as $k => $value){
            $examination_report['NhomMau'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
        }

        $data['examination_report'] = json_encode($examination_report);
        return array(
            'hospitalId' => $data['hospital_id'],
            'occ' => $data['Occupation'],
            'data' => $data['examination_report']
        );

    }

    public function get_phr_user_group($id){
        $data = $this->groupAnnual->where('annual_checkup_id', $id)->orderBy('annual_checkup_id', 'DESC')->first();

        if ($data) {
            $report = json_decode($data['examination_report'], true);
            if ($report['theLuc'] != null) {
                $report['theLuc']['check']              = $this->checkGroupEmpty($report['theLuc']);
            } else
                $report['theLuc']['check']              = 0;

            if ($report['khamLamSan'] != null) {
                $report['khamLamSan']['check']          = $this->checkGroupEmpty($report['khamLamSan']);
            } else
                $report['khamLamSan']['check']          = 0;

            if ($report['chuanDoanHinhAnh'] != null) {
                $report['chuanDoanHinhAnh']['check']    = $this->checkGroupEmpty($report['chuanDoanHinhAnh']);
            } else
                $report['chuanDoanHinhAnh']['check']    = 0;

            if ($report['thamDoChucNang'] != null) {
                $report['thamDoChucNang']['check']      = $this->checkGroupEmpty($report['thamDoChucNang']);
            } else
                $report['thamDoChucNang']['check']      = 0;

            if ($report['hoaSinhMienDich'] != null) {
                $report['hoaSinhMienDich']['check']     = $this->checkGroupEmpty($report['hoaSinhMienDich']);
            } else
                $report['hoaSinhMienDich']['check']     = 0;

            if ($report['nuocTieu'] != null) {
                $report['nuocTieu']['check']            = $this->checkGroupEmpty($report['nuocTieu']);
            } else
                $report['nuocTieu']['check']            = 0;

            if ($report['congThucMau'] != null) {
                $report['congThucMau']['check']         = $this->checkGroupEmpty($report['congThucMau']);
            } else
                $report['congThucMau']['check']         = 0;

            // if ($report['HuyetDo'] != null || $report['HuyetDo'] != '') {
            //     $report['HuyetDo']['check']             = $this->checkGroupEmpty($report['HuyetDo']);
            // } else
                // $report['HuyetDo']['check']             = 0;

            if ($report['dong_mau'] != null) {
                $report['dong_mau']['check']                 = $this->checkGroupEmpty($report['dong_mau']);
            } else
                $report['dong_mau']['check']                 = 0;

            if ($report['HST'] != null) {
                $report['HST']['check']                 = $this->checkGroupEmpty($report['HST']);
            } else
                $report['HST']['check']                 = 0;

            if ($report['sinh_hoc_phan_tu'] != null) {
                $report['sinh_hoc_phan_tu']['check']              = $this->checkGroupEmpty($report['sinh_hoc_phan_tu']);
            } else
                $report['sinh_hoc_phan_tu']['check']              = 0;

            if ($report['viSinh'] != null) {
                $report['viSinh']['check']              = $this->checkGroupEmpty($report['viSinh']);
            } else
                $report['viSinh']['check']              = 0;

            if ($report['NhomMau'] != null) {
                $report['NhomMau']['check']             = $this->checkGroupEmpty($report['NhomMau']);
            }

            if ($report['soiTuoiAmDao'] != null) {
                $report['soiTuoiAmDao']['check']             = $this->checkGroupEmpty($report['soiTuoiAmDao']);
            } else
                $report['soiTuoiAmDao']['check']             = 0;

            if ($report['ChiSoKhac'] != null) {
                $report['ChiSoKhac']['check']             = $this->checkGroupEmpty($report['ChiSoKhac']);
            } else
                $report['ChiSoKhac']['check']             = 0;

            if (strpos($report['birth'], '-')) {
                $report['birth'] = date_format(date_create($report['birth']), "d/m/Y");
            }

            if (!isset($report['ma_nv']) || $report['ma_nv'] == null) {
                $report['ma_nv'] = '';
            }
            // print_r($report);
            $data['examination_report'] = json_encode($report);

            return array(
                'hospitalId' => $data['hospital_id'],
                'occ' => $data['Occupation'],
                'data' => $data['examination_report']
            );
        } else {
            return false;
        }
    }

    public function get_phr_user_group_2($id){
        $data = $this->groupAnnual->where('annual_checkup_id', $id)->orderBy('annual_checkup_id', 'DESC')->first();
        return array(
            'hospitalId' => $data['hospital_id'],
            'occ' => $data['Occupation'],
            'data' => $data['examination_report']
        );
    }

    public function checkUser2($data){
        $check = $this->user2->where(['username' => $data['username']])->first();
        if ($check) {
            if ($check['password'] == hash('sha512', $data['password'] . $check['salt'])) {
                return array(
                    'status'    => 1,
                    'msg'       => '',
                    'user'      => $check
                );
            } else {
                return array(
                    'status'    => 0,
                    'msg'       => 'Mật khẩu không chính xác, xin vui lòng thử lại',
                    'user'      => null
                );
            }
        } else {
            return array(
                'status'    => 0,
                'msg'       => 'Số điện thoại chưa đăng ký trong hệ thống',
                'user'      => null
            );
        }
    }

    public function checkGroupEmpty($data){
        $check = 0;
        $key_array = array_keys($data);
        if ($key_array != null) {
            foreach ($key_array as $key) {
                // log_message('error', $key);
                if ($data[$key] != '' || $data[$key] != null) {
                    $check = 1;
                }
            }
        }
        return $check;
    }


    public function getNewRegister($num_day){
        $arr_day = array();
        $x = 0;
        for ($i = $num_day; $i >= 0; $i--) {
            $day        = date('d-m', strtotime('-' . $i . ' days'));
            $dayBegin   = date('Y-m-d 00:00:01', strtotime('-' . $i . ' days'));
            $dayEnd     = date('Y-m-d 23:59:59', strtotime('-' . $i . ' days'));
            $arr_day[$x]['day'] = array(
                'day'       => $day,
                'dayBegin'  => $dayBegin,
                'dayEnd'    => $dayEnd
            );
            $x++;
        }
        $y = 0;
        foreach ($arr_day as $k => $day) {
            $b = $day['day']['dayBegin'];
            $e = $day['day']['dayEnd'];
            $arr_day[$y]['countUsers'] = $this->user->where(['date_created >=' => $b, 'date_created <=' => $e])->countAllResults();
            $y++;
        }
        // dd($arr_day);
        return $arr_day;
    }

    public function countPatientAccount(){
        $query = "SELECT count(person.person_id) as count
                FROM person
                INNER JOIN users ON users.person_id = person.person_id
                LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                WHERE users.`status` = 'ACTIVE' AND person_attribute.`value` is not null and users.retired=0
                ORDER BY users.person_id DESC";
                log_message('error', $query);
        $list = $this->db->query($query)->getResultArray();
        $result = $list[0]['count'];

        return $result;
    }

    public function countActiveAccountInDay(){
        $lastActive = $this->active->orderBy('date_created', 'DESC')->first();
        $lastDate_begin = date('Y-m-d 00:00:00', strtotime($lastActive['date_created']));
        $lastDate_end = date('Y-m-d 23:59:59', strtotime($lastActive['date_created']));
        $query = "select pa.value, pn.given_name, ual.date_created, ual.phone_number from user_active_log as ual
                    left join users as u ON u.username = ual.user_name
                    left join person as p ON p.person_id = u.person_id
                    left join person_name as pn ON p.person_id = pn.person_id
                    left join person_attribute as pa ON p.person_id = pa.person_id
                    where ual.date_created >= '" . $lastDate_begin . "' and ual.date_created <= '" . $lastDate_end . "' and u.retired=0
                    group by ual.phone_number";
        $activeList = $this->db->query($query);
        $result['list'] = $activeList->getResultArray();
        $result['date'] = date('d-m-Y', strtotime($lastActive['date_created']));

        return $result;
    }

    public function getActiveAccountInDay($start, $limit){
        $lastActive = $this->active->orderBy('date_created', 'DESC')->first();
        $lastDate_begin = date('Y-m-d 00:00:00', strtotime($lastActive['date_created']));
        $lastDate_end = date('Y-m-d 23:59:59', strtotime($lastActive['date_created']));
        $query = "select pa.value, pn.given_name, ual.date_created, ual.phone_number, p.birthdate, p.gender from user_active_log as ual
                    left join users as u ON u.username = ual.user_name
                    left join person as p ON p.person_id = u.person_id
                    left join person_name as pn ON p.person_id = pn.person_id
                    left join person_attribute as pa ON p.person_id = pa.person_id
                    where ual.date_created >= '" . $lastDate_begin . "' and ual.date_created <= '" . $lastDate_end . "' and u.retired=0
                    group by ual.phone_number LIMIT ".$start.", ".$limit;
        $activeList = $this->db->query($query);
        $result = $activeList->getResultArray();

        return $result;
    }

    public function countNewRegisterInDay(){
        $todayBegin = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $query = "select pa.value, pn.given_name, p.birthdate, p.gender, u.date_created from users as u
                    left join person as p ON p.person_id = u.person_id
                    left join person_name as pn ON p.person_id = pn.person_id
                    left join person_attribute as pa ON p.person_id = pa.person_id
                    where u.date_created >= '" . $todayBegin . "' and u.date_created <= '" . $todayEnd . "' and u.retired=0";
        $todayRegister = $this->db->query($query);
        $result = $todayRegister->getResultArray();

        return $result;
    }

    public function getNewRegisterInDay($start, $limit){
        $todayBegin = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $query = "select pa.value, pn.given_name, p.birthdate, p.gender, u.date_created from users as u
                    left join person as p ON p.person_id = u.person_id
                    left join person_name as pn ON p.person_id = pn.person_id
                    left join person_attribute as pa ON p.person_id = pa.person_id
                    where u.date_created >= '" . $todayBegin . "' and u.date_created <= '" . $todayEnd . "' and u.retired=0 LIMIT ".$start.", ".$limit;
        $todayRegister = $this->db->query($query);
        $result = $todayRegister->getResultArray();

        return $result;
    }

    public function countBirthdateInday(){
        $date = date('Y-m-d');
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));

        $query_person = "SELECT p.person_id, pa.value, p.birthdate, pn.given_name FROM person p
                            LEFT JOIN person_attribute pa ON pa.person_id = p.person_id 
                            LEFT JOIN person_name pn ON pn.person_id = p.person_id
                            WHERE MONTH(p.birthdate)='" . $month . "' AND DAY(p.birthdate)='" . $day . "'";
        $person = $this->db->query($query_person);
        $result = $person->getResultArray();

        return $result;
    }

    public function getBirthdateInday($start, $limit){
        $date = date('Y-m-d');
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));

        $query_person = "SELECT p.person_id, pa.value, p.birthdate, pn.given_name FROM person p
                            LEFT JOIN person_attribute pa ON pa.person_id = p.person_id 
                            LEFT JOIN person_name pn ON pn.person_id = p.person_id
                            WHERE MONTH(p.birthdate)='" . $month . "' AND DAY(p.birthdate)='" . $day . "' LIMIT ".$start.", ".$limit;
        $person = $this->db->query($query_person);
        $result = $person->getResultArray();

        return $result;
    }

    public function countAppointmentInMonth(){
        return $this->report->getAllReportsInMonth();
    }

    public function countPaymentInMonth(){
        return $this->report->getAllPaymentInMonth();
    }

    public function countAllVNPAY(){
        $query = "SELECT person_attribute.`value`, transaction_vnpay.vnp_bank_code, transaction_vnpay.vnp_card_type, transaction_vnpay.vnp_amount, transaction_vnpay.vnp_pay_date, `transaction`.status FROM transaction_vnpay 
                LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_vnpay.transaction_id
                LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
                LEFT JOIN users ON users.user_id = wallet.user_id
                LEFT JOIN person ON person.person_id = users.person_id
                LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
                WHERE transaction_vnpay.voided = 0 AND `transaction`.status = 'PAID'";

        return $this->db->query($query)->getResultArray();
    }

    public function countAllMOMO(){
        $query = "SELECT person_attribute.`value`, transaction_momo.amount, `transaction`.date_created FROM transaction_momo 
        LEFT JOIN `transaction` ON `transaction`.transaction_id = transaction_momo.transaction_id
        LEFT JOIN wallet ON wallet.wallet_id = `transaction`.wallet_id
        LEFT JOIN users ON users.user_id = wallet.user_id
        LEFT JOIN person ON person.person_id = users.person_id
        LEFT JOIN person_attribute ON person_attribute.person_id = person.person_id
        WHERE transaction_momo.voided = 0  AND `transaction`.status = 'PAID'";

        return $this->db->query($query)->getResultArray();
    }

    public function countAllTRANSFER(){
        $sql = "SELECT * FROM auto_transfer_history WHERE status = 1";

        return $this->db->query($sql)->getResultArray();
    }

}
