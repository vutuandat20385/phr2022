<?php
namespace App\Services;

use App\Models\ReferenceResultRangeModel;
use App\Models\ServiceDefineModel;
use App\Models\SettingsModel;
use App\Models\StandardValueModel;
use App\Models\StandardValueModel2;
use App\Models\MarketingNotificationModel;

class SettingService extends BaseService{

    public function __construct(){
        $this->resultRange          = new ReferenceResultRangeModel();
        $this->setting              = new SettingsModel();
        $this->standardValue        = new StandardValueModel();
        $this->standardValue2       = new StandardValueModel2();
        $this->marketing            = new MarketingNotificationModel();
        $this->serviceDefine        = new ServiceDefineModel();

        $this->db = \Config\Database::connect();
    }

    function getStandardIndex($gender){
        $genderStandardValue = $this->standardValue->where(['gender' => $gender, 'parentId >' => '0'])->findAll();
        $result = array();
        foreach($genderStandardValue as $k => $value){
            $parentId = $value['parentId'];
            $parentInfo = $this->standardValue->where(['id' => $parentId])->first();
            $genderStandardValue[$k]['parent'] = $parentInfo['shortName'];

            $result[$parentInfo['shortName']][$value['shortName']]['min'] = trim($value['min']);
            $result[$parentInfo['shortName']][$value['shortName']]['max'] = trim($value['max']);
            $result[$parentInfo['shortName']][$value['shortName']]['text'] = $value['text'];
            $result[$parentInfo['shortName']][$value['shortName']]['unit'] = $value['unit'];
        }
        return $result;
    }

    function getChiSoChuan($maDV, $gender){
        $record = $this->standardValue2->where(['status' => 1, 'codeName' => $maDV])->first();

        if($gender == 'nam' || $gender == 'Nam'){
            $result = array(
                'min'   => $record['minMale'],
                'max'   => $record['maxMale'],
                'text'  => $record['textMale'],
                'unit'  => $record['unit']
            );
        }else{
            $result = array(
                'min'   => $record['minFemale'],
                'max'   => $record['maxFemale'],
                'text'  => $record['textFemale'],
                'unit'  => $record['unit']
            );
        }

        return $result;
        
    }

    function getStandardIndex_2(){
        // $getAllIndex = $this->standardValue->where(['parentId >' => '0'])->findAll();
        $getAllIndex = $this->standardValue2->findAll();
        // $result = array();
        // foreach($getAllIndex as $k => $value){
        //     if(!is_null($value['parentId'])){
        //         $parentId = $value['parentId'];
        //     }else{
        //         $parentId = 0;
        //     }
        // }
        return $getAllIndex;
    }

    function getSettingValue($data){
        return $this->setting->where($data)->first();
    }

    function saveSettingValue($data){
        $check = $this->setting->where(['settingType' => $data['settingType'], 'settingName' =>$data['settingName']])->first();
        if($check){
            return $this->setting->set(['settingValue' => $data['settingValue']])->where(['settingType' => $data['settingType'], 'settingName' =>$data['settingName']])->update();
        }else
            return $this->setting->save($data);
    }

    function addNewValue($value){
        // Ki???m tra shortName xem ???? t???n t???i ch??a
        $shortName  = $value['standard_ShortName'];
        $group      = $value['standard_Group'];
        $code       = $value['standard_CodeName'];
        $checkShortName = $this->standardValue->where(['shortName' => $shortName, 'parentId' => $group, 'codeName' => $code])->first();
        if($checkShortName != null){
            return redirect()->to('/settings/standard-index')->with('msg','T??n vi???t t???t ???? t???n t???i trong nh??m ch??? s???!');
        }else{
            $data = array(
                'fullName'  => $value['standard_DisplayName'],
                'shortName' => $value['standard_ShortName'],
                'codeName'  => $value['standard_ShortName'],

                'parentId'      => $value['standard_Group'],
                'unit'          => $value['standard_DisplayUnit'],

                'maxMale'           => $value['standard_MaxValue_Male'],
                'minMale'           => $value['standard_MinValue_Male'],
                'maxFemale'         => $value['standard_MaxValue_Female'],
                'minFemale'         => $value['standard_MinValue_Female'],
                'textMale'          => $value['standard_DisplayValue_Male'],
                'textMale'          => $value['standard_DisplayValue_Female'],
            );

            // dd($data);
            $this->standardValue->save($data);
            return redirect()->to('/settings/standard-index')->with('msg','Th??m ch??? s??? m???i th??nh c??ng!');
        }
    }

