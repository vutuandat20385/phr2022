<?php

namespace App\Controllers;

use App\Services\HistoryService;

class History extends BaseController {

    public function __construct(){
        $this->history = new HistoryService(); 

	}

    public function historyPHRimport($type) {
        $data['user'] 	= session()->get('user');
        $data['panelTitle'] = 'Lịch sử Import';
        switch ($type) {
            case 'kham-le'              : $type = 1; break;
            case 'kham-doan'            : $type = 2; break;
            case 'test-covid'           : $type = 3; break;
            case 'medelab'              : $type = 4; break;
            case 'tai-khoan-benh-nhan'  : $type = 5; break;
        }
        $data['phr'] = $this->history->data_history_import($type);

        $data = $this->getAfterLoginLayout($data, 'Lịch sử Import bệnh án', 'AfterLogin/pages/history/history_import');
        return view('AfterLogin/main', $data);
	}

    public function singlePHRimportResult($type, $result) {
        $data['user'] 	= session()->get('user');
        $data['panelTitle'] = 'Lịch sử Import';
        switch ($type) {
            case 'kham-le'              : $type = 1; break;
            case 'kham-doan'            : $type = 2; break;
            case 'test-covid'           : $type = 3; break;
            case 'medelab'              : $type = 4; break;
            case 'tai-khoan-benh-nhan'  : $type = 5; break;
        }
        $data['phr'] = $this->history->data_history_import($type);
        $data['history_result'] = $result;
        $data = $this->getAfterLoginLayout($data, 'Lịch sử Import bệnh án', 'AfterLogin/pages/history/history_import');
        return view('AfterLogin/main', $data);
	}

    public function detail_import($id) {
        $data['user'] 	= session()->get('user');
        $data['panelTitle'] = 'Chi tiết Import';
        $rs_import = $this->history->data_import_detail($id);
        $data['data'] = json_decode($rs_import['report'], true);

        $data 			= $this->getAfterLoginLayout($data, 'Lịch sử Import bệnh án', 'AfterLogin/pages/history/history_import_detail');
        return view('AfterLogin/main', $data);
    }


}
