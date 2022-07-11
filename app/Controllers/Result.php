<?php

namespace App\Controllers;

require 'vendor/autoload.php';

use App\Services\ResultService;

class Result extends BaseController {

	public function __construct(){
		$this->result = new ResultService();
	}

	public function index() {
		return true;
	}

    public function get_covidTestResult(){
        $phone_number = $this->request->getVar('phone_number');
        $result = $this->result->get_covidTestResult($phone_number);
        if($result == false){
            return json_encode(array(
                'msg'   => 'fail',
                'list'  => []
            ));
        }else{
            return json_encode(array(
                'msg'   => 'success',
                'list'  => $result
            ));
            
        }
        
    }

}
