<?php

namespace App\Models;

use CodeIgniter\Model;

class ResultMedelabModel extends Model{
    protected $table = 'result_medelab';
    protected $primaryKey = 'annual_checkup_id';
    protected $returnType     = 'array';
    protected $allowedFields = ['user_id', 'phone_number','company_id','employee_id','full_name','gender','birthdate','Government_id','department','Occupation','Hospital','examination_date','examination_report'];

}