    function getGroupStandardIndex(){
        return $this->standardValue->where(['parentId' => 0])->findAll();
    }

    function getStandardIndexByGroup($group){
        $allIndex = $this->standardValue
                ->select('fullName, shortName, parentId')
                ->where(['parentId' => $group])
                ->groupBy('shortName')
                ->findAll();

        foreach($allIndex as $k => $value){
            $parentId = $value['parentId'];
            $parentInfo = $this->standardValue->where(['id' => $parentId])->first();
            $allIndex[$k]['parent'] = $parentInfo['shortName'];

            $v    = $this->standardValue->where(['shortName' => $value['shortName']])->findAll();
            foreach($v as $v1){
                if($v1['gender']==='Nam'){
                    if(isset($v1['min'])){  $allIndex[$k]['min_nam']    = $v1['min']; }
                    if(isset($v1['max'])){  $allIndex[$k]['max_nam']    = $v1['max']; }
                    if(isset($v1['text'])){ $allIndex[$k]['text_nam']   = $v1['text']; }
                    if(isset($v1['unit'])){ $allIndex[$k]['unit_nam']   = $v1['unit']; }
                }else{
                    if(isset($v1['min'])){  $allIndex[$k]['min_nu']     = $v1['min']; }
                    if(isset($v1['max'])){  $allIndex[$k]['max_nu']     = $v1['max']; }
                    if(isset($v1['text'])){ $allIndex[$k]['text_nu']    = $v1['text']; }
                    if(isset($v1['unit'])){ $allIndex[$k]['unit_nu']    = $v1['unit']; }
                }                 
            }
        }

        return $allIndex;
    }

    public function getAllMarketingNotification($data){
        $query = "SELECT * FROM manage_marketing_notifications WHERE status >= 0";
        if($data['info'] != ''){
            $query .= " AND content like '%".$data['info']."%'";
        }

        if($data['start'] != ''){
            $newDateStart = \DateTime::createFromFormat('d/m/Y H:i', $data['start']);
            $query .= " AND public_time >= '".$newDateStart->format('Y-m-d H:i')."'";
            log_message('error', $newDateStart->format('Y-m-d H:i'));
        }

        if($data['end'] != ''){
            $newDateEnd = \DateTime::createFromFormat('d/m/Y H:i', $data['end']);
            $query .= " AND public_time <= '".$newDateEnd->format('Y-m-d H:i')."'";
            log_message('error', $newDateEnd->format('Y-m-d H:i'));
        }

        $query .= " ORDER BY status DESC, public_time DESC";
        log_message('error', $query);
        return $this->db->query($query)->getResultArray(); 
    }

    public function editMarketingNotification($id, $data){
        return $this->marketing->where(['id' => $id])->set($data)->update();
    }

    public function addMarketingNotification($data){
        return $this->marketing->save($data);
    }

    public function saveChiSoCDHA($data){
        return $this->standardValue->save($data);
    }

    //D???CH V??? KH??M
    function getServiceDefineParent() {
        return $this->serviceDefine->where(['parentId' => 0])->findAll();
    }

    function getAllServiceDefine() {
        $parent = $this->serviceDefine->where(['parentId' => 0])->find();

        foreach ($parent as $k => $p){
            $parent[$k]['child'] = $this->serviceDefine->where(['parentId' => $p['id']])->find();
        }

        return $parent;
    }

//    TH??? L???C: 1
//    CHU???N ??O??N H??NH ???NH: 2
//    H??A SINH MI???N D???CH: 3
//    N?????C TI???U: 4
//    C??NG TH???C M??U: 5
//    ????NG M??U: 6
//    HST: 7
//    SINH H???C PH??N T???: 8
//    VI SINH: 9
//    NH??M M??U: 10
//    CH??? S??? KH??C: 11
    function getServiceDefineChild($parent){
        return $this->serviceDefine->where(['parentId' => $parent, 'status' => 1])->find();
    }

    function newServiceDefine($data){
        $check = $this->serviceDefine->where(['name' => $data['name']])->orWhere(['codeName' => $data['codeName']])->first();
        if (!$check){
            return $this->serviceDefine->save($data);
        }
    }

    function updateServiceDefine($id,$data){
        return $this->serviceDefine->where(['id' => $id])->set($data)->update();
    }

    function deleteServiceDefine($id){
        return $this->serviceDefine->where(['id' => $id])->set(['status' => 0])->update();
    }

    function restoreServiceDefine($id){
        return $this->serviceDefine->where(['id' => $id])->set(['status' => 1])->update();
    }
}