<?php
namespace App\Services;

use App\Models\HistoryImportModel;

class HistoryService extends BaseService{
    public function __construct(){
        $this->history = new HistoryImportModel();
    }
    /**
     * History Type:
     *      1: Import KQXN khách lẻ
     *      2: Import KQXN khách đoàn
     *      3: Import Kết quả Test COVID
     *      4: Import KQXN Medelab
     *      5: Import Tài khoản bệnh nhân
     */

    public function addHistoryImport($data){
        return $this->history->save($data);
    }

    public function data_history_import($type){
        if ($type){
            $data = $this->history
                ->join('users2', 'history_import.user_id = users2.id', 'LEFT')
                ->select('history_import.*, users2.fullname')
                ->where(['type' => $type])
                ->orderBy('history_import.id', 'DESC')
                ->findAll();
        } else {
            $data = $this->history
                ->join('users2', 'history_import.user_id = users2.id', 'LEFT')
                ->select('history_import.*, users2.fullname')
                ->orderBy('history_import.id', 'DESC')
                ->findAll();
        }

        if ($data) {
            return $data;
        } else {
            return false;
        }
    }

    public function data_import_detail($id){
        $data = $this->history->where(['id' => $id])->first();
        if ($data) {
            return $data;
        } else {
            return false;
        }
    } 

}