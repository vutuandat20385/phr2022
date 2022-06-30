<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupAnnualCheckupModel extends Model {
    protected $table = 'group_annual_checkup';
    protected $primaryKey = 'annual_checkup_id';
    protected $returnType     = 'array';
    protected $allowedFields = ['user_id', 'phone_number','company_id','employee_id','full_name','gender','birthdate','Government_id','department','Occupation','Hospital', 'hospital_id','examination_date','examination_report','status'];

}