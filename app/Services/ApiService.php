<?php
namespace App\Services;

use App\Models\AnnualCheckupModel;
use App\Models\GroupAnnualCheckupModel;

use App\Services\SettingService;

class ApiService extends BaseService{
    public function __construct(){
        $this->annual = new AnnualCheckupModel();
        $this->groupAnnual = new GroupAnnualCheckupModel();

        $this->settingService = new SettingService();
    }

    public function get_hospital_user($phone) {
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

    public function get_phr_user($id) {
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

            if ($report['dong_mau'] != null) {
                $report['dong_mau']['check']            = $this->checkGroupEmpty($report['dong_mau']);
            } else
                $report['dong_mau']['check']            = 0;

            if ($report['HST'] != null) {
                $report['HST']['check']                 = $this->checkGroupEmpty($report['HST']);
            } else
                $report['HST']['check']                 = 0;

            if ($report['sinh_hoc_phan_tu'] != null) {
                $report['sinh_hoc_phan_tu']['check']    = $this->checkGroupEmpty($report['sinh_hoc_phan_tu']);
            } else
                $report['sinh_hoc_phan_tu']['check']    = 0;

            if ($report['viSinh'] != null) {
                $report['viSinh']['check']              = $this->checkGroupEmpty($report['viSinh']);
            } else
                $report['viSinh']['check']              = 0;

            if ($report['NhomMau'] != null) {
                $report['NhomMau']['check']             = $this->checkGroupEmpty($report['NhomMau']);
            }

            if ($report['soiTuoiAmDao'] != null) {
                $report['soiTuoiAmDao']['check']        = $this->checkGroupEmpty($report['soiTuoiAmDao']);
            } else
                $report['soiTuoiAmDao']['check']        = 0;

            if ($report['ChiSoKhac'] != null) {
                $report['ChiSoKhac']['check']           = $this->checkGroupEmpty($report['ChiSoKhac']);
            } else
                $report['ChiSoKhac']['check']           = 0;

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

}