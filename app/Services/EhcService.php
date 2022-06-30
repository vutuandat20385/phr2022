<?php
namespace App\Services;

use App\Models\StandardValueModel;
use App\Models\StandardValueModel2;
use App\Models\AutoUpdateHistoryModel;
use App\Models\AutoUpdateCovidHistoryModel;
use App\Models\AnnualCheckupModel;
use App\Models\EhcTempRecordModel;

class EhcService extends BaseService{

    public function __construct(){
        $this->standard     = new StandardValueModel();
        $this->standard2     = new StandardValueModel2();
        $this->history      = new AutoUpdateHistoryModel();
        $this->covidhistory = new AutoUpdateCovidHistoryModel();
        $this->annual       = new AnnualCheckupModel();
        $this->temp       = new EhcTempRecordModel();

        $this->db = \Config\Database::connect();
    }

    public function updateStandardValue($data){
        
        if(!is_null($data['fullName'])){
            $value['fullName'] = $data['fullName'];
        }

        if(!is_null($data['codeName'])){
            $value['codeName'] = $data['codeName'];
        }

        if(!is_null($data['unit'])){
            $value['unit'] = $data['unit'];
        }

        if(!is_null($data['gioitinh'])){
            $value['gioitinh'] = $data['gioitinh'];
        }

        if(!is_null($data['textMale'])){
            $value['textMale'] = $data['textMale'];
        }

        if(!is_null($data['minMale'])){
            $value['minMale'] = $data['minMale'];
        }

        if(!is_null($data['maxMale'])){
            $value['maxMale'] = $data['maxMale'];
        }

        if(!is_null($data['textFemale'])){
            $value['textFemale'] = $data['textFemale'];
        }

        if(!is_null($data['minFemale'])){
            $value['minFemale'] = $data['minFemale'];
        }

        if(!is_null($data['maxFemale'])){
            $value['maxFemale'] = $data['maxFemale'];
        }

        //Check mã DV
        $checkCodeName = $this->standard2->where(['codeName' => $data['codeName']])->first();

        if(!$checkCodeName){
            $this->standard2->insert($value);
            $id = $this->standard2->getInsertID();
        }else{
            $this->standard2->set($value)->where(['codeName' => $data['codeName']])->update();
            $lastUpdaterecord = $this->standard2->where(['codeName' => $data['codeName']])->first();
            $id = $lastUpdaterecord['id'];
        }

        return $id;
    }

    public function addHistoryUpdate($data){
        // Check exist
        $check = $this->history->where(['id_treatment' => $data['id_treatment']])->first();
        if($check == null){
            return $this->history->save($data);
        }else
            return $this->history->set($data)->where(['id_treatment' => $data['id_treatment']])->update();
        
    }

    public function addCovidHistoryUpdate($data){
        // Check exist
        $check = $this->covidhistory->where(['id_treatment' => $data['id_treatment']])->first();
        if($check == null){
            return $this->covidhistory->save($data);
        }else
            return $this->covidhistory->set($data)->where(['id_treatment' => $data['id_treatment']])->update();
        
    }

    public function getAllEHChistory(){
        return $this->history->orderBy('exam_date', 'DESC')->findAll();
    }

    public function getEHChistoryKhachLe(){
        return $this->history->where(['type' => 'khach-le'])->orderBy('exam_date', 'DESC')->findAll();
    }

    public function getEHChistoryKhachDoan(){
        return $this->history->where(['type' => 'khach-doan'])->orderBy('exam_date', 'DESC')->findAll();
    }

    public function getEHChistoryTestCovid(){
        return $this->covidhistory->orderBy('exam_date', 'DESC')->findAll();
    }

    public function updateTreatment($dataUpdate, $annual_checkup_id){
        return $this->annual->set($dataUpdate)->where(['annual_checkup_id' => $annual_checkup_id])->update();
    }

    public function checkIdLuotKham($idluotkham){
        return $this->history->where(['id_treatment' => $idluotkham])->first();
    }

    public function checkTempTreatment($idluotkham){
        return $this->temp->where(['treatment_id' => $idluotkham])->first();
    }

    public function getAnnualCheckupRecord($annual_checkup_id){
        $data = $this->annual->where(['annual_checkup_id' => $annual_checkup_id])->first();

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

        // soiTuoiAmDao
        foreach($examination_report['soiTuoiAmDao'] as $k => $value){
            $examination_report['soiTuoiAmDao'][$k]['chisochuan'] = $this->settingService->getChiSoChuan($value['maDV'], $gender);
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

    public function getEHChistoryByType($type, $date_from, $date_to, $info){
        if ($type == 'test-covid') {
            $sql = $this->covidhistory;
        } else {
            $sql = $this->history->where(['type' => $type]);
        }

        if($info !== ''){
        //            $sql .= " AND (aa.appointment_code like '%$info%' OR patient.given_name like '%$info%')";
            $sql = $sql->like(['phoneNumber' => $info])->orLike(['fullName' => $info]);
        }

        if($date_from != ''){
            $from = \DateTime::createFromFormat('d/m/Y', $date_from);
            $dateFrom = $from->format('Y-m-d 00:00:01');
        //            $sql .= " AND ats.start_date >= '$dateFrom'";
            $sql = $sql->where(['exam_date >=' => $dateFrom]);
        }

        if($date_to != ''){
            $to = \DateTime::createFromFormat('d/m/Y', $date_to);
            $dateTo = $to->format('Y-m-d 23:59:59');
        //            $sql .= " AND ats.end_date <= '$dateTo'";
            $sql = $sql->where(['exam_date <=' => $dateTo]);
        }
        return $sql->orderBy('exam_date', 'DESC')->findAll();
    }

    public function getVisitInfo($type, $hostpitalId, $visitId){
        if($type == 'khach-le'){
            return array(
                'hospital_id' => $hostpitalId,
                'info' => $this->annual->where(['annual_checkup_id'=> $visitId, 'hospital_id' => $hostpitalId])->first()
            );
        }else if($type == 'khach-doan'){
            return array(
                'hospital_id' => $hostpitalId,
                'info' => $this->groupAnnual->where(['annual_checkup_id'=> $visitId, 'hospital_id' => $hostpitalId])->first()
            );
        }

    }

    public function addEhcTempRecord($data){
        return $this->temp->save($data);
    }


}