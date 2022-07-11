<?php
namespace App\Services;

require_once 'TechAPI/bootstrap.php';

use App\Models\CovidTestModel;

use App\Services\SettingService;

class ResultService extends BaseService{

    public function __construct(){
        $this->covid                = new CovidTestModel();
        $this->settingService       = new SettingService();
        $this->db = \Config\Database::connect();
    }

    public function get_covidTestResult($phone_number){
        $result = $this->covid->where(['phone_number' => $phone_number, 'status' => 1])->orderBy('date','DESC')->findAll();
    
        $kqTest = array();
        if($result){
            foreach($result as $k => $re){
                $fileResult = '';
                $ext = '';
                if($re['file_result'] != null && $re['file_result'] != ''){
                    $r = json_decode($re['file_result'], true);
                    
                    if($r != []){
                        $fileResult = base_url().'/public/TestCovidResult'.'/'.$r['fileResult'];
                        $ext = $r['ext'];
                    }
                }
                
                $rr = array(
                    'date'          => date('d-m-Y', strtotime($re['date'])),
                    'title'         => $re['type'].': '.$re['result'],
                    'fileResult'    => $fileResult,
                    'ext'           => $ext
                );
                array_push($kqTest, $rr);
            }
            if($kqTest != []){
                return $kqTest;
            }else{
                return false;
            }
        }else
            return false;
    }

    public function updateResult($data){
        return $this->covid->set(['file_result' => $data['file_result']])->where(['id' => $data['id']])->update();
    }

    public function getTestInfo($id){
        return $this->covid->where(['id' => $id])->first();
    }
